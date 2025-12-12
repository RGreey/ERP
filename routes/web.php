<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PeriodoAcademicoController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\MonitoriaController;
use App\Http\Controllers\PostuladoController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\NovedadController;

use App\Http\Controllers\SubsidioAlimenticioController;
use App\Http\Controllers\AdminEstudiantesController;
use App\Http\Controllers\AdminCuposController;
use App\Http\Controllers\AdminRestaurantesController;
use App\Http\Controllers\AdminReportesController;
use App\Http\Controllers\AdminStandbyController;

use App\Http\Controllers\EstudianteConvocatoriaController;
use App\Http\Controllers\PostulacionSubsidioController;
use App\Http\Controllers\EstudiantePostulacionController;

use App\Http\Controllers\PWA\SubsidioEstudianteController;
use App\Http\Controllers\PWA\ReportesEstudianteController;
use App\Http\Controllers\PWA\StandbyController as PwaStandbyController;

use App\Http\Controllers\PWA\Restaurantes\AsistenciasController as RestAsistenciasController;
use App\Http\Controllers\PWA\Restaurantes\ReportesRestaurantesController;
use App\Http\Controllers\PWA\Restaurantes\RestaurantesDashboardController;

use App\Http\Controllers\StandbyOfferController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Auth::routes();

Route::view('/', 'welcome');

// Utilidades (considera proteger con clave en producción)
Route::get('storage-link', fn() => Artisan::call('storage:link'));
Route::get('eventos-del-dia', function () {
    $key = request('key');
    abort_if($key !== env('EVENTOS_SECRET_KEY'), 403);
    Artisan::call('eventos:enviar-del-dia');
    return 'Tarea ejecutada';
});

// Sesión
Route::get('/flush-session', function (Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success','Sesión reiniciada.');
});

// HOME único (incluye Subsidio/Restaurante/Estudiante)
Route::get('/home', function () {
    if (!auth()->check()) return redirect()->route('login');
    $u = auth()->user();

    if ($u->hasRole('AdminBienestar')) return redirect()->route('subsidio.admin.dashboard');
    if ($u->hasRole('Restaurante'))    return redirect()->route('restaurantes.asistencias.hoy'); // FIX nombre ruta
    if ($u->hasRole('Estudiante'))     return redirect()->route('app.subsidio.mis-cupos');

    if ($u->hasRole('Administrativo') || $u->hasRole('CooAdmin') || $u->hasRole('AuxAdmin'))
        return redirect()->route('administrativo.dashboard');
    if ($u->hasRole('Profesor'))
        return redirect()->route('profesor.dashboard');

    return redirect('/');
})->name('home');

// Auth auxiliares
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Dashboards por rol
Route::middleware(['auth','checkrole:Administrativo,CooAdmin,AuxAdmin,Profesor','verified'])->group(function () {
    Route::view('/administrativo/dashboard', 'roles.administrativo.dashboard')->name('administrativo.dashboard');
    Route::get('/consultarEventos', [EventoController::class, 'indexAdmin'])->name('consultarEventos');
});

Route::middleware(['auth','checkrole:Estudiante','verified'])->get('/estudiante/dashboard', fn() => view('roles.estudiante.dashboard'))->name('estudiante.dashboard');

Route::middleware(['auth','checkrole:Profesor','verified'])->group(function () {
    Route::get('/profesor/dashboard', [DashboardController::class, 'profesorDashboard'])->name('profesor.dashboard'); // Deja solo esta
});

// Administrativo/Profesor
Route::middleware(['auth','checkrole:Administrativo,Profesor,CooAdmin,AuxAdmin','verified'])->group(function () {
    Route::get('/crearEvento', [EventoController::class, 'crearEvento'])->name('crearEvento');
    Route::get('/administrativo/dashboard', [DashboardController::class, 'administrativoDashboard'])->name('administrativo.dashboard');
});

// Eventos generales
Route::get('/obtener-espacios/{lugarId}', [EventoController::class, 'obtenerEspacios']);
Route::post('/crearEvento', [EventoController::class, 'guardarEvento'])->name('guardarEvento');
Route::post('/guardar-evento', [EventoController::class, 'guardarEvento'])->name('guardar-evento');
Route::get('/obtener-eventos', [EventoController::class, 'obtenerEventos']);
Route::get('generate-pdf/{id}', [PDFController::class, 'generatePDF']);

// Calendario
Route::middleware(['auth'])->view('/calendario', 'calendario')->name('calendario');

