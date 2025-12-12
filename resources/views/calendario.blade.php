@extends('layouts.app')

@section('title', 'Calendario')

@section('content')
<style>
:root{
    --uv-rojo: #cd1f32;
    --uv-rojo-dark: #b31a2a;
    --uv-azul: #1e40af;
}
.calendar-page{ margin-top: 0; }
.calendar-frame{ max-width: 1100px; margin: 0 auto; background: transparent; }
#calendar{ min-height: 760px; }

/* Toolbar */
.fc .fc-toolbar.fc-header-toolbar{
    background: var(--uv-rojo);
    color: #fff;
    border-radius: 10px 10px 0 0;
    padding: 10px 14px;
    margin-bottom: 0;
}
.fc .fc-toolbar-title{ color:#fff; font-weight:700; }
.fc .fc-button-primary{ background:transparent; border:0; color:#fff; box-shadow:none; }
.fc .fc-button-primary:not(:disabled):hover{ background:rgba(255,255,255,.15); }
.fc .fc-scrollgrid{
    border-radius:0 0 10px 10px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

/* Eventos en month (dayGrid) */
.fc .fc-daygrid-event{
    border:1px solid var(--uv-rojo-dark);
    background:var(--uv-rojo);
    color:#fff;
    border-radius:999px;
    padding:2px 10px;
    line-height:1.25;
    box-shadow:0 1px 2px rgba(0,0,0,.12);
    transition:transform .06s ease, box-shadow .12s ease, filter .12s ease;
}
.fc .fc-daygrid-event .fc-event-title{
    position:relative; padding-left:14px;
}
.fc .fc-daygrid-event .fc-event-title:before{
    content:''; position:absolute; left:0; top:50%; transform:translateY(-50%);
    width:7px; height:7px; border-radius:50%;
    background:var(--uv-azul);
    box-shadow:0 0 0 1px rgba(255,255,255,.8);
}
.fc .fc-daygrid-day-events .fc-daygrid-event-harness{ margin-top:4px; }
.fc .fc-daygrid-event:hover,
.fc .fc-timegrid-event:hover{
    filter:brightness(1.05);
    box-shadow:0 0 0 2px rgba(255,255,255,.75) inset, 0 2px 6px rgba(0,0,0,.18);
    transform:translateY(-1px);
}

/* Semana / Día */
.fc .fc-timegrid-event{
    border-radius:10px;
    overflow:hidden;
    border:1px solid rgba(0,0,0,.08);
    background:#3b82f6;
    color:#fff;
    box-shadow:0 0 0 2px #fff inset, 0 1px 2px rgba(0,0,0,.12);
}
.fc .fc-timegrid-event .fc-event-main{ padding:6px 8px; }
.fc .fc-timegrid-event .fc-event-title{
    display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; overflow:hidden;
}

/* Popover */
.fc-popover{ border-radius:.6rem; box-shadow:0 .75rem 1.5rem rgba(0,0,0,.18); }

/* Highlight temporal opcional */
.fc-event-clicked{ outline:2px solid #fff; box-shadow:0 0 0 2px var(--uv-rojo) inset; }
</style>

<div class="calendar-page" role="region" aria-label="Calendario institucional de eventos">
  <div class="calendar-frame">
    <div id="calendar"></div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/es.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    themeSystem: 'bootstrap5',
    locale: 'es',
    timeZone: 'local', // Ajusta si usas otra zona
    initialView: 'dayGridMonth',
    buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    dayMaxEventRows: true,
    views: { dayGridMonth: { dayMaxEventRows: 3 } },
    moreLinkClick: 'popover',
    slotEventOverlap: true,
    nowIndicator: true,
    // Mantén tu endpoint (si prefieres route: usa route('eventos.obtener') si existe)
    events: '/obtener-eventos',

    eventClick: function(info){
      info.jsEvent.preventDefault();
      // Highlight temporal (quitamos anterior)
      document.querySelectorAll('.fc-event-clicked').forEach(e => e.classList.remove('fc-event-clicked'));
      info.el.classList.add('fc-event-clicked');

      const e = info.event;
      const start = e.start, end = e.end;
      const fFecha = start ? start.toLocaleDateString(undefined,{year:'numeric',month:'long',day:'numeric'}) : '';
      const fHoraI = start ? start.toLocaleTimeString(undefined,{hour:'numeric',minute:'numeric',hour12:true}) : '';
      const fHoraF = end   ? end.toLocaleTimeString(undefined,{hour:'numeric',minute:'numeric',hour12:true})   : '';
      const { lugar = '', espacio = '' } = e.extendedProps || {};
      Swal.fire({
        title: e.title || 'Evento',
        html: `
          ${fFecha ? '<p>Fecha: '+fFecha+'</p>' : ''}
          ${fHoraI ? '<p>Inicio: '+fHoraI+'</p>' : ''}
          ${fHoraF ? '<p>Fin: '+fHoraF+'</p>' : ''}
          ${lugar   ? '<p>Lugar: '+lugar+'</p>' : ''}
          ${espacio ? '<p>Espacio: '+espacio+'</p>' : ''}
        `,
        icon: 'info',
        confirmButtonText: 'Ok'
      }).then(() => {
        // Remover highlight al cerrar (opcional)
        info.el.classList.remove('fc-event-clicked');
      });
    }
  });

  calendar.render();

  function resizeCalendar() {
    const nav = document.querySelector('.navbar');
    const navH = nav ? Math.ceil(nav.getBoundingClientRect().height) : 74;
    const available = window.innerHeight - navH - 80;
    calendar.setOption('height', Math.max(available, 640));
  }
  resizeCalendar();
  window.addEventListener('resize', resizeCalendar);
});
</script>
@endpush