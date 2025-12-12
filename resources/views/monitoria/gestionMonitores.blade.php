@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold text-primary mb-4"><i class="fa-solid fa-users-gear me-2"></i>Consultar Monitores</h2>
    
    <!-- Mensaje destacado -->
    <div class="alert alert-info d-flex align-items-center mb-4" style="background:rgba(13,110,253,0.08);border-left:5px solid #0d6efd;">
        <i class="fa-solid fa-circle-info me-2"></i>
        <div>
            <strong>Importante:</strong>
            <span class="fw-semibold">
                En cada columna de mes verás una <span class="text-primary">propuesta de horas</span> calculada automáticamente según las fechas y horas semanales del monitor.
            </span>
            <br>
            <span class="fw-semibold">
                <span class="text-danger">Debes revisar, completar y guardar las horas por mes de cada monitor.</span>
                Estas horas serán las que se reflejarán en el seguimiento mensual y en los reportes.
            </span>
            <br>
            <span class="text-secondary">Recuerda que puedes definir fechas generales y luego ajustar individualmente si lo requieres.</span>
        </div>
    </div>

    <!-- Leyenda de colores -->
    <div class="alert alert-light border mb-4">
        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-3"><i class="fa-solid fa-palette me-2"></i>Leyenda de Colores:</h6>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="me-2" style="width: 20px; height: 20px; background-color: #e8f5e8; border: 2px solid #28a745; border-radius: 4px;"></div>
                        <small><strong>Verde:</strong> Campo diligenciado</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-2" style="width: 20px; height: 20px; background-color: #ffeaea; border: 2px solid #dc3545; border-radius: 4px;"></div>
                        <small><strong>Rojo:</strong> Campo pendiente de diligenciar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fechas generales -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="fechaGeneralVinculacion" class="form-label">Fecha Vinculación General</label>
            <input type="date" id="fechaGeneralVinculacion" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="fechaGeneralCulminacion" class="form-label">Fecha Culminación General</label>
            <input type="date" id="fechaGeneralCulminacion" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" class="btn btn-outline-primary" id="btnAplicarFechas">
                <i class="fa-solid fa-calendar-check"></i> Aplicar a todos
            </button>
        </div>
    </div>



    <form id="formGestionMonitores">
        <div class="table-responsive">
                            <table class="table table-bordered align-middle bg-white" id="tablaGestionMonitores">
                    <thead class="table-light sticky-header" id="theadGestionMonitores">
                        <tr id="headerRow">
                            <th class="sticky-col monitor-col">Monitoría</th>
                            <th class="sticky-col-2 monitor-col">Monitor</th>
                        <th class="horas-col">H/Sem</th>
                        <th class="horas-col">H/Tot</th>
                        <th class="fecha-col">F.Vinc</th>
                        <th class="fecha-col">F.Culm</th>
                        <!-- Meses dinámicos -->
                        <th class="historial-col">Historial</th>
                    </tr>
                </thead>
                <tbody id="tbodyGestionMonitores">
                    <tr><td colspan="12" class="text-center">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-info" onclick="imprimirTabla()">
                    <i class="fa-solid fa-print"></i> Imprimir Tabla
                </button>
                <button type="button" class="btn btn-outline-success" onclick="descargarHistorico()">
                    <i class="fa-solid fa-download"></i> Descargar Histórico Completo
                </button>
                                                         <a href="{{ route('lista-admitidos.index') }}" class="btn btn-outline-warning">
                            <i class="fa-solid fa-users"></i> Lista de Admitidos
                       </a>
                 
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<!-- Modal para mostrar el historial de documentos -->
<div class="modal fade" id="modalHistorialDocumentos" tabindex="-1" aria-labelledby="modalHistorialDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalHistorialDocumentosLabel">
                    <i class="fa-solid fa-history me-2"></i>Historial de Documentos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    <div>
                        <strong>Monitor:</strong> <span id="nombreMonitorHistorial"></span>
                        <br>
                        <small>Aquí puedes ver todos los documentos generados para este monitor, incluyendo seguimientos mensuales, asistencias y evaluaciones de desempeño.</small>
                    </div>
                </div>
                
                <div id="contenidoHistorial">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando historial...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables y Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Estilos para mejor visualización de la tabla */
.table-container {
    position: relative;
}



/* Vista Compacta - Columnas más pequeñas */
.vista-compacta .monitor-col { width: 180px; font-size: 13px; }
.vista-compacta .horas-col { width: 65px; font-size: 12px; }
.vista-compacta .fecha-col { width: 115px; font-size: 12px; }
.vista-compacta .mes-col { width: 75px; font-size: 12px; }
.vista-compacta .historial-col { width: 80px; font-size: 12px; }
.vista-compacta input { font-size: 12px; padding: 3px 5px; }



/* Columnas fijas para scroll horizontal */
.sticky-col {
    position: sticky;
    left: 0;
    background: white;
    z-index: 10;
    border-right: 2px solid #dee2e6;
}

.sticky-col-2 {
    position: sticky;
    left: 180px; /* Ajustar según el ancho de la primera columna en vista compacta */
    background: white;
    z-index: 10;
    border-right: 2px solid #dee2e6;
}

.sticky-header {
    position: sticky;
    top: 0;
    z-index: 11;
    background: white;
}




/* Responsive - eliminamos esta sección duplicada */

/* Tooltips para mejor UX */
.tooltip-hover {
    cursor: help;
}

/* Mejorar visibilidad de inputs pequeños */
.table input {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.table input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Estilos específicos para campos de texto largo */
.campo-largo {
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.3;
    cursor: help;
}

/* Control específico para campos largos */
.campo-largo {
    max-width: 170px; /* Un poco menos que el ancho de la columna para padding */
    width: 100%;
    box-sizing: border-box;
}

/* Efectos hover para campos largos */
.campo-largo:hover {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
    transform: scale(1.02);
    transition: all 0.2s ease;
    z-index: 5;
    position: relative;
}

/* Hacer los campos de monitoría y monitor más visibles */
.sticky-col input[type="text"], 
.sticky-col-2 input[type="text"] {
    font-weight: 600;
    color: #2c3e50;
    background-color: #f8f9fa;
}

.monitor-col input[type="text"] {
    font-weight: 600;
    color: #2c3e50;
    background-color: #f8f9fa;
}

/* Modo responsive para móviles */
@media (max-width: 768px) {
    .vista-compacta .monitor-col { width: 140px; font-size: 11px; }
    .vista-compacta .fecha-col { width: 100px; }
    .sticky-col-2 { left: 140px; }
}

/* Colores para diferentes tipos de datos */
.fecha-vinculacion { background-color: #f8f9fa; }
.fecha-culminacion { background-color: #f8f9fa; }

/* Estilos para campos de horas mensuales no diligenciados */
.horas-mensuales:not(:focus):placeholder-shown {
    background-color: #ffeaea !important;
    border-color: #dc3545 !important;
    color: #dc3545 !important;
}

.horas-mensuales:not(:focus):placeholder-shown::placeholder {
    color: #dc3545 !important;
    font-weight: bold;
}

/* Estilos para campos de horas mensuales diligenciados */
.horas-mensuales:not(:placeholder-shown) {
    background-color: #e8f5e8 !important;
    border-color: #28a745 !important;
    color: #155724 !important;
}

/* Estilos para campos de horas mensuales vacíos (sin placeholder) */
.horas-mensuales:not(:focus):invalid {
    background-color: #ffeaea !important;
    border-color: #dc3545 !important;
    color: #dc3545 !important;
}

/* Estados específicos de campos de horas mensuales */
.campo-diligenciado {
    background-color: #e8f5e8 !important;
    border-color: #28a745 !important;
    color: #155724 !important;
    font-weight: 600;
}

.campo-pendiente {
    background-color: #ffeaea !important;
    border-color: #dc3545 !important;
    color: #dc3545 !important;
    font-weight: bold;
}

.campo-pendiente::placeholder {
    color: #dc3545 !important;
    font-weight: bold;
}

.campo-vacio {
    background-color: #ffeaea !important;
    border-color: #dc3545 !important;
    color: #dc3545 !important;
    font-weight: bold;
}

/* Efectos hover para campos pendientes */
.campo-pendiente:hover,
.campo-vacio:hover {
    background-color: #ffd6d6 !important;
    border-color: #c82333 !important;
    transform: scale(1.02);
    transition: all 0.2s ease;
}
</style>

<script>
let mesesDinamicos = [];

function getMesesEntreFechas(fechaInicio, fechaFin) {
    if (!fechaInicio || !fechaFin) return [];
    
    const meses = [];
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    
    if (isNaN(inicio.getTime()) || isNaN(fin.getTime())) return [];
    
    const fechaActual = new Date(inicio.getFullYear(), inicio.getMonth(), 1);
    while (fechaActual <= new Date(fin.getFullYear(), fin.getMonth() + 1, 0)) {
        const nombreMes = fechaActual.toLocaleString('es-ES', { month: 'long' }).toLowerCase();
        meses.push(nombreMes);
        fechaActual.setMonth(fechaActual.getMonth() + 1);
    }
    
    return meses;
}

function calcularHorasMensuales(fechaInicio, fechaFin, horasSemanales) {
    if (!fechaInicio || !fechaFin || !horasSemanales) return {};
    const horasMensuales = {};
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    if (isNaN(inicio.getTime()) || isNaN(fin.getTime())) return {};
    const fechaActual = new Date(inicio.getFullYear(), inicio.getMonth(), 1);
    while (fechaActual <= new Date(fin.getFullYear(), fin.getMonth() + 1, 0)) {
        const nombreMes = fechaActual.toLocaleString('es-ES', { month: 'long' }).toLowerCase();
        const primerDia = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 1);
        const ultimoDia = new Date(fechaActual.getFullYear(), fechaActual.getMonth() + 1, 0);
        const inicioMes = new Date(Math.max(primerDia, inicio));
        const finMes = new Date(Math.min(ultimoDia, fin));
        const diasLaborables = Math.ceil((finMes - inicioMes) / (1000 * 60 * 60 * 24));
        const semanasEnMes = Math.ceil(diasLaborables / 7);
        horasMensuales[nombreMes] = semanasEnMes * horasSemanales;
        fechaActual.setMonth(fechaActual.getMonth() + 1);
    }
    return horasMensuales;
}

// Función para calcular horas totales basadas en fechas
function calcularHorasTotales(fechaInicio, fechaFin, horasSemanales) {
    if (!fechaInicio || !fechaFin || !horasSemanales) return 0;
    
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    
    if (isNaN(inicio.getTime()) || isNaN(fin.getTime())) return 0;
    
    // Calcular días entre fechas
    const diferenciaTiempo = fin.getTime() - inicio.getTime();
    const diasDiferencia = Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));
    
    // Calcular semanas usando la misma lógica del backend
    const semanas = Math.floor(diasDiferencia / 7) + 1;
    return horasSemanales * semanas;
}

function validarHorasMensuales(input) {
    const row = input.closest('tr');
    const horasTotales = parseInt(row.querySelector('input[name*="[horas_totales]"]').value) || 0;
    const camposHoras = row.querySelectorAll('input.horas-mensuales');
    
    // Calcular suma actual de horas mensuales
    let sumaHoras = 0;
    let camposNoDiligenciados = 0;
    
    camposHoras.forEach(campo => {
        const valor = parseInt(campo.value) || 0;
        sumaHoras += valor;
        
        // Contar campos no diligenciados
        if (!campo.value || campo.value.trim() === '') {
            camposNoDiligenciados++;
        }
        
        // Actualizar estado visual del campo
        actualizarEstadoVisualCampo(campo);
    });
    
    // Remover clases de validación anteriores
    camposHoras.forEach(campo => {
        campo.classList.remove('is-valid', 'is-invalid');
    });
    
    // Validar si excede las horas totales
    if (sumaHoras > horasTotales && horasTotales > 0) {
        camposHoras.forEach(campo => {
            campo.classList.add('is-invalid');
        });
        
        // Mostrar mensaje de advertencia
        const mensaje = `⚠️ La suma de horas mensuales (${sumaHoras}) excede las horas totales (${horasTotales})`;
        mostrarMensajeValidacion(row, mensaje, 'warning');
    } else {
        // Remover mensaje de advertencia si existe
        ocultarMensajeValidacion(row);
        
        // Si hay campos no diligenciados, mostrar advertencia
        if (camposNoDiligenciados > 0) {
            const mensaje = `⚠️ Hay ${camposNoDiligenciados} mes(es) sin diligenciar. Los campos en rojo indican que faltan por completar.`;
            mostrarMensajeValidacion(row, mensaje, 'info');
        }
    }
}

// Función para actualizar el estado visual de un campo
function actualizarEstadoVisualCampo(campo) {
    // Remover clases anteriores
    campo.classList.remove('campo-diligenciado', 'campo-pendiente', 'campo-vacio');
    
    if (campo.value && campo.value.trim() !== '') {
        // Campo diligenciado
        campo.classList.add('campo-diligenciado');
    } else if (campo.placeholder && campo.placeholder.trim() !== '') {
        // Campo con placeholder (recomendación)
        campo.classList.add('campo-pendiente');
    } else {
        // Campo completamente vacío
        campo.classList.add('campo-vacio');
    }
}

// Función para inicializar el estado visual de todos los campos
function inicializarEstadosVisuales() {
    const camposHoras = document.querySelectorAll('input.horas-mensuales');
    camposHoras.forEach(campo => {
        actualizarEstadoVisualCampo(campo);
    });
}

function mostrarMensajeValidacion(row, mensaje, tipo = 'warning') {
    // Remover mensaje anterior si existe
    ocultarMensajeValidacion(row);
    
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `alert alert-${tipo} alert-dismissible fade show mt-2`;
    mensajeDiv.innerHTML = `
        <small>${mensaje}</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insertar después de la fila
    row.insertAdjacentElement('afterend', mensajeDiv);
}

function ocultarMensajeValidacion(row) {
    const mensajeExistente = row.nextElementSibling;
    if (mensajeExistente && mensajeExistente.classList.contains('alert')) {
        mensajeExistente.remove();
    }
}

function validarFormularioCompleto() {
    let esValido = true;
    const filas = document.querySelectorAll('#tablaGestionMonitores tbody tr');
    
    filas.forEach((row, index) => {
        const camposHoras = row.querySelectorAll('input.horas-mensuales');
        const horasTotales = parseInt(row.querySelector('input[name*="[horas_totales]"]').value) || 0;
        
        if (camposHoras.length > 0 && horasTotales > 0) {
            let sumaHoras = 0;
            camposHoras.forEach(campo => {
                const valor = parseInt(campo.value) || 0;
                sumaHoras += valor;
            });
            
            if (sumaHoras > horasTotales) {
                esValido = false;
                const nombreMonitor = row.querySelector('input[name*="[monitor_elegido]"]').value;
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Horas Excedidas',
                    html: `<strong>${nombreMonitor}</strong><br><br>
                           La suma de horas mensuales (${sumaHoras}) excede las horas totales (${horasTotales}).<br><br>
                           Por favor, ajusta las horas mensuales antes de guardar.`,
                    confirmButtonText: 'Entendido'
                });
            }
        }
    });
    
    return esValido;
}

function inicializarDataTable() {
    // Destruir DataTable existente si existe
    if ($.fn.DataTable.isDataTable('#tablaGestionMonitores')) {
        $('#tablaGestionMonitores').DataTable().destroy();
    }
    
    // Calcular el número total de columnas (6 fijas + meses dinámicos + 1 historial)
    const totalColumnas = 6 + mesesDinamicos.length + 1;
    
    // Crear configuración de columnas dinámicamente
    const columnDefs = [
        {
            targets: [0, 1], // Columnas de Monitoría y Monitor
            orderable: true,
            searchable: true,
            type: 'string'
        },
        {
            targets: [2, 3], // Columnas de horas
            orderable: true,
            searchable: false,
            type: 'num'
        },
        {
            targets: [4, 5], // Columnas de fechas
            orderable: true,
            searchable: false,
            type: 'date'
        },
        {
            targets: -1, // Última columna (Historial)
            orderable: false,
            searchable: false
        }
    ];
    
    // Agregar configuración para columnas de meses (desde la columna 6 hasta la penúltima)
    if (mesesDinamicos.length > 0) {
        const mesesTargets = [];
        for (let i = 6; i < totalColumnas - 1; i++) {
            mesesTargets.push(i);
        }
        columnDefs.push({
            targets: mesesTargets,
            orderable: true,
            searchable: false,
            type: 'num'
        });
    }
    
    // Inicializar DataTable con configuración mejorada
    $('#tablaGestionMonitores').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar columna ascendente",
                "sortDescending": ": activar para ordenar columna descendente"
            }
        },
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        destroy: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        columnDefs: columnDefs,
        // Configuración para inputs dentro de celdas
        drawCallback: function() {
            // Reinicializar tooltips después de cada redibujado
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Asegurarse de que todos los campos de horas mensuales, fechas y horas totales permanezcan habilitados
            document.querySelectorAll('input.horas-mensuales, input.fecha-vinculacion, input.fecha-culminacion, input[name*="[horas_totales]"]').forEach(input => {
                input.disabled = false;
                input.readOnly = false;
            });
        },
        // Configuración para mejorar el filtrado
        search: {
            smart: true,
            regex: false,
            caseInsensitive: true
        },
        // Configuración para mejorar el ordenamiento
        order: [[0, 'asc']], // Ordenar por la primera columna (Monitoría) por defecto
        // Configuración para mejorar la experiencia de usuario
        stateSave: false,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true
    });
    
    // Configurar filtrado personalizado para inputs dentro de celdas
    configurarFiltradoPersonalizado();
}

// Función para configurar filtrado personalizado
function configurarFiltradoPersonalizado() {
    const table = $('#tablaGestionMonitores').DataTable();
    
    // Agregar event listeners a los inputs para actualizar el filtrado
    $('#tablaGestionMonitores tbody').on('input', 'input[type="text"], input[type="number"], input[type="date"]', function() {
        const $cell = $(this).closest('td');
        const newValue = $(this).val();
        const $input = $(this);
        
        // Actualizar el atributo data-search para búsqueda
        if ($cell.hasClass('monitor-col')) {
            $cell.attr('data-search', newValue.toLowerCase());
        }
        
        // Actualizar el atributo data-order para ordenamiento
        if ($cell.hasClass('horas-col') || $cell.hasClass('mes-col') || $cell.hasClass('fecha-col')) {
            $cell.attr('data-order', newValue || 0);
        }
        
        // NO actualizar DataTables en tiempo real para campos de horas mensuales, fechas y horas totales
        // Esto evita que los campos pierdan el foco mientras se escriben
        if (!$input.hasClass('horas-mensuales') && !$input.hasClass('fecha-vinculacion') && !$input.hasClass('fecha-culminacion') && !$input.attr('name').includes('[horas_totales]')) {
            const cell = table.cell($cell[0]);
            cell.data(newValue).draw(false);
        }
    });
    
    // NO actualizar DataTables para campos de horas mensuales - mantener todos activos
    // Solo actualizar atributos de ordenamiento sin redibujar la tabla
    $('#tablaGestionMonitores tbody').on('blur', 'input.horas-mensuales', function() {
        const $cell = $(this).closest('td');
        const newValue = $(this).val();
        
        // Solo actualizar el atributo data-order para ordenamiento
        $cell.attr('data-order', newValue || 0);
        
        // NO redibujar DataTables para evitar deshabilitar campos
    });
    
    // Agregar un pequeño delay para evitar conflictos con DataTables
    $('#tablaGestionMonitores tbody').on('focus', 'input.horas-mensuales', function() {
        $(this).attr('data-focus-time', Date.now());
    });
    
    // Prevenir entrada de decimales en campos de horas mensuales
    $('#tablaGestionMonitores tbody').on('keypress', 'input.horas-mensuales', function(e) {
        // Permitir: backspace, delete, tab, escape, enter
        if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
            // Permitir: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }
        // Asegurar que es un número y no un decimal
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    // Limpiar valores decimales si se pegan
    $('#tablaGestionMonitores tbody').on('paste', 'input.horas-mensuales', function(e) {
        setTimeout(() => {
            let value = $(this).val();
            // Remover decimales y mantener solo la parte entera
            value = value.replace(/\.\d+/g, '').replace(/[^0-9]/g, '');
            $(this).val(value);
            validarHorasMensuales($(this));
        }, 10);
    });
    
    // Validar horas mensuales cuando cambien
    $('#tablaGestionMonitores tbody').on('input', 'input.horas-mensuales', function() {
        validarHorasMensuales($(this));
        actualizarEstadoVisualCampo(this);
    });
    
    // Actualizar estado visual cuando el campo pierde el foco
    $('#tablaGestionMonitores tbody').on('blur', 'input.horas-mensuales', function() {
        actualizarEstadoVisualCampo(this);
    });
    
    // Manejar campos de fecha sin redibujar DataTables
    $('#tablaGestionMonitores tbody').on('blur', 'input.fecha-vinculacion, input.fecha-culminacion', function() {
        const $cell = $(this).closest('td');
        const newValue = $(this).val();
        
        // Solo actualizar el atributo data-order para ordenamiento
        $cell.attr('data-order', newValue || 0);
        
        // NO redibujar DataTables para evitar deshabilitar campos de fecha
    });
    
    // Agregar un pequeño delay para evitar conflictos con DataTables en fechas
    $('#tablaGestionMonitores tbody').on('focus', 'input.fecha-vinculacion, input.fecha-culminacion', function() {
        $(this).attr('data-focus-time', Date.now());
    });
    
    // Manejar campo de horas totales sin redibujar DataTables
    $('#tablaGestionMonitores tbody').on('blur', 'input[name*="[horas_totales]"]', function() {
        const $cell = $(this).closest('td');
        const newValue = $(this).val();
        
        // Solo actualizar el atributo data-order para ordenamiento
        $cell.attr('data-order', newValue || 0);
        
        // NO redibujar DataTables para evitar deshabilitar campos
    });
    
    // Configurar búsqueda global mejorada
    const searchInput = $('.dataTables_filter input');
    if (searchInput.length > 0) {
        searchInput.attr('placeholder', 'Buscar en Monitoría, Monitor...');
        searchInput.attr('title', 'Busca por nombre de monitoría o monitor');
    }
    
    // Configurar búsqueda personalizada que incluya los atributos data-search
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const searchValue = table.search();
        if (!searchValue) return true;
        
        const $row = $(table.row(dataIndex).node());
        const searchableText = $row.find('[data-search]').map(function() {
            return $(this).attr('data-search') || '';
        }).get().join(' ');
        
        return searchableText.toLowerCase().includes(searchValue.toLowerCase());
    });
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('/gestion-monitores/data')
        .then(res => res.json())
        .then(data => {
            // Guardar datos globalmente para el historial
            window.monitoriasData = data.monitorias || [];
            
            const tbody = document.getElementById('tbodyGestionMonitores');
            const thead = document.getElementById('headerRow');
            tbody.innerHTML = '';
            mesesDinamicos = [];

            if (!data.monitorias || data.monitorias.length === 0) {
                tbody.innerHTML = '<tr><td colspan="12" class="text-center">No hay monitorías activas.</td></tr>';
                return;
            }

            let mesesSet = new Set();
            let monitoriasArray = Array.isArray(data.monitorias) ? data.monitorias : Object.values(data.monitorias);
            monitoriasArray.forEach(m => {
                const meses = getMesesEntreFechas(m.fecha_vinculacion, m.fecha_culminacion);
                meses.forEach(mes => mesesSet.add(mes));
            });
            mesesDinamicos = Array.from(mesesSet);

            let headerHtml = `
                <th>Monitoría</th>
                <th>Monitor Elegido</th>
                <th>Horas Semanales</th>
                <th>Horas Totales</th>
                <th>Fecha Vinculación</th>
                <th>Fecha Culminación</th>
            `;
                            mesesDinamicos.forEach(mes => {
                    headerHtml += `<th class="mes-col">${mes.charAt(0).toUpperCase() + mes.slice(1)}</th>`;
                });
                headerHtml += '<th class="historial-col">Historial</th>';
                thead.innerHTML = headerHtml;

            monitoriasArray.forEach((m, idx) => {
                let horas = {};
                if (m.horas_mensuales) {
                    horas = typeof m.horas_mensuales === 'string' ? JSON.parse(m.horas_mensuales) : m.horas_mensuales;
                }
                const horasSugeridas = calcularHorasMensuales(m.fecha_vinculacion, m.fecha_culminacion, m.horas_semanales);

                // Calcular mes actual para el seguimiento
                const hoy = new Date();
                const mesActual = hoy.getMonth() + 1; // 1-12

                let row = `<tr>
                    <input type="hidden" name="monitores[${idx}][monitor_id]" value="${m.monitor_id}">
                    <td class="sticky-col monitor-col" data-search="${m.nombre.toLowerCase()}">
                        <input type="text" class="form-control campo-largo" name="monitores[${idx}][monitoria_nombre]" 
                               value="${m.nombre}" readonly title="Monitoría: ${m.nombre}" 
                               data-bs-toggle="tooltip" data-bs-placement="top">
                    </td>
                    <td class="sticky-col-2 monitor-col" data-search="${m.monitor_elegido.toLowerCase()}">
                        <input type="text" class="form-control campo-largo" name="monitores[${idx}][monitor_elegido]" 
                               value="${m.monitor_elegido}" readonly title="Monitor: ${m.monitor_elegido}"
                               data-bs-toggle="tooltip" data-bs-placement="top">
                    </td>
                    <td class="horas-col" data-order="${m.horas_semanales}"><input type="number" class="form-control" name="monitores[${idx}][horas_semanales]" value="${m.horas_semanales}" readonly></td>
                    <td class="horas-col" data-order="${m.horas_totales || 0}">
                        <input type="number" class="form-control" name="monitores[${idx}][horas_totales]" 
                               value="${m.horas_totales ?? ''}" 
                               placeholder="${m.horas_totales_calculadas ? 'Calculado: ' + m.horas_totales_calculadas : ''}"
                               title="${m.horas_totales_calculadas ? 'Recomendación: ' + m.horas_totales_calculadas + ' horas (basado en fechas)' : ''}">
                    </td>
                    <td class="fecha-col" data-order="${m.fecha_vinculacion || ''}"><input type="date" class="form-control fecha-vinculacion" name="monitores[${idx}][fecha_vinculacion]" value="${m.fecha_vinculacion ? m.fecha_vinculacion.substring(0,10) : ''}" onchange="actualizarHorasMensuales(this)"></td>
                    <td class="fecha-col" data-order="${m.fecha_culminacion || ''}"><input type="date" class="form-control fecha-culminacion" name="monitores[${idx}][fecha_culminacion]" value="${m.fecha_culminacion ? m.fecha_culminacion.substring(0,10) : ''}" onchange="actualizarHorasMensuales(this)"></td>
                `;
                mesesDinamicos.forEach(mes => {
                    const valorActual = horas[mes] ?? '';
                    const valorSugerido = horasSugeridas[mes] ?? '';
                    const valorOrden = valorActual || valorSugerido || 0;
                    
                    // Determinar clase CSS según el estado del campo
                    let claseEstado = '';
                    if (valorActual && valorActual !== '') {
                        claseEstado = 'campo-diligenciado';
                    } else if (valorSugerido && valorSugerido !== '') {
                        claseEstado = 'campo-pendiente';
                    } else {
                        claseEstado = 'campo-vacio';
                    }
                    
                    row += `<td class="mes-col" data-order="${valorOrden}">
                        <input type="number" class="form-control horas-mensuales ${claseEstado}" 
                            name="monitores[${idx}][${mes}]" 
                            value="${valorActual}"
                            placeholder="${valorSugerido}"
                            title="${valorActual ? `Horas registradas: ${valorActual}` : `Pendiente - Horas sugeridas: ${valorSugerido}`}"
                            min="0" 
                            max="999" 
                            step="1">
                    </td>`;
                });
                // Columna de Historial
                row += `<td class="historial-col">
                    <div class="d-flex flex-column gap-1">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="mostrarHistorialDocumentos(${m.monitor_id}, '${m.monitor_elegido}')" title="Ver historial completo de documentos">
                            <i class="fa-solid fa-history"></i>
                        </button>
                    </div>
                </td>
                </tr>`;
                tbody.innerHTML += row;
            });

            // Inicializar DataTable después de crear toda la tabla
            inicializarDataTable();
            
            // Inicializar estado visual de todos los campos de horas mensuales
            inicializarEstadosVisuales();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tbodyGestionMonitores').innerHTML = 
                '<tr><td colspan="12" class="text-center text-danger">Error al cargar los datos.</td></tr>';
        });

    // Botón para aplicar fechas generales
    document.getElementById('btnAplicarFechas').addEventListener('click', function() {
        const fechaVinc = document.getElementById('fechaGeneralVinculacion').value;
        const fechaCulm = document.getElementById('fechaGeneralCulminacion').value;
        if (!fechaVinc || !fechaCulm) {
            Swal.fire({
                icon: 'warning',
                title: 'Fechas requeridas',
                text: 'Por favor selecciona ambas fechas generales para aplicar.',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        document.querySelectorAll('.fecha-vinculacion').forEach(input => input.value = fechaVinc);
        document.querySelectorAll('.fecha-culminacion').forEach(input => input.value = fechaCulm);
    });

    // Aplicar vista compacta como única vista
    const tabla = document.getElementById('tablaGestionMonitores');
    tabla.classList.add('vista-compacta');
});

// Función para actualizar las horas mensuales cuando cambian las fechas
function actualizarHorasMensuales(input) {
    const row = input.closest('tr');
    const fechaInicio = row.querySelector('input[name*="[fecha_vinculacion]"]').value;
    const fechaFin = row.querySelector('input[name*="[fecha_culminacion]"]').value;
    const horasSemanales = parseInt(row.querySelector('input[name*="[horas_semanales]"]').value);

    if (fechaInicio && fechaFin && horasSemanales) {
        const horasSugeridas = calcularHorasMensuales(fechaInicio, fechaFin, horasSemanales);
        
        // Calcular horas totales recomendadas
        const horasTotalesCalculadas = calcularHorasTotales(fechaInicio, fechaFin, horasSemanales);
        
        // Actualizar placeholder de horas totales
        const campoHorasTotales = row.querySelector('input[name*="[horas_totales]"]');
        if (campoHorasTotales) {
            campoHorasTotales.placeholder = `Calculado: ${horasTotalesCalculadas}`;
            campoHorasTotales.title = `Recomendación: ${horasTotalesCalculadas} horas (basado en fechas)`;
        }
        
        // Solo actualizar campos de horas mensuales (no las horas semanales/totales)
        row.querySelectorAll('input.horas-mensuales').forEach(input => {
            const matches = input.name.match(/\[(.*?)\]$/);
            if (matches) {
                const mes = matches[1];
                if (horasSugeridas[mes]) {
                    // Solo actualizar placeholder y title si el campo está vacío
                    // para no interferir con valores ya ingresados por el usuario
                    if (!input.value || input.value.trim() === '') {
                        input.placeholder = horasSugeridas[mes];
                    }
                    input.title = `Horas sugeridas: ${horasSugeridas[mes]}`;
                }
            }
        });
    }
}

// Imprimir tabla con formato profesional (sin columna de seguimiento)
function imprimirTabla() {
    const tabla = document.getElementById('tablaGestionMonitores').cloneNode(true);
    
    // Remover DataTables y hacer la tabla más simple para impresión
    const inputs = tabla.querySelectorAll('input');
    inputs.forEach(input => {
        const span = document.createElement('span');
        span.textContent = input.value || input.placeholder || '';
        span.style.fontWeight = 'bold';
        input.parentNode.replaceChild(span, input);
    });

    // Remover la columna de documentos para el PDF
    const filas = tabla.querySelectorAll('tr');
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td, th');
        if (celdas.length > 0) {
            // Remover la última celda (columna de documentos)
            const ultimaCelda = celdas[celdas.length - 1];
            if (ultimaCelda) {
                ultimaCelda.remove();
            }
        }
    });

    // Crear contenido HTML para impresión
    const contenidoHTML = `
        <html>
        <head>
            <title>Consultar Monitores - ERP Univalle</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { color: #0d6efd; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                .info { background-color: #e7f3ff; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>ERP Univalle</h1>
                <h2>Consultar Monitores</h2>
                <p>Fecha de impresión: ${new Date().toLocaleDateString('es-ES')}</p>
            </div>
            <div class="info">
                <strong>Información:</strong> Esta tabla muestra los monitores activos con sus fechas de vinculación, 
                culminación y horas mensuales asignadas. Las horas en cada columna representan las horas trabajadas por mes.
            </div>
            ${tabla.outerHTML}
        </body>
        </html>
    `;

    // Crear un iframe oculto para la impresión
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.style.position = 'fixed';
    iframe.style.top = '-9999px';
    iframe.style.left = '-9999px';
    document.body.appendChild(iframe);
    
    iframe.onload = function() {
        try {
            iframe.contentWindow.document.write(contenidoHTML);
            iframe.contentWindow.document.close();
            
            // Esperar un momento para que se cargue el contenido
            setTimeout(() => {
                iframe.contentWindow.print();
                
                // Remover el iframe después de un tiempo
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 1000);
            }, 500);
        } catch (error) {
            console.error('Error al imprimir:', error);
            // Fallback: usar ventana emergente si el iframe falla
            const ventana = window.open('', '', 'height=700,width=1200');
            ventana.document.write(contenidoHTML);
            ventana.document.close();
            ventana.print();
        }
    };
    
    iframe.src = 'about:blank';
}

// Descargar histórico completo
function descargarHistorico() {
    Swal.fire({
        title: 'Descargando Histórico',
        text: 'Preparando el archivo con todos los monitores históricos...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/gestion-monitores/historico')
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Error al generar el histórico');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `historico_monitores_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            Swal.fire({
                icon: 'success',
                title: '¡Descarga Completada!',
                text: 'El archivo con el histórico completo de monitores se ha descargado correctamente.',
                confirmButtonText: 'Entendido'
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error en la Descarga',
                text: 'No se pudo generar el archivo del histórico. Por favor, intenta nuevamente.',
                confirmButtonText: 'Cerrar'
            });
        });
}

// Manejar el envío del formulario
document.getElementById('formGestionMonitores').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        const matches = key.match(/monitores\[(\d+)\]\[(.*?)\]/);
        if (matches) {
            const [, index, field] = matches;
            if (!data[index]) data[index] = {};
            data[index][field] = value;
        }
    }
    
    const meses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];
    Object.values(data).forEach(monitor => {
        monitor.horas_mensuales = {};
        meses.forEach(mes => {
            if (monitor.hasOwnProperty(mes)) {
                if (monitor[mes] !== '' && !isNaN(monitor[mes]) && parseInt(monitor[mes]) >= 0) {
                    monitor.horas_mensuales[mes] = parseInt(monitor[mes]);
                }
                delete monitor[mes];
            }
        });
    });
    
    // Validar horas antes de enviar
    if (!validarFormularioCompleto()) {
        return;
    }
    
    fetch('/gestion-monitores/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ monitores: Object.values(data) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: 'Datos guardados correctamente. Recuerda que las horas mensuales serán las que se reflejarán en el seguimiento.',
                confirmButtonText: 'Entendido'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar los datos: ' + data.message,
                confirmButtonText: 'Cerrar'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al guardar los datos',
            confirmButtonText: 'Cerrar'
        });
    });
});

// Función para mostrar el historial de documentos
function mostrarHistorialDocumentos(monitorId, nombreMonitor) {
    // Actualizar el nombre del monitor en el modal
    document.getElementById('nombreMonitorHistorial').textContent = nombreMonitor;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalHistorialDocumentos'));
    modal.show();
    
    // Buscar los datos del monitor en la tabla actual
    const monitorias = window.monitoriasData || [];
    const monitor = monitorias.find(m => m.monitor_id == monitorId);
    
    if (monitor && monitor.documentos) {
        mostrarContenidoHistorial(monitor.documentos);
    } else {
        // Si no hay datos en memoria, cargar desde el servidor
        cargarHistorialDesdeServidor(monitorId);
    }
}

// Función para mostrar el contenido del historial
function mostrarContenidoHistorial(documentos) {
    const contenido = document.getElementById('contenidoHistorial');
    
    if (!documentos || documentos.length === 0) {
        contenido.innerHTML = `
            <div class="alert alert-warning text-center">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                No hay documentos en el historial para este monitor.
            </div>
        `;
        return;
    }
    
    let html = '<div class="table-responsive">';
    html += '<table class="table table-striped table-hover">';
    html += '<thead class="table-light">';
    html += '<tr>';
    html += '<th>Tipo de Documento</th>';
    html += '<th>Período</th>';
    html += '<th>Estado</th>';
    html += '<th>Fecha Generación</th>';
    html += '<th>Acciones</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    documentos.forEach(doc => {
        const nombreMes = doc.mes ? obtenerNombreMes(doc.mes) : '';
        const periodo = doc.tipo_documento === 'evaluacion_desempeno' ? 
            (doc.parametros_generacion?.periodo_academico || 'N/A') :
            (doc.mes && doc.anio ? `${nombreMes} ${doc.anio}` : 'N/A');
        
        const estadoClase = doc.estado === 'firmado' ? 'bg-success text-white' : 
                           doc.estado === 'generado' ? 'bg-secondary text-white' : 'bg-warning text-dark';
        
        html += '<tr>';
        html += `<td><i class="${obtenerIconoTipo(doc.tipo_documento)} me-2"></i>${obtenerNombreTipo(doc.tipo_documento)}</td>`;
        html += `<td>${periodo}</td>`;
        html += `<td><span class="badge ${estadoClase}">${doc.estado}</span></td>`;
        html += `<td>${formatearFecha(doc.fecha_generacion)}</td>`;
        html += '<td>';
        
        // Botón para ver documento
        if (doc.tipo_documento === 'asistencia' && doc.ruta_archivo) {
            html += `<a href="/monitoria/asistencia/ver/${doc.monitor_id}/${doc.anio}/${doc.mes}" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="Ver documento">
                <i class="fa-solid fa-eye"></i> Ver
            </a>`;
        } else if (doc.tipo_documento === 'seguimiento' && doc.mes) {
            html += `<a href="/monitoria/seguimiento/pdf/${doc.monitor_id}/${doc.mes}/${doc.anio || new Date().getFullYear()}" class="btn btn-sm btn-outline-info me-1" target="_blank" title="Ver documento">
                <i class="fa-solid fa-eye"></i> Ver
            </a>`;
        } else if (doc.tipo_documento === 'evaluacion_desempeno') {
            html += `<a href="/monitoria/desempeno/pdf/${doc.monitor_id}" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="Ver documento">
                <i class="fa-solid fa-eye"></i> Ver
            </a>`;
        }
        
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    
    contenido.innerHTML = html;
}

// Función para cargar historial desde el servidor
function cargarHistorialDesdeServidor(monitorId) {
    const contenido = document.getElementById('contenidoHistorial');
    contenido.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando historial desde el servidor...</p>
        </div>
    `;
    
    // Aquí podrías hacer una llamada AJAX para cargar el historial
    // Por ahora, mostraremos un mensaje de error
    setTimeout(() => {
        contenido.innerHTML = `
            <div class="alert alert-warning text-center">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                No se pudo cargar el historial. Intenta recargar la página.
            </div>
        `;
    }, 2000);
}

// Funciones auxiliares
function obtenerNombreMes(mes) {
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    return meses[mes - 1] || '';
}

function obtenerNombreTipo(tipo) {
    const tipos = {
        'seguimiento': 'Seguimiento Mensual',
        'asistencia': 'Asistencia Mensual',
        'evaluacion_desempeno': 'Evaluación de Desempeño'
    };
    return tipos[tipo] || tipo;
}

function obtenerIconoTipo(tipo) {
    const iconos = {
        'seguimiento': 'fa-solid fa-eye',
        'asistencia': 'fa-solid fa-file-pdf',
        'evaluacion_desempeno': 'fa-solid fa-file-pdf'
    };
    return iconos[tipo] || 'fa-solid fa-file';
}

function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
@endsection