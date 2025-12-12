<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/estiloUnivalle.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://kit.fontawesome.com/71e9100085.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #ddd;
        }
        .navbar-custom {
            background-color: #cd1f32;
        }

        .navbar-brand img {
            max-height: 50px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            margin-left: 10px;
        }


        .table {
            background-color: #fff;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .form-select {
            width: auto;
            display: inline-block;
            vertical-align: middle;
        }

        .btn-action {
            margin-left: 10px;
        }

        .hidden {
            display: none;
        }
    
        .left-aligned-alert {
            text-align: left !important;
        }

        .justified-alert {
            text-align: justify; /* Justificar el contenido */
        }


        .star-rating {
            font-size: 24px;
            cursor: pointer;
        }

        .star-rating .star {
            color: #ccc;
        }

        .star-rating .star.checked {
            color: #ffc107; /* Color amarillo para las estrellas seleccionadas */
        }

        .note-header {
            font-weight: bold; /* Texto en negrita */
            text-align: left; /* Justificado a la izquierda */
            margin-bottom: 5px; /* Espacio inferior para separación */
        }
        
        .note-content {
            margin-bottom: 10px; /* Espacio inferior para separación */
        }

        .note-date {
            font-size: 0.8rem; /* Tamaño de fuente más pequeño para la fecha */
        }

        .note {
            margin-bottom: 15px; /* Espacio inferior general entre anotaciones */
        }
    </style>



</head>
<body>

<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="#">
                    <img src="{{ asset('imagenes/header_logo.jpg')}}" alt="Logo de la universidad" style="max-height: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
        
                    <a href="{{ route('dashboard') }}" class="btn btn-light custom-button" style="background-color: #ffffff; color: #000000; margin-right: 10px;">Inicio</a> 
                    
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownEventos" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #ffffff; color: #000000;">
                            Eventos
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownEventos" style="background-color: #ffffff;">
                            <li><a class="dropdown-item" href="{{ route('crearEvento') }}" style="color: #000000;">Crear Evento</a></li>
                            @if(auth()->user()->hasRole('CooAdmin') || auth()->user()->hasRole('AuxAdmin') || auth()->user()->hasRole('Administrativo'))
                            <li><a class="dropdown-item" href="{{ route('consultarEventos') }}" style="color: #000000;">Consultar tus eventos</a></li>
                            @endif
                        </ul>
                    </div>
                    <!--
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMonitoria" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #ffffff; color: #000000;">
                            Monitorias
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMonitoria" style="background-color: #ffffff;">
                            @if(auth()->user()->hasRole('CooAdmin')|| auth()->user()->hasRole('AuxAdmin'))
                            <li><a class="dropdown-item" href="{{ route('periodos.crear') }}" style="color: #000000;">Consultar Periodo Academico</a></li>
                            <li><a class="dropdown-item" href="{{ route('convocatoria.index') }}" style="color: #000000;">Crear Convocatoria</a></li>
                            @endif
                            @if(auth()->user()->hasRole('CooAdmin') || auth()->user()->hasRole('Profesor'))
                            <li><a class="dropdown-item" href="{{ route('monitoria.index') }}" style="color: #000000;">Gestionar Monitorias</a></li>
                            @endif
                            @if(auth()->user()->hasRole('CooAdmin')|| auth()->user()->hasRole('AuxAdmin'))
                            <li><a class="dropdown-item" href="{{ route('postulados.index') }}" style="color: #000000;">Ver Postulados</a></li>
                            @endif
                        </ul>
                    </div>-->
                    @if(auth()->user()->hasRole('CooAdmin') || auth()->user()->hasRole('AuxAdmin') || auth()->user()->hasRole('Profesor') || auth()->user()->hasRole('Administrativo'))
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownNovedades" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #ffffff; color: #000000;">
                                Mantenimiento
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownNovedades" style="background-color: #ffffff;">
                                <li><a class="dropdown-item" href="{{ route('novedades.index') }}" style="color: #000000;">Gestionar Novedades</a></li>
                                @if(auth()->user()->hasRole('CooAdmin') || auth()->user()->hasRole('AuxAdmin'))
                                    <li><a class="dropdown-item" href="{{ route('mantenimiento.index') }}" style="color: #000000;">Plan de Mantenimiento Preventivo</a></li>
                                    <li><a class="dropdown-item" href="{{ route('evidencias-mantenimiento.index') }}" style="color: #000000;">Evidencias de Mantenimiento</a></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    <a href="{{ route('calendario') }}" class="btn btn-light custom-button" style="background-color: #ffffff; color: #000000; margin-left: 10px;">
                        Calendario <i class="fa-regular fa-calendar"></i>
                    </a>        
                </div>
                <div class="d-flex">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-gear "></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Cerrar sesión  <i class="fa-solid fa-right-from-bracket"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">PDF del Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfFrame" style="width: 100%; height: 600px;" frameborder="0"></iframe>
            </div>
            </div>
        </div>
    </div>   
    <!-- Tu contenido HTML aquí --> 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-rating .star');

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    document.getElementById('puntaje').value = rating;

                    // Remover la clase 'checked' de todas las estrellas
                    stars.forEach(s => s.classList.remove('checked'));

                    // Agregar la clase 'checked' a las estrellas seleccionadas hasta la calificación actual
                    for (let i = 0; i < rating; i++) {
                        stars[i].classList.add('checked');
                    }
                });
            });
        });
        $(document).ready(function() {
            $('.table').DataTable({
                "pageLength": 10,
                "order": [[1, 'desc']],
                language: {
                    "decimal": "",
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                    "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                    "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ Entradas",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
        });
    </script>
    
    <div class="container mt-5">
        <div class="mb-3">
            <a href="{{ route('exportar.eventos') }}" class="btn btn-primary tt" data-bs-placement="top" title="Descargar Listado de eventos"><i class="fas fa-download"></i></a>
        </div>
        <table id="table" class="table table-bordered " style="width:100%">
        
            <thead class=" bg-primary thead-dark">
            
                <tr>
                    <th scope="col">Nombre del evento</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Hora inicio</th>
                    <th scope="col">Hora finalización</th>
                    <th scope="col">Lugar</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventos as $evento)
                <tr>
                    <td>{{ $evento->nombreEvento }}</td>
                    <td>{{ $evento->fechaRealizacion }}</td>
                    <td>{{ $evento->horaInicio }}</td>
                    <td>{{ $evento->horaFin }}</td>
                    <td>{{ $evento->nombreLugar }}</td>
                    <td>
                        @if(auth()->user()->hasRole('CooAdmin') || auth()->user()->hasRole('AuxAdmin'))
                            <!-- Formulario para actualizar el estado solo para CooAdmin y AuxAdmin -->
                            <form action="{{ route('actualizar_estado', ['eventoId' => $evento->id]) }}" method="POST" id="form-{{ $evento->id }}">
                                @csrf
                                @method('PUT')
                                <select class="form-select" name="estado" onchange="toggleButton({{ $evento->id }})">
                                    <option value="Creado" {{ $evento->estado == 'Creado' ? 'selected' : '' }}>Creado</option>
                                    <option value="Aceptado" {{ $evento->estado == 'Aceptado' ? 'selected' : '' }}>Aceptado</option>
                                    <option value="Rechazado" {{ $evento->estado == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    <option value="Cancelado" {{ $evento->estado == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    <option value="Cerrado" {{ $evento->estado == 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
                                </select>
                                <span class="tt" data-bs-placement="top" title="Actualizar">
                                    <button type="submit" class="btn btn-primary btn-action hidden" id="button-{{ $evento->id }}"><i class="fas fa-save"></i></button>
                                </span>
                            </form>
                        @else
                            {{ $evento->estado }}
                        @endif
                    </td>
                        
                    <td>
                        <span class="tt" data-bs-placement="top" title="Ver">
                            <button type="button" class="btn btn-primary view-event" data-event-id="{{ $evento->id }}"><i class="fas fa-eye"></i></button>
                        </span>

                        <span class="tt" data-bs-placement="top" title="Editar">
                            <a class="btn btn-warning" onclick="manejarEditarEvento('{{ $evento->id }}', '{{ $evento->fechaRealizacion }}', '{{ $evento->horaInicio }}', '{{ $evento->estado }}' )"><i class="fas fa-edit"></i></a>
                        </span>
                        <form action="{{ route('borrarEvento', ['id' => $evento->id]) }}" method="POST" id="form-delete-{{ $evento->id }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <span class="tt" data-bs-placement="top" title="Eliminar">
                                <button type="button" class="btn btn-danger delete-event" data-event-id="{{ $evento->id }}" data-event-estado="{{ $evento->estado }}" style="display: inline;"><i class="fas fa-trash"></i></button>
                            </span>    
                        </form>
                        <span class="tt" data-bs-placement="top" title="Ver PDF">
                            <button type="button" class="btn btn-secondary view-pdf" data-event-id="{{ $evento->id }}"><i class="fas fa-file-pdf"></i></button>
                        </span>
                        @if($evento->estado != 'Creado')
                        <span class="tt" data-bs-placement="top" title="Historial Anotaciones">
                            <button type="button" class="btn btn-info view-notes" data-event-id="{{ $evento->id }}"><i class="fas fa-sticky-note"></i></button>                        
                        </span>
                        @endif
                        @if($evento->estado == 'Cerrado')
                            <span class="tt" data-bs-placement="top" title="Calificar">
                                <button type="button" class="btn btn-success rate-event" data-event-id="{{ $evento->id }}"><i class="fas fa-star"></i></button>
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Califica el evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ratingForm">
                    @csrf
                    <input type="hidden" id="eventoId" name="evento_id" value="">
                    <div class="mb-3">
                        <p>Tu calificación es fundamental para nosotros. Nos ayuda a mejorar y asegurar una gestión administrativa efectiva. ¡Gracias por tu colaboración! 🤓👆</p>
                        <label class="form-label">Calificación</label>
                        
                        <div id="starRating" class="star-rating">
                            <i class="far fa-star star" data-rating="1"></i>
                            <i class="far fa-star star" data-rating="2"></i>
                            <i class="far fa-star star" data-rating="3"></i>
                            <i class="far fa-star star" data-rating="4"></i>
                            <i class="far fa-star star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="puntaje" id="puntaje" value="1"> <!-- Este campo oculto almacenará el valor seleccionado -->
                    </div>
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentarios</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Si tienes alguna opinión, comentario, sugerencia. ¡Compártela!"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" id="submitRating">Enviar Calificación</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">Historial de anotaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notesList"></div>
                <form action="{{ route('anotacion.agregar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="evento_id" name="evento_id">
                    <div class="mb-3">
                        <label for="nota" class="form-label">Nueva anotación</label>
                        <textarea class="form-control" id="nota" name="contenido" placeholder="Ingrese una anotacion sobre el evento" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Cargue por aca sus evidencias</label>
                        <input type="file" class="form-control" id="archivo" name="archivo">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver imagen en grande -->
<div class="modal fade" id="largeImageModal" tabindex="-1" aria-labelledby="largeImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="largeImageModalLabel">Ver imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="img-fluid" alt="Imagen de anotación">
            </div>
        </div>
    </div>
</div>


    
<script>
$(document).ready(function() {
    // Listener para abrir la ventana modal de anotaciones
    $('.view-notes').click(function() {
        const eventId = $(this).data('event-id');
        // Llenar el input oculto con el id del evento
        $('#evento_id').val(eventId);
        // Llamar a la función para cargar las anotaciones
        loadNotes(eventId);
        // Mostrar la ventana modal
        $('#notesModal').modal('show');
    });

    // Función para cargar las anotaciones existentes
    function loadNotes(eventId) {
        $.ajax({
            url: '/anotaciones/' + eventId, // Ruta que devuelve las anotaciones para el evento
            type: 'GET',
            success: function(response) {
                $('#notesList').empty(); // Limpiar la lista de anotaciones
                // Iterar sobre las anotaciones y agregarlas a la lista
                response.forEach(function(nota) {
                    const formattedDateTime = new Date(nota.fecha).toLocaleString(); 
                    // Construir el HTML para mostrar la anotación y el nombre del usuario
                    var html = `<div class="note">
                                    <div class="note-header">${nota.usuario.name}</div>
                                    <div class="alert alert-secondary">${nota.contenido} <small class="text-muted">${formattedDateTime}</small></div>`;
                    if (nota.archivo_url) {
                        html += `<div class="note-image">
                                    <img src="${nota.archivo_url}" class="img-fluid view-large" alt="Anotación Imagen" data-image-url="${nota.archivo_url}">
                                    <br>
                                    <a href="${nota.archivo_url}" download class="btn btn-sm btn-primary mt-2 tt" data-bs-placement="top" title="Descargar"><i class="fa-solid fa-circle-down"></i></a>
                                </div>`;
                    }
                    html += `</div>`;
                    $('#notesList').append(html);
                });

                // Listener para hacer clic en la imagen para verla en grande
                $('.view-large').click(function() {
                    const imageUrl = $(this).data('image-url');
                    $('#largeImageModal .modal-body img').attr('src', imageUrl);
                    $('#largeImageModal').modal('show');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error cargando anotaciones:', error);
            }
        });
    }
});
</script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar el modal de calificación al hacer clic en el botón de calificación
    document.querySelectorAll('.rate-event').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');

            // Hacer una llamada AJAX para verificar si el evento ya ha sido calificado
            fetch(`/verificar-calificacion/${eventId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.calificado) {
                        // Si ya está calificado, mostrar la calificación existente en una alerta
                        let comentario = data.comentario ? `<br>Comentario: ${data.comentario}` : '';
                        Swal.fire({
                            icon: 'info',
                            title: 'Calificación existente',
                            html: `El evento ya ha sido calificado. <br>Puntuación: ${data.calificacion} <i class="far fa-star star"></i>.${comentario}`,
                            confirmButtonColor: '#28a745'
                        });
                    } else {
                        // Si no está calificado, abrir el modal
                        document.getElementById('eventoId').value = eventId; // Establecer el ID del evento en el formulario
                        const modal = new bootstrap.Modal(document.getElementById('ratingModal'));
                        modal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });

    // Enviar la calificación al servidor
    document.getElementById('submitRating').addEventListener('click', function() {
        const form = document.getElementById('ratingForm');
        const formData = new FormData(form);

        fetch('/calificar-evento', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Calificación enviada',
                    text: 'La calificación del evento se ha enviado con éxito.',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload(); // Recargar la página
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al enviar la calificación.',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al enviar la calificación.',
                confirmButtonColor: '#dc3545'
            });
        });
    });
});

</script>
    <script>

        const tooltips = document.querySelectorAll('.tt');
        tooltips.forEach(t => {
            new bootstrap.Tooltip(t)
        })
    </script>
    <script>
    function toggleButton(eventId) {
        var select = document.getElementById('form-' + eventId).querySelector('select');
        var button = document.getElementById('button-' + eventId);
        if (select.value !== '') {
            button.classList.remove('hidden');
        } else {
            button.classList.add('hidden');
        }
    }

    document.addEventListener('submit', function(event) {
        if (event.target && event.target.matches('form')) {
            event.preventDefault(); // Evita que el formulario se envíe automáticamente
            var form = event.target;
            var formData = new FormData(form);
            fetch(form.action, {
                method: form.method,
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'El estado del evento fue actualizado exitosamente.',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        
                        location.reload();
                    });
                } else {
                    throw new Error('Error en la respuesta del servidor.');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Ocurrió un error al intentar actualizar el estado del evento.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agrega un listener a los botones de eliminar evento
        var deleteButtons = document.querySelectorAll('.delete-event');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var eventId = this.getAttribute('data-event-id');
                var eventEstado = this.getAttribute('data-event-estado'); // Agrega esta línea para obtener el estado del evento
                // Verificar si el evento está en estado "Cancelado" o "Rechazado"
                if (eventEstado === 'Cancelado' || eventEstado === 'Rechazado') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Si el evento es eliminado, tendrá que ser ingresado nuevamente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Envía el formulario de eliminación
                            document.getElementById('form-delete-' + eventId).submit();
                        }
                    });
                } else {
                    // Si el evento no está en estado "Cancelado" o "Rechazado", muestra un mensaje de error
                    Swal.fire({
                        title: 'Error',
                        text: 'Solo se pueden eliminar eventos en estado "Cancelado" o "Rechazado".',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });
    });
</script>

<script>
    // Función para manejar el clic en el botón de editar evento
    function manejarEditarEvento(eventoId, eventoFecha, eventoHora, estadoEvento) {
        // Convertir la fecha del evento a un objeto Date
        var fechaEvento = new Date(eventoFecha + 'T' + eventoHora);

        // Obtener la fecha y hora actual
        var fechaHoraActual = new Date();

        // Verificar si el estado del evento es "Aceptado"
        if (estadoEvento === "Aceptado") {
            // Mostrar una alerta indicando que el evento ya fue aceptado y no se pueden realizar cambios
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Una vez el evento es aceptado no se pueden realizar cambios.',
                confirmButtonColor: '#3085d6',
            });
        } else {
            // Calcular la diferencia en milisegundos
            var diferenciaMilisegundos = fechaEvento - fechaHoraActual;

            // Convertir la diferencia a horas
            var diferenciaHoras = diferenciaMilisegundos / (1000 * 60 * 60);

            // Verificar si faltan menos de 48 horas para el evento
            if (diferenciaHoras < 72) {
                // Mostrar una alerta indicando que no se puede editar el evento porque faltan menos de 48 horas
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se puede editar el evento porque faltan menos de 72 horas para su realización.',
                    confirmButtonColor: '#3085d6',
                });
            } else {
                // Redireccionar al formulario de edición del evento
                window.location.href = "/editarEvento/" + eventoId;
            }
        }
    }

</script>


<script>
function obtenerInformacionEvento(eventoId) {
    $.ajax({
        url: '/ver-evento/' + eventoId, // Ruta a tu método en el controlador que devuelve la información del evento
        type: 'GET',
        success: function(response) {
            // Llamar a la función para mostrar la alerta con la información del evento
            mostrarAlertaInformacionEvento(response);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

// Función para mostrar la información del evento en una alerta de SweetAlert
function mostrarAlertaInformacionEvento(informacion) {
    // Construir el contenido de la alerta de SweetAlert
    var contenidoAlerta = '<div style="text-align: justify;">' +
        '<p><strong>Nombre del evento:</strong> ' + informacion.evento.nombreEvento + '</p>' +
        '<p><strong>Propósito del evento:</strong> ' + informacion.evento.propositoEvento + '</p>' +
        '<p><strong>Fecha:</strong> ' + informacion.evento.fechaRealizacion + '</p>' +
        '<p><strong>Hora de inicio:</strong> ' + informacion.evento.horaInicio + '</p>' +
        '<p><strong>Hora de fin:</strong> ' + informacion.evento.horaFin + '</p>';

    if (informacion.programaDependencia) { // Ajusta aquí
        contenidoAlerta += '<p><strong>Programa o dependencia:</strong> ' + informacion.programaDependencia + '</p>'; // Ajusta aquí
    }
    
    if (informacion.programaDependenciaSecundario) {
        contenidoAlerta += '<p><strong>Programa o dependencia secundario:</strong> ' + informacion.programaDependenciaSecundario + '</p>';
    }
    
    if (informacion.programaDependenciaTerciaria) {
        contenidoAlerta += '<p><strong>Programa o dependencia terciaria:</strong> ' + informacion.programaDependenciaTerciaria + '</p>';
    }

    // Mostrar el lugar si está definido
    if (informacion.lugar) {
        contenidoAlerta += '<p><strong>Lugar:</strong> ' + informacion.lugar + '</p>';
    }

    // Mostrar el espacio si está definido
    if (informacion.espacio) {
        contenidoAlerta += '<p><strong>Espacio:</strong> ' + informacion.espacio + '</p>';
    }

    // Mostrar detalles del evento
    if (informacion.detallesEvento) {
        contenidoAlerta += '<h4>Detalles del evento</h4>';
        contenidoAlerta += '<ul>'; // Agregar una lista desordenada para los detalles
        // Iterar sobre los detalles del evento y agregar cada detalle como un ítem de lista
        for (var detalle in informacion.detallesEvento) {
            // Excluir los campos "created_at" y "updated_at"
            if (detalle !== 'created_at' && detalle !== 'updated_at' && detalle !== 'id' && detalle !== 'evento') {
                // Solo mostrar los detalles que no son nulos ni vacíos (0)
                if (informacion.detallesEvento[detalle] !== null && informacion.detallesEvento[detalle] !== 0) {
                    // No mostrar el valor si el detalle tiene un icono (chulo)
                    var valorDetalle = informacion.detallesEvento[detalle] === 1 ? '' : informacion.detallesEvento[detalle];
                    // Agregar un icono (chulo) para los detalles marcados (1)
                    var icono = informacion.detallesEvento[detalle] === 1 ? '<i class="fas fa-check-circle"></i> ' : '';
                    contenidoAlerta += '<li><strong>' + detalle + ':</strong> ' + icono + valorDetalle + '</li>';
                }
            }
        }
        contenidoAlerta += '</ul>'; // Cerrar la lista desordenada
    }

    // Mostrar inventario del evento
    if (informacion.inventarioEvento.length > 0) {
        contenidoAlerta += '<h4>Inventario del evento</h4>';
        contenidoAlerta += '<ul>'; // Agregar una lista desordenada para los elementos de inventario
        // Iterar sobre el inventario del evento y agregar cada elemento como un ítem de lista
        informacion.inventarioEvento.forEach(function(item) {
            contenidoAlerta += '<li><strong>' + item.tipo + ':</strong> ' + item.cantidad + '</li>';
        });
        contenidoAlerta += '</ul> </div>'; // Cerrar la lista desordenada
    }

    // Mostrar la alerta de SweetAlert con la información del evento
    Swal.fire({
        title: 'Informacion del evento',
        html: contenidoAlerta,
        icon: 'info',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Cerrar'
        
    });
}

// Llamar a la función para obtener la información del evento cuando la página se cargue
$(document).ready(function() {
    // Agregar un listener a los botones de ver evento
    $('.view-event').click(function() {
        // Obtener el ID del evento desde el atributo data-event-id del botón
        var eventoId = $(this).data('event-id');
        // Llamar a la función para obtener la información del evento
        obtenerInformacionEvento(eventoId);
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar un event listener al botón "Ver PDF"
        document.querySelectorAll('.view-pdf').forEach(button => {
            button.addEventListener('click', function() {
                // Obtener el ID del evento del atributo data
                const eventId = this.getAttribute('data-event-id');
                // Obtener el iframe donde se cargará el PDF
                const pdfFrame = document.getElementById('pdfFrame');
                // Construir la URL para generar el PDF
                const pdfUrl = `/generate-pdf/${eventId}`;
                // Establecer la URL del iframe para cargar el PDF
                pdfFrame.src = pdfUrl;
                // Abrir el modal
                const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
                modal.show();
            });
        });
    });
</script>

</body>
</html>