// Monitoría: seguimiento/actividades
Route::post('/crearMonitor/{postuladoId}', [PostuladoController::class, 'storeFechas'])->name('postulados.storeFechas');
Route::post('/monitoria/seguimiento/store', [MonitoriaController::class, 'store'])->name('seguimiento.monitoria.store');
Route::get('/monitoria/seguimiento/{monitoria_id}', [MonitoriaController::class, 'seguimiento'])->name('seguimiento.monitoria');
Route::get('/monitor-id', [MonitorController::class, 'getMonitorId'])->name('monitor.id');
Route::get('/api/actividades/{monitor_id}', [MonitorController::class, 'cargarActividades']);
Route::delete('/api/actividades/eliminar/{id}', [MonitorController::class, 'eliminar'])->name('actividades.eliminar');

// Estados de evento
Route::put('/administrativo/consultarEventos/{eventoId}/actualizar-estado', [EventoController::class, 'actualizarEstado'])->name('actualizar_estado');
Route::post('/actualizar-evento/{eventoId}', [EventoController::class, 'actualizarEvento'])->name('actualizarEvento');
Route::post('/eventos/{id}/actualizar', [EventoController::class, 'actualizarEvento'])->name('actualizarEvento'); // alias
Route::get('/editarEvento/{id}', [EventoController::class, 'editarEvento'])->name('editarEvento');
Route::delete('/eventos/borrar/{id}', [EventoController::class, 'borrarEvento'])->name('borrarEvento');
Route::get('/obtener-informacion-evento/{id}', [EventoController::class, 'verEvento'])->name('obtener_informacion_evento');
Route::get('/ver-evento/{id}', [EventoController::class, 'verEvento'])->name('ver_evento');
Route::get('/obtener-info', [EventoController::class, 'informacionEventos'])->name('crear.evento');
Route::post('/eventos/verificar-nombre', [EventoController::class, 'verificarNombre']);

// Calificación y anotaciones
Route::post('/calificar-evento', [EventoController::class, 'calificarEvento'])->name('calificar-evento');
Route::get('/verificar-calificacion/{eventoId}', [EventoController::class, 'verificarCalificacion']);
Route::post('/anotaciones/agregar', [EventoController::class, 'agregarAnotacion'])->name('anotacion.agregar');
Route::get('/anotaciones/{eventoId}', [EventoController::class, 'verAnotaciones'])->name('anotaciones.ver');

// Dashboard genérico por rol
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();
    if ($user->hasRole('Administrativo') || $user->hasRole('CooAdmin') || $user->hasRole('AuxAdmin')) return redirect()->route('administrativo.dashboard');
    if ($user->hasRole('Profesor'))   return redirect()->route('profesor.dashboard');
    if ($user->hasRole('Estudiante')) return redirect()->route('estudiante.dashboard');
})->name('dashboard');

Route::get('/exportar-eventos', [EventoController::class, 'exportarEventos'])->name('exportar.eventos');

// Módulo Monitorías — Convocatorias y Períodos (elige si AuxAdmin entra)
Route::middleware(['auth','checkrole:CooAdmin,AuxAdmin'])->group(function () {
    Route::prefix('convocatorias')->group(function () {
        Route::get('/', [ConvocatoriaController::class, 'index'])->name('convocatoria.index');
        Route::post('/store', [ConvocatoriaController::class, 'store'])->name('convocatorias.store');
        Route::put('/{convocatoria}', [ConvocatoriaController::class, 'update'])->name('convocatorias.update');
        Route::delete('/{convocatoria}', [ConvocatoriaController::class, 'destroy'])->name('convocatorias.destroy');
        Route::get('/{convocatoria}', [ConvocatoriaController::class, 'show'])->name('convocatorias.show');
        Route::post('/{convocatoria}/reabrir', [ConvocatoriaController::class, 'reabrir'])->name('convocatorias.reabrir');
    });

    Route::get('/crearPeriodoA', [PeriodoAcademicoController::class, 'create'])->name('periodos.crear');
    Route::post('/crearPeriodoA', [PeriodoAcademicoController::class, 'store'])->name('periodos.store');
    Route::get('/obtenerPeriodo', [PeriodoAcademicoController::class, 'index'])->name('periodos.index');
    Route::put('/periodos/{periodoAcademico}', [PeriodoAcademicoController::class, 'update'])->name('periodos.update');
});

// Monitorías — CRUD y PDF
Route::middleware(['auth','checkrole:CooAdmin,Profesor,Administrativo'])->group(function () {
    Route::get('/monitorias', [MonitoriaController::class, 'index'])->name('monitoria.index');
    Route::post('/monitorias', [MonitoriaController::class, 'store'])->name('monitoria.store');
    Route::post('/monitorias/updateEstado/{id}', [MonitoriaController::class, 'updateEstado'])->name('monitorias.updateEstado');
    Route::put('/monitorias/actualizar', [MonitoriaController::class, 'update'])->name('monitoria.update');
    Route::get('/monitoria/get', [MonitoriaController::class, 'getMonitoria'])->name('monitoria.get');
    Route::get('/monitorias/activas', [MonitoriaController::class, 'listarMonitoriasActivas'])->name('monitorias.activas'); // único name
});

Route::middleware(['auth'])->group(function () {
    Route::post('/postular', [PostuladoController::class, 'store'])->name('postular');
    Route::delete('/postulacion/{monitoria}', [PostuladoController::class, 'destroy'])->name('postulacion.destroy');

    Route::get('/documentos/{monitoriaId}', [PostuladoController::class, 'getDocument']);
    Route::get('/monitorias/pdf', [MonitoriaController::class, 'generarPDF'])->name('monitorias.pdf');

    Route::put('/postulados/{id}', [PostuladoController::class, 'update'])->name('postulados.update');

    Route::get('/monitorias/lista', [MonitoriaController::class, 'listarMonitoriasActivas'])->name('listaMonitorias'); // name distinto

    Route::post('/postulados/{id}/enviarCorreo', [PostuladoController::class, 'enviarCorreo'])->name('postulados.enviarCorreo');

    Route::get('/generate-pdf/{id}', [PDFController::class, 'generatePDF']);
    Route::post('/enviar-correo-evento', [PDFController::class, 'enviarCorreo'])->name('enviar.correo.evento');

    Route::get('gestion-monitores', [MonitorController::class, 'indexGestionMonitores'])->name('admin.gestionMonitores');
    Route::get('gestion-monitores/data', [MonitorController::class, 'getGestionMonitoresData'])->name('admin.gestionMonitores.data');
    Route::get('gestion-monitores/debug', [MonitorController::class, 'debugGestionMonitores'])->name('admin.gestionMonitores.debug');
    Route::post('/gestion-monitores/store', [MonitorController::class, 'storeGestionMonitores'])->name('gestionMonitores.store');
    Route::get('/gestion-monitores/historico', [MonitorController::class, 'descargarHistorico'])->name('gestionMonitores.historico');

    Route::get('/lista-admitidos/pdf', [App\Http\Controllers\ListaAdmitidosController::class, 'generarPDF'])->name('lista-admitidos.pdf');
    Route::get('/lista-admitidos', [App\Http\Controllers\ListaAdmitidosController::class, 'index'])->name('lista-admitidos.index');
    Route::post('/lista-admitidos/actualizar-cedulas', [App\Http\Controllers\ListaAdmitidosController::class, 'actualizarCedulas'])->name('lista-admitidos.actualizar-cedulas');

    Route::get('/monitoria/desempeno/pdf/{monitor_id}', [MonitoriaController::class, 'generarPDFDesempeno'])->name('monitoria.desempeno.pdf');
    Route::post('/monitoria/desempeno/guardar', [MonitoriaController::class, 'guardarDesempeno'])->name('monitoria.desempeno.guardar');
    Route::post('/monitoria/desempeno/borrar', [MonitoriaController::class, 'borrarDesempeno'])->name('monitoria.desempeno.borrar');

    Route::post('/monitoria/seguimiento/guardar', [MonitoriaController::class, 'guardarSeguimiento'])->name('monitoria.seguimiento.guardar');
    Route::post('/seguimiento/guardar-observacion/{id}', [MonitorController::class, 'guardarObservacion'])->name('seguimiento.guardarObservacion');

    // Compatibilidad: mantener anio? y POST
    Route::match(['get','post'], '/monitoria/seguimiento/pdf/{monitor_id}/{mes}/{anio?}', [MonitoriaController::class, 'generarPDFSeguimiento'])
        ->name('monitoria.seguimiento.pdf');

    // Asistencia mensual
    Route::post('/monitoria/asistencia/subir', [MonitorController::class, 'subirAsistencia'])->name('monitoria.asistencia.subir');
    Route::get('/monitoria/asistencia/ver/{monitor_id}/{anio}/{mes}', [MonitorController::class, 'verAsistencia'])->name('monitoria.asistencia.ver');
    Route::delete('/monitoria/asistencia/borrar/{monitor_id}/{anio}/{mes}', [MonitorController::class, 'borrarAsistencia'])->name('monitoria.asistencia.borrar');
});

// Postulados
Route::middleware(['auth','checkrole:Administrativo,CooAdmin,AuxAdmin'])->get('/postulados', [PostuladoController::class, 'index'])->name('postulados.index');
Route::middleware(['auth','checkrole:Administrativo,CooAdmin,AuxAdmin,Profesor'])->group(function () {
    Route::get('/postulados/entrevistas', [PostuladoController::class, 'entrevistas'])->name('postulados.entrevistas');
    Route::post('/postulados/{id}/guardar-entrevista', [PostuladoController::class, 'guardarEntrevista'])->name('postulados.guardarEntrevista');
    Route::post('/postulados/{id}/decidir-entrevista', [PostuladoController::class, 'decidirEntrevista'])->name('postulados.decidirEntrevista');
    Route::post('/postulados/{id}/revertir-decision', [PostuladoController::class, 'revertirDecision'])->name('postulados.revertirDecision');
});
Route::post('/monitorias/{id}/comentarios', [MonitoriaController::class, 'updateComentarios'])->name('monitorias.updateComentarios');

// Endpoints públicos para dashboards
Route::get('/convocatoria/estadisticas-monitorias', [ConvocatoriaController::class, 'estadisticasMonitorias']);
Route::get('/dashboard/estadisticas-horas-convocatoria', [DashboardController::class, 'estadisticasHorasConvocatoria']);

// Novedades (público + protegido)
Route::prefix('novedades')->name('novedades.')->group(function () {
    Route::get('/', [NovedadController::class, 'index'])->name('index');
    Route::post('/', [NovedadController::class, 'store'])->name('store');
    Route::get('/{id}', [NovedadController::class, 'show'])->name('show');
    Route::put('/{id}', [NovedadController::class, 'update'])->name('update');
    Route::post('/{id}/evidencia', [NovedadController::class, 'addEvidencia'])->name('addEvidencia');
    Route::post('/{id}/cerrar', [NovedadController::class, 'closeNovedad'])->name('close');
    Route::delete('/{id}', [NovedadController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/mantenimiento-realizado', [NovedadController::class, 'updateEstado'])->name('updateEstado');
});

Route::middleware(['auth','checkrole:Administrativo,Profesor,CooAdmin,AuxAdmin'])->prefix('novedades')->name('novedades.')->group(function () {
    Route::get('/', [NovedadController::class, 'index'])->name('index');
    Route::post('/', [NovedadController::class, 'store'])->name('store');
    Route::get('/{id}', [NovedadController::class, 'show'])->name('show');
    Route::put('/{id}', [NovedadController::class, 'update'])->name('update');
    Route::post('/{id}/evidencia', [NovedadController::class, 'addEvidencia'])->name('addEvidencia');
    Route::post('/{id}/cerrar', [NovedadController::class, 'closeNovedad'])->name('close');
    Route::delete('/{id}', [NovedadController::class, 'destroy'])->name('destroy');
});

// Mantenimiento
Route::middleware(['auth','checkrole:CooAdmin,AuxAdmin'])->prefix('mantenimiento')->name('mantenimiento.')->group(function () {
    Route::get('/', [App\Http\Controllers\MantenimientoController::class, 'index'])->name('index');
    Route::get('/crear', [App\Http\Controllers\MantenimientoController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\MantenimientoController::class, 'store'])->name('store');
    Route::get('/{actividad}', [App\Http\Controllers\MantenimientoController::class, 'show'])->name('show');
    Route::get('/{actividad}/editar', [App\Http\Controllers\MantenimientoController::class, 'edit'])->name('edit');
    Route::put('/{actividad}', [App\Http\Controllers\MantenimientoController::class, 'update'])->name('update');
    Route::delete('/{actividad}', [App\Http\Controllers\MantenimientoController::class, 'destroy'])->name('destroy');

    Route::post('/{actividad}/marcar-realizada', [App\Http\Controllers\MantenimientoController::class, 'marcarRealizada'])->name('marcar-realizada');
    Route::post('/{actividad}/marcar-pendiente', [App\Http\Controllers\MantenimientoController::class, 'marcarPendiente'])->name('marcar-pendiente');
    Route::post('/semana/{semana}/marcar-ejecutada', [App\Http\Controllers\MantenimientoController::class, 'marcarSemanaEjecutada'])->name('semana.marcar-ejecutada');
    Route::post('/semana/{semana}/marcar-pendiente', [App\Http\Controllers\MantenimientoController::class, 'marcarSemanaPendiente'])->name('semana.marcar-pendiente');
    Route::post('/{actividad}/generar-semanas', [App\Http\Controllers\MantenimientoController::class, 'generarSemanas'])->name('generar-semanas');
    Route::post('/cargar-actividades-predeterminadas', [App\Http\Controllers\MantenimientoController::class, 'cargarActividadesPredeterminadas'])->name('cargar-predeterminadas');
    Route::delete('/eliminar-todas', [App\Http\Controllers\MantenimientoController::class, 'eliminarTodas'])->name('eliminar-todas');
    Route::post('/eliminar-todas', [App\Http\Controllers\MantenimientoController::class, 'eliminarTodas'])->name('eliminar-todas.post');
    Route::post('/limpiar-semanas', [App\Http\Controllers\MantenimientoController::class, 'limpiarSemanas'])->name('limpiar-semanas');
});

// Evidencias
Route::middleware(['auth','checkrole:CooAdmin,AuxAdmin'])->prefix('evidencias-mantenimiento')->name('evidencias-mantenimiento.')->group(function () {
    Route::get('/', [App\Http\Controllers\PaqueteEvidenciaController::class, 'index'])->name('index');
    Route::get('/paquetes/{paquete}/edit', [App\Http\Controllers\PaqueteEvidenciaController::class, 'edit'])->name('paquetes.edit');
    Route::put('/paquetes/{paquete}', [App\Http\Controllers\PaqueteEvidenciaController::class, 'update'])->name('paquetes.update');
    Route::get('/paquetes/{paquete}/generar-pdf', [App\Http\Controllers\PaqueteEvidenciaController::class, 'generarPdf'])->name('paquetes.generar-pdf');
    Route::get('/paquetes/{paquete}/descargar', [App\Http\Controllers\PaqueteEvidenciaController::class, 'descargar'])->name('paquetes.descargar');
    Route::get('/paquetes/{paquete}/previsualizar', [App\Http\Controllers\PaqueteEvidenciaController::class, 'previsualizar'])->name('paquetes.previsualizar');
    Route::delete('/paquetes/{paquete}/limpiar', [App\Http\Controllers\PaqueteEvidenciaController::class, 'eliminarPdf'])->name('paquetes.limpiar');
    Route::post('/limpiar-archivos', [App\Http\Controllers\PaqueteEvidenciaController::class, 'limpiarArchivos'])->name('limpiar-archivos');
});

// Export Excel
Route::get('/exportar-excel', [App\Http\Controllers\MantenimientoController::class, 'exportarExcel'])->name('mantenimiento.exportar-excel');

// Admin usuarios
Route::middleware(['auth','checkrole:CooAdmin,AuxAdmin,Administrativo','verified'])->prefix('admin/usuarios')->name('admin.usuarios.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminUsuarioController::class, 'index'])->name('index');
    Route::post('/{id}/aprobar-rol', [App\Http\Controllers\AdminUsuarioController::class, 'aprobarRol'])->name('aprobarRol');
    Route::get('/crear', [App\Http\Controllers\AdminUsuarioController::class, 'create'])->name('create');
    Route::post('/crear', [App\Http\Controllers\AdminUsuarioController::class, 'store'])->name('store');
    Route::get('/{id}/editar', [App\Http\Controllers\AdminUsuarioController::class, 'edit'])->name('edit');
    Route::put('/{id}/actualizar', [App\Http\Controllers\AdminUsuarioController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\AdminUsuarioController::class, 'destroy'])->name('destroy');
});

// Backups
Route::middleware(['auth','verified'])->prefix('admin/backups')->name('admin.backups.')->group(function () {
    Route::get('/', [App\Http\Controllers\BackupController::class, 'index'])->name('index');
    Route::post('/crear', [App\Http\Controllers\BackupController::class, 'create'])->name('create');
    Route::get('/descargar/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('download');
    Route::delete('/eliminar/{filename}', [App\Http\Controllers\BackupController::class, 'delete'])->name('delete');
});

// Clear cache (considera proteger con clave)
Route::get('clear-cache', function() {
    try {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        return response()->json(['success'=>true,'message'=>'Caché limpiada correctamente']);
    } catch (\Exception $e) {
        return response()->json(['success'=>false,'message'=>'Error al limpiar caché: '.$e->getMessage()], 500);
    }
})->name('clear.cache');

// Email verification
Route::get('/email/verify', fn() => view('auth.verify'))->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) { $request->fulfill(); return redirect('/dashboard'); })->middleware(['auth','signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) { $request->user()->sendEmailVerificationNotification(); return back()->with('message','¡Enlace de verificación reenviado!'); })->middleware(['auth','throttle:6,1'])->name('verification.send');

// Rutas de prueba/diagnóstico (mantén clave)
Route::get('/probar-convocatoria', /* ... igual a tu versión ... */)->name('probar.convocatoria');
Route::get('/test-sistema-completo', /* ... igual a tu versión ... */)->name('test.sistema.completo');
Route::get('/debug-convocatorias', /* ... igual a tu versión ... */)->name('debug.convocatorias');
Route::get('/test-sistema-convocatoria', /* ... igual a tu versión ... */)->name('test.sistema.convocatoria');
Route::get('/probar-convocatoria-html', /* ... igual a tu versión ... */)->name('probar.convocatoria.html');

// =================== MÓDULO SUBSIDIO ALIMENTICIO ===================

// Admin Bienestar
Route::middleware(['auth','checkrole:AdminBienestar'])->get('/subsidio/admin', [SubsidioAlimenticioController::class, 'dashboard'])->name('subsidio.admin.dashboard');

Route::middleware(['auth','checkrole:AdminBienestar'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/subsidio', [SubsidioAlimenticioController::class, 'dashboard'])->name('subsidio.admin.dashboard');

    Route::get('/estudiantes', [AdminEstudiantesController::class, 'index'])->name('estudiantes');
    Route::prefix('estudiantes')->as('estudiantes.')->group(function () {
        Route::get('/{user}', [AdminEstudiantesController::class, 'show'])->name('show');
        Route::post('/{user}/observaciones', [AdminEstudiantesController::class, 'storeObservacion'])->name('observaciones.store');
        Route::delete('/{user}/observaciones/{observacion}', [AdminEstudiantesController::class, 'destroyObservacion'])->name('observaciones.destroy');
    });

    Route::resource('/convocatorias-subsidio', \App\Http\Controllers\ConvocatoriaSubsidioController::class)->names('convocatorias-subsidio');
    Route::get('/convocatorias', fn() => redirect()->route('admin.convocatorias-subsidio.index'))->name('convocatorias');

    Route::prefix('convocatorias-subsidio')->as('convocatorias-subsidio.')->group(function () {
        Route::get('/{convocatoria}/postulaciones', [\App\Http\Controllers\AdminPostulacionSubsidioController::class, 'index'])->name('postulaciones.index');
        Route::get('/postulaciones/{postulacion}', [\App\Http\Controllers\AdminPostulacionSubsidioController::class, 'show'])->name('postulaciones.show');
        Route::post('/postulaciones/{postulacion}/estado', [\App\Http\Controllers\AdminPostulacionSubsidioController::class, 'updateEstado'])->name('postulaciones.estado');
        Route::get('/postulaciones/{postulacion}/pdf', [\App\Http\Controllers\AdminPostulacionSubsidioController::class, 'download'])->name('postulaciones.pdf');
        Route::post('/postulaciones/{postulacion}/recalcular-prioridad', [\App\Http\Controllers\AdminPostulacionSubsidioController::class, 'recalcularPrioridad'])->name('postulaciones.recalcular');
        Route::post('/postulaciones/{postulacion}/prioridad-manual', [\App\Http\Controllers\AdminPostulacionSubsidioController::class, 'updatePrioridadManual'])->name('postulaciones.prioridad-manual');
    });

    Route::get('/cupos', [AdminCuposController::class, 'index'])->name('cupos.index');
    Route::post('/cupos/generar-periodo', [AdminCuposController::class, 'generarPeriodo'])->name('cupos.generar-periodo');
    Route::post('/cupos/planificar-periodo', [AdminCuposController::class, 'planificarPeriodo'])->name('cupos.planificar-periodo');
    Route::get('/cupos/exportar-semana', [AdminCuposController::class, 'exportarSemana'])->name('cupos.exportar-semana');
    Route::get('/cupos/reporte-excel', [AdminCuposController::class, 'exportarReporteSemanaExcel'])->name('cupos.reporte-excel');
    Route::get('/cupos/reporte', [AdminCuposController::class, 'reporteSemana'])->name('cupos.reporte-semana');

    Route::get('/asistencias/semanal/export', [\App\Http\Controllers\AdminAsistenciasController::class,'exportSemanalExcel'])->name('asistencias.semanal.export');
    Route::get('/asistencias/mensual/export', [\App\Http\Controllers\AdminAsistenciasController::class,'exportMensualExcel'])->name('asistencias.mensual.export');

    Route::post('/cupos/auto-asignar-semana', [AdminCuposController::class, 'autoAsignarSemana'])->name('cupos.auto-asignar-semana');
    Route::post('/cupos/generar-plantilla', [AdminCuposController::class, 'generarPlantillaSemana'])->name('cupos.generar-plantilla');
    Route::post('/cupos/aplicar-plantilla', [AdminCuposController::class, 'aplicarPlantillaPeriodo'])->name('cupos.aplicar-plantilla');

    Route::get('/cupos/dia', [AdminCuposController::class, 'dia'])->name('cupos.dia');
    Route::post('/cupos/dia/capacidad', [AdminCuposController::class, 'actualizarCapacidadDia'])->name('cupos.dia.capacidad');
    Route::post('/cupos/dia/asignar', [AdminCuposController::class, 'asignarManual'])->name('cupos.dia.asignar');
    Route::delete('/cupos/asignacion/{asignacion}', [AdminCuposController::class, 'desasignarManual'])->name('cupos.asignacion.eliminar');
    Route::post('/cupos/dia/auto-asignar', [AdminCuposController::class, 'autoAsignarDia'])->name('cupos.dia.auto-asignar');

    Route::get('/asistencias', [\App\Http\Controllers\AdminAsistenciasController::class, 'index'])->name('asistencias.index');
    Route::get('/asistencias/diario', [\App\Http\Controllers\AdminAsistenciasController::class, 'diario'])->name('asistencias.diario');
    Route::get('/asistencias/semanal', [\App\Http\Controllers\AdminAsistenciasController::class, 'semanal'])->name('asistencias.semanal');
    Route::get('/asistencias/mensual', [\App\Http\Controllers\AdminAsistenciasController::class, 'mensual'])->name('asistencias.mensual');
    Route::get('/asistencias/cancelaciones', [\App\Http\Controllers\AdminAsistenciasController::class, 'cancelaciones'])->name('asistencias.cancelaciones');

    Route::get('/reportes', [AdminReportesController::class, 'index'])->name('reportes');
    Route::get('/reportes/{reporte}', [AdminReportesController::class, 'show'])->name('reportes.show');
    Route::post('/reportes/{reporte}/estado', [AdminReportesController::class, 'updateEstado'])->name('reportes.estado');

    Route::get('/cupos/dia/default', [AdminCuposController::class, 'diaDefault'])->name('cupos.dia.default');
    Route::post('/cupos/reponer-ofertas', [AdminCuposController::class, 'reponerOfertas'])->name('cupos.reponer-ofertas');
});

// Admin: restaurantes
Route::middleware(['auth','checkrole:AdminBienestar'])->prefix('admin/restaurantes')->as('admin.restaurantes.')->group(function () {
    Route::get('/', [AdminRestaurantesController::class, 'index'])->name('index');
    Route::post('/', [AdminRestaurantesController::class, 'store'])->name('store');
    Route::post('/{restaurante}/attach', [AdminRestaurantesController::class, 'attachUser'])->name('attach');
    Route::delete('/{restaurante}/detach', [AdminRestaurantesController::class, 'detachUser'])->name('detach');
    Route::delete('/{restaurante}', [AdminRestaurantesController::class, 'destroy'])->name('destroy');
});

// Admin: Standby registros
Route::middleware(['auth','checkrole:AdminBienestar'])->prefix('admin/standby')->as('admin.standby.')->group(function () {
    Route::get('/', [AdminStandbyController::class, 'index'])->name('index');
    Route::get('/create', [AdminStandbyController::class,'create'])->name('create');
    Route::post('/', [AdminStandbyController::class,'store'])->name('store');
    Route::get('/{registro}/edit', [AdminStandbyController::class,'edit'])->name('edit');
    Route::put('/{registro}', [AdminStandbyController::class,'update'])->name('update');
    Route::delete('/{registro}', [AdminStandbyController::class,'destroy'])->name('destroy');
    Route::patch('/{registro}/activo', [AdminStandbyController::class,'toggleActivo'])->name('toggle-activo');
    Route::post('/ofertas/claim/{cupo}', [\App\Http\Controllers\PWA\StandbyInboxController::class, 'claimCupo'])->name('ofertas.claim')->middleware('throttle:60,1');
});

// Estudiante (Subsidio)
Route::middleware(['auth','checkrole:Estudiante'])->group(function () {
    Route::get('/subsidio/convocatorias', [EstudianteConvocatoriaController::class, 'index'])->name('subsidio.convocatorias.index');
    Route::get('/subsidio/convocatorias/{convocatoria}/postular', [PostulacionSubsidioController::class, 'create'])->name('subsidio.postulacion.create');
    Route::post('/subsidio/convocatorias/{convocatoria}/postular', [PostulacionSubsidioController::class, 'store'])->name('subsidio.postulacion.store');
    Route::get('/subsidio/convocatorias/{convocatoria}/gracias', [PostulacionSubsidioController::class, 'gracias'])->name('subsidio.postulacion.gracias');

    Route::get('/subsidio/mis-postulaciones', [EstudiantePostulacionController::class, 'index'])->name('subsidio.postulaciones.index');
    Route::get('/subsidio/postulaciones/{postulacion}', [EstudiantePostulacionController::class, 'show'])->name('subsidio.postulaciones.show');
    Route::get('/subsidio/postulaciones/{postulacion}/pdf', [EstudiantePostulacionController::class, 'download'])->name('subsidio.postulaciones.pdf');
});

// PWA Estudiante
Route::middleware(['auth','checkrole:Estudiante'])->prefix('app/subsidio')->name('app.subsidio.')->group(function () {
    Route::get('/mis-cupos', [SubsidioEstudianteController::class, 'misCupos'])->name('mis-cupos');
    Route::post('/cancelar',  [SubsidioEstudianteController::class, 'cancelar'])->name('cancelar');
    Route::post('/deshacer',  [SubsidioEstudianteController::class, 'deshacer'])->name('deshacer');

    Route::get('/reportes', [ReportesEstudianteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/nuevo', [ReportesEstudianteController::class, 'create'])->name('reportes.create');
    Route::post('/reportes', [ReportesEstudianteController::class, 'store'])->name('reportes.store');
    Route::get('/reportes/{reporte}', [ReportesEstudianteController::class, 'show'])->name('reportes.show');

    Route::get('/standby', [PwaStandbyController::class, 'index'])->name('standby');
    Route::post('/standby', [PwaStandbyController::class, 'save'])->name('standby.save');

    Route::get('/ping', fn() => 'ok')->name('ping');
});

// PWA Estudiante — ofertas
Route::middleware(['auth','checkrole:Estudiante'])->prefix('app/subsidio')->as('app.subsidio.')->group(function () {
    Route::get('/ofertas', [\App\Http\Controllers\PWA\StandbyInboxController::class, 'index'])->name('ofertas.index');
    Route::post('/ofertas/{oferta}/aceptar', [\App\Http\Controllers\PWA\StandbyInboxController::class, 'accept'])->name('ofertas.accept')->middleware('throttle:20,1');
    Route::post('/ofertas/{oferta}/rechazar', [\App\Http\Controllers\PWA\StandbyInboxController::class, 'decline'])->name('ofertas.decline')->middleware('throttle:20,1');
    Route::post('/ofertas/claim/{cupo}', [\App\Http\Controllers\PWA\StandbyInboxController::class, 'claimCupo'])->name('ofertas.claim')->middleware('throttle:60,1');
});

// PWA Restaurante (grupo principal)
Route::middleware(['auth','checkrole:Restaurante'])->prefix('app/restaurantes')->group(function () {
    Route::get('/', [RestaurantesDashboardController::class, 'index'])->name('restaurantes.dashboard');
    Route::post('/contexto', [RestaurantesDashboardController::class, 'setContext'])->name('restaurantes.context.set');

    Route::get('/asistencias', [RestAsistenciasController::class,'hoy'])->name('restaurantes.asistencias.hoy');
    Route::post('/asistencias/{asignacion}/marcar', [RestAsistenciasController::class,'marcar'])->name('restaurantes.asistencias.marcar');

    Route::get('/asistencias/fecha', [RestAsistenciasController::class,'fecha'])->name('restaurantes.asistencias.fecha');
    Route::post('/asistencias/{asignacion}/marcar-fecha', [RestAsistenciasController::class,'marcarFecha'])->name('restaurantes.asistencias.marcar-fecha');

    Route::get('/asistencias/semana', [RestAsistenciasController::class,'semana'])->name('restaurantes.asistencias.semana');
    Route::get('/asistencias/semana/export', [RestAsistenciasController::class,'exportSemana'])->name('restaurantes.asistencias.semana.export');

    Route::get('/asistencias/mes', [RestAsistenciasController::class,'mes'])->name('restaurantes.asistencias.mes');
    Route::get('/asistencias/mes/export', [RestAsistenciasController::class,'exportMes'])->name('restaurantes.asistencias.mes.export');

    Route::post('/asistencias/cerrar-dia', [RestAsistenciasController::class,'cerrarDia'])->name('restaurantes.asistencias.cerrar-dia');
    Route::post('/asistencias/cerrar-semana', [RestAsistenciasController::class,'cerrarSemana'])->name('restaurantes.asistencias.cerrar-semana');
    Route::post('/asistencias/cerrar-mes', [RestAsistenciasController::class,'cerrarMes'])->name('restaurantes.asistencias.cerrar-mes');

    Route::post('/asistencias/festivo', [RestAsistenciasController::class,'marcarFestivo'])->name('restaurantes.asistencias.festivo');

    // Redirect trailing slash correcto dentro del prefijo
    Route::redirect('/', '/app/restaurantes', 301);
});

// PWA Restaurante — Reportes
Route::middleware(['auth','checkrole:Restaurante'])->prefix('app/restaurante')->as('app.restaurante.')->group(function () {
    Route::get('/reportes', [ReportesRestaurantesController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/nuevo', [ReportesRestaurantesController::class, 'create'])->name('reportes.create');
    Route::post('/reportes', [ReportesRestaurantesController::class, 'store'])->name('reportes.store');
    Route::get('/reportes/{reporte}', [ReportesRestaurantesController::class, 'show'])->name('reportes.show');
});

// Standby oferta pública (rate limited)
Route::middleware('throttle:30,1')->get('/standby/oferta/aceptar', [StandbyOfferController::class, 'aceptar'])->name('standby.oferta.aceptar');

// Admin Bienestar: reponer ofertas (alias)
Route::middleware(['auth','checkrole:AdminBienestar'])->prefix('admin')->as('admin.')->group(function () {
    Route::post('/cupos/reponer-ofertas', [AdminCuposController::class, 'reponerOfertas'])->name('cupos.reponer-ofertas');
});