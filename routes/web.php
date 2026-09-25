<?php

use App\Http\Controllers\Academia\AlumnoController as AcademiaAlumnoController;
use App\Http\Controllers\Academia\ApiController as AcademiaApiController;
use App\Http\Controllers\Academia\AttendanceCaptureAssignmentController;
use App\Http\Controllers\Academia\CicloController as AcademiaCicloController;
use App\Http\Controllers\Academia\CursoController as AcademiaCursoController;
use App\Http\Controllers\Academia\DashboardController as AcademiaDashboardController;
use App\Http\Controllers\Academia\GrupoController as AcademiaGrupoController;
use App\Http\Controllers\Academia\HorarioController as AcademiaHorarioController;
use App\Http\Controllers\Academia\PlanController as AcademiaPlanController;
use App\Http\Controllers\Academia\ProfesorController as AcademiaProfesorController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceSyncController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FirebirdController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NavigationItemController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\PreferenciaUsuarioController;
use App\Http\Controllers\PuestoController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('kpis/json', [DashboardController::class, 'kpisJson'])->name('dashboard.kpisJson');

    Route::middleware('module_permission:dashboard,view')->group(function () {
        // Mantiene el acceso del panel principal bajo la nueva capa de permisos.
    });

    // Academia routes
    Route::prefix('academia')->name('academia.')
        ->middleware('module_permission:academia,view')
        ->group(function () {
            Route::get('/', [AcademiaDashboardController::class, 'index'])->name('dashboard');
            Route::get('kpis-json', [AcademiaDashboardController::class, 'kpisJson'])->name('kpisJson');
            Route::get('materias', function () {
                $ciclo = app('App\Services\CicloActualService')->resolve(request());
                $summary = app('App\Services\AcademiaDashboardService')->build($ciclo);
                return view('academia.dashboard.materias-module', [
                    'ciclo' => $ciclo,
                    'materiasCount' => $summary['kpis']['materias'] ?? 0,
                ]);
            })->name('materias.index');

            // Ciclos
            Route::resource('ciclos', AcademiaCicloController::class)->only(['create', 'store'])->middleware('module_permission:academia.ciclos,create');
            Route::resource('ciclos', AcademiaCicloController::class)->only(['edit', 'update'])->middleware('module_permission:academia.ciclos,update');
            Route::resource('ciclos', AcademiaCicloController::class)->only(['destroy'])->middleware('module_permission:academia.ciclos,delete');
            Route::resource('ciclos', AcademiaCicloController::class)->only(['index', 'show']);
            Route::post('ciclos/{ciclo}/activo', [AcademiaCicloController::class, 'setActivo'])
                ->middleware('module_permission:academia.ciclos,activo')
                ->name('ciclos.activo');

            // Grupos
            Route::resource('grupos', AcademiaGrupoController::class)->only(['index', 'show']);
            Route::get('grupos/{grupo}/asistencia', [AcademiaGrupoController::class, 'asistencia'])->name('grupos.asistencia');
            Route::post('grupos/asistencia', [AcademiaGrupoController::class, 'guardarAsistencia'])->name('grupos.asistencia.guardar');

            // Alumnos
            Route::resource('alumnos', AcademiaAlumnoController::class)->only(['index', 'show']);
            Route::get('alumnos/{alumno}/kardex', [AcademiaAlumnoController::class, 'kardex'])->name('alumnos.kardex');
            Route::get('alumnos/{alumno}/historial', [AcademiaAlumnoController::class, 'historial'])->name('alumnos.historial');

            // Profesores
            Route::resource('profesores', AcademiaProfesorController::class)
                ->only(['index', 'show'])
                ->parameters(['profesores' => 'profesor']);
            Route::get('profesores/{profesor}/horario', [AcademiaProfesorController::class, 'horario'])->name('profesores.horario');
            Route::post('profesores/{profesor}/usuario', [AcademiaProfesorController::class, 'assignUser'])
                ->middleware('module_permission:academia.profesores,usuario')
                ->name('profesores.usuario');

            // Horarios
            Route::prefix('horarios')->name('horarios.')->group(function () {
                Route::get('clase', [AcademiaHorarioController::class, 'clase'])->name('clase');
                Route::post('clase/asistencia', [AcademiaHorarioController::class, 'guardarAsistencia'])->name('clase.asistencia.guardar');
                Route::get('profesor', [AcademiaHorarioController::class, 'profesor'])->name('profesor');
                Route::get('aula', [AcademiaHorarioController::class, 'aula'])->name('aula');
                Route::get('base', [AcademiaHorarioController::class, 'base'])->name('base');
                Route::get('persona', [AcademiaHorarioController::class, 'persona'])->name('persona');
            });

            // Kardex
            Route::prefix('kardex')->name('kardex.')->group(function () {
                Route::get('/', [App\Http\Controllers\Academia\KardexController::class, 'index'])->name('index');
                Route::get('show', [App\Http\Controllers\Academia\KardexController::class, 'show'])->name('show');
                Route::get('historial', [App\Http\Controllers\Academia\KardexController::class, 'historial'])->name('historial');
                Route::get('print', [App\Http\Controllers\Academia\KardexController::class, 'print'])->name('print');
            });

            // Cursos
            Route::resource('cursos', AcademiaCursoController::class)->only(['create', 'store'])->middleware('module_permission:academia.cursos,create');
            Route::resource('cursos', AcademiaCursoController::class)->only(['edit', 'update'])->middleware('module_permission:academia.cursos,update');
            Route::resource('cursos', AcademiaCursoController::class)->only(['destroy'])->middleware('module_permission:academia.cursos,delete');
            Route::resource('cursos', AcademiaCursoController::class)->only(['index', 'show']);
            Route::post('cursos/{curso}/materia', [AcademiaCursoController::class, 'addMateria'])
                ->middleware('module_permission:academia.cursos,materia')
                ->name('cursos.materia.add');
            Route::delete('cursos/{curso}/materia/{materia}', [AcademiaCursoController::class, 'removeMateria'])
                ->middleware('module_permission:academia.cursos,materia')
                ->name('cursos.materia.remove');

            // Planes
            Route::resource('planes', AcademiaPlanController::class)
                ->only(['create', 'store'])
                ->middleware('module_permission:academia.planes,create')
                ->parameters(['planes' => 'plan']);
            Route::resource('planes', AcademiaPlanController::class)
                ->only(['edit', 'update'])
                ->middleware('module_permission:academia.planes,update')
                ->parameters(['planes' => 'plan']);
            Route::resource('planes', AcademiaPlanController::class)
                ->only(['destroy'])
                ->middleware('module_permission:academia.planes,delete')
                ->parameters(['planes' => 'plan']);
            Route::resource('planes', AcademiaPlanController::class)
                ->only(['index', 'show'])
                ->parameters(['planes' => 'plan']);
        });

    // API Routes for AJAX
    Route::prefix('api/academia')->name('api.academia.')
        ->middleware('module_permission:academia,view')
        ->group(function () {
            Route::get('grupos-por-ciclo', [AcademiaApiController::class, 'gruposPorCiclo'])->name('grupos-por-ciclo');
            Route::get('alumnos-por-grupo', [AcademiaApiController::class, 'alumnosPorGrupo'])->name('alumnos-por-grupo');
            Route::get('ciclos-disponibles', [AcademiaApiController::class, 'ciclosDisponibles'])->name('ciclos-disponibles');
            Route::get('planes-por-nivel', [AcademiaApiController::class, 'planesPorNivel'])->name('planes-por-nivel');
            Route::get('materias-por-plan', [AcademiaApiController::class, 'materiasPorPlan'])->name('materias-por-plan');
            Route::get('metodos-eval', [AcademiaApiController::class, 'metodosEval'])->name('metodos-eval');
            Route::get('niveles', [AcademiaApiController::class, 'niveles'])->name('niveles');
            Route::get('turnos', [AcademiaApiController::class, 'turnos'])->name('turnos');
            Route::get('sedes', [AcademiaApiController::class, 'sedes'])->name('sedes');
            Route::get('horario-base', [AcademiaApiController::class, 'horarioBase'])->name('horario-base');
            Route::get('grupo-detalle', [AcademiaApiController::class, 'grupoDetalle'])->name('grupo-detalle');
        });

    // Firebird Sync — orden: rutas literales antes que params para evitar captura
    Route::prefix('firebird')->name('firebird.')->group(function () {
        Route::get('/', [FirebirdController::class, 'index'])->middleware('module_permission:firebird,view')->name('index');
        Route::post('/start', [FirebirdController::class, 'startSync'])->middleware('admin')->name('start');
        Route::post('/execute-pending', [FirebirdController::class, 'executePending'])->middleware('admin')->name('execute-pending');
        Route::get('sync/{sync}', [FirebirdController::class, 'sync'])->middleware('module_permission:firebird,view')->name('sync');
        Route::get('/{sync}/status', [FirebirdController::class, 'status'])->middleware('module_permission:firebird,view')->name('status');
        Route::post('/{sync}/cancel', [FirebirdController::class, 'cancel'])->middleware('admin')->name('cancel');
        Route::post('/{sync}/retry', [FirebirdController::class, 'retry'])->middleware('admin')->name('retry');
        Route::delete('/{sync}', [FirebirdController::class, 'destroy'])->middleware('admin')->name('delete');
    });

    // Ciclo selector - AJAX endpoint
    Route::post('/academia/set-ciclo', function (\Illuminate\Http\Request $request) {
        $label = $request->input('ciclo_label');
        if ($label) {
            session(\App\Services\CicloActualService::SESSION_KEY, $label);
        } else {
            session()->forget(\App\Services\CicloActualService::SESSION_KEY);
        }

        return response()->json(['success' => true, 'ciclo' => $label]);
    })->middleware(['auth', 'module_permission:academia,view'])->name('academia.set-ciclo');

    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('/', [DeviceController::class, 'index'])->middleware('module_permission:dispositivos,view')->name('index');
        Route::get('/create', [DeviceController::class, 'create'])->middleware('module_permission:dispositivos,create')->name('create');
        Route::post('/', [DeviceController::class, 'store'])->middleware('module_permission:dispositivos,create')->name('store');
        Route::get('/{device}', [DeviceController::class, 'show'])->middleware('module_permission:dispositivos,view')->name('show');
        Route::get('/{device}/sync-status', [DeviceController::class, 'syncStatus'])->middleware('module_permission:dispositivos,view')->name('sync-status');
        Route::get('/{device}/refresh-data', [DeviceController::class, 'refreshData'])->middleware('module_permission:dispositivos,view')->name('refresh-data');
        Route::get('/{device}/progress', [DeviceController::class, 'progress'])->middleware('module_permission:dispositivos,view')->name('progress');
        Route::get('/{device}/edit', [DeviceController::class, 'edit'])->middleware('module_permission:dispositivos,update')->name('edit');
        Route::put('/{device}', [DeviceController::class, 'update'])->middleware('module_permission:dispositivos,update')->name('update');
        Route::delete('/{device}', [DeviceController::class, 'destroy'])->middleware('module_permission:dispositivos,delete')->name('destroy');

        Route::post('/{device}/check-status', [DeviceController::class, 'checkStatus'])->name('check-status');
        Route::middleware(['module_permission:dispositivos,sync', 'throttle:30,1'])->group(function () {
            Route::post('/deduplicate', [DeviceController::class, 'deduplicate'])->name('deduplicate');
            Route::post('/{device}/sync-users', [DeviceSyncController::class, 'syncUsers'])->name('sync-users');
            Route::post('/{device}/sync-fingerprints', [DeviceSyncController::class, 'syncFingerprints'])->name('sync-fingerprints');
            Route::post('/{device}/sync-attendances', [DeviceSyncController::class, 'syncAttendances'])->name('sync-attendances');
            Route::post('/{device}/sync-all', [DeviceSyncController::class, 'syncAll'])->name('sync-all');
            Route::post('/{device}/employees/{employee}/upload-fingerprints', [FingerprintController::class, 'uploadFingerprintsOnDevice'])->name('employees.upload-fingerprints');
            Route::delete('/{device}/employees/{employee}', [FingerprintController::class, 'removeFromDevice'])->name('employees.remove');
            Route::post('/{device}/set-time', [DeviceSyncController::class, 'setTime'])->name('set-time');
            Route::post('/{device}/sync-now', [DeviceController::class, 'syncNow'])->name('sync-now');
            Route::post('/{device}/clear-attendance', [DeviceSyncController::class, 'clearAttendance'])->name('clear-attendance');
            Route::post('/{device}/restore', [DeviceSyncController::class, 'restore'])->name('restore');
        });
    });

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->middleware('module_permission:empleados,view')->name('index');
        Route::get('/search', [EmployeeController::class, 'search'])->middleware('module_permission:empleados,view')->name('search');
        Route::get('/create', [EmployeeController::class, 'create'])->middleware('module_permission:empleados,create')->name('create');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('module_permission:empleados,update')->name('edit');
        Route::get('/{employee}/enrollment-diff', [EmployeeController::class, 'enrollmentDiff'])->middleware('module_permission:empleados,view')->name('enrollment-diff');
        Route::get('/{employee}/sync-progress', [EmployeeController::class, 'syncProgress'])->middleware('module_permission:empleados,view')->name('sync-progress');
        Route::middleware('auth')->group(function () {
            Route::post('/', [EmployeeController::class, 'store'])->middleware(['module_permission:empleados,create', 'throttle:30,1'])->name('store');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->middleware(['module_permission:empleados,update', 'throttle:30,1'])->name('update');
            Route::post('/{employee}/card', [EmployeeController::class, 'updateCard'])->middleware(['module_permission:empleados,update', 'throttle:30,1'])->name('update-card');
            Route::post('/{employee}/enroll-device', [EmployeeController::class, 'enrollOnDevice'])->middleware(['module_permission:empleados,update', 'throttle:30,1'])->name('enroll-device');
            Route::post('/{employee}/sync-devices', [EmployeeController::class, 'syncToDevices'])->middleware(['module_permission:empleados,update', 'throttle:30,1'])->name('sync-devices');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->middleware('module_permission:empleados,delete')->name('destroy');
            Route::post('/{employee}/fingerprints/{fingerprint}/copy', [FingerprintController::class, 'copyFingerprint'])->middleware('module_permission:empleados,enroll')->name('copy-fingerprint');
            Route::delete('/{employee}/fingerprints/{fingerprint}', [FingerprintController::class, 'deleteFingerprint'])->middleware('module_permission:empleados,enroll')->name('delete-fingerprint');
        });

        // Sobrantes: device_employee sin employee válido o con employee dado de baja
        Route::get('/sobrantes', [EmployeeController::class, 'sobrantes'])->middleware('module_permission:dispositivos,view')->name('sobrantes');
        Route::get('/sobrantes/data', [EmployeeController::class, 'sobrantesData'])->middleware('module_permission:dispositivos,view')->name('sobrantes.data');
        Route::post('/sobrantes/{deviceId}/{deviceUid}/ignore', [EmployeeController::class, 'sobrantesIgnore'])->middleware('admin')->name('sobrantes.ignore');
        Route::post('/sobrantes/{deviceId}/{deviceUid}/unignore', [EmployeeController::class, 'sobrantesUnignore'])->middleware('admin')->name('sobrantes.unignore');
        Route::post('/sobrantes/{deviceId}/{deviceUid}/{type}/remove', [EmployeeController::class, 'sobrantesRemove'])->middleware('admin')->name('sobrantes.remove');
    });

    // Preferencia - Crear usuarios con preferencia de empleado o profesor
    Route::prefix('preferencia')->name('preferencia.')->group(function () {
        Route::get('/usuarios', [PreferenciaUsuarioController::class, 'index'])->middleware('module_permission:usuarios,view')->name('usuarios.index');
        Route::get('/usuarios/crear', [PreferenciaUsuarioController::class, 'create'])->middleware('module_permission:usuarios,create')->name('usuarios.create');
        Route::post('/usuarios', [PreferenciaUsuarioController::class, 'store'])->middleware('module_permission:usuarios,create')->name('usuarios.store');
        Route::get('/usuarios/{user}/editar', [PreferenciaUsuarioController::class, 'edit'])->middleware('module_permission:usuarios,update')->name('usuarios.edit');
        Route::put('/usuarios/{user}', [PreferenciaUsuarioController::class, 'update'])->middleware('module_permission:usuarios,update')->name('usuarios.update');
        Route::get('/usuarios/{user}/captura-asistencia', [AttendanceCaptureAssignmentController::class, 'edit'])
            ->middleware('module_permission:usuarios,update')
            ->name('usuarios.captura-asistencia.edit');
        Route::put('/usuarios/{user}/captura-asistencia', [AttendanceCaptureAssignmentController::class, 'update'])
            ->middleware('module_permission:usuarios,update')
            ->name('usuarios.captura-asistencia.update');
    });

    Route::resource('areas', AreaController::class)
        ->only(['index', 'show'])
        ->parameters(['areas' => 'area'])
        ->middleware('module_permission:areas,view');
    Route::resource('areas', AreaController::class)
        ->only(['create', 'store'])
        ->parameters(['areas' => 'area'])
        ->middleware('module_permission:areas,create');
    Route::resource('areas', AreaController::class)
        ->only(['edit', 'update'])
        ->parameters(['areas' => 'area'])
        ->middleware('module_permission:areas,update');
    Route::resource('areas', AreaController::class)
        ->only(['destroy'])
        ->parameters(['areas' => 'area'])
        ->middleware('module_permission:areas,delete');
    Route::resource('puestos', PuestoController::class)
        ->only(['index', 'show'])
        ->parameters(['puestos' => 'puesto'])
        ->middleware('module_permission:puestos,view');
    Route::resource('puestos', PuestoController::class)
        ->only(['create', 'store'])
        ->parameters(['puestos' => 'puesto'])
        ->middleware('module_permission:puestos,create');
    Route::resource('puestos', PuestoController::class)
        ->only(['edit', 'update'])
        ->parameters(['puestos' => 'puesto'])
        ->middleware('module_permission:puestos,update');
    Route::resource('puestos', PuestoController::class)
        ->only(['destroy'])
        ->parameters(['puestos' => 'puesto'])
        ->middleware('module_permission:puestos,delete');

    Route::prefix('permission-groups')->name('permission-groups.')->middleware('admin')->group(function () {
        Route::get('/', [PermissionGroupController::class, 'index'])->name('index');
        Route::get('/create', [PermissionGroupController::class, 'create'])->name('create');
        Route::post('/', [PermissionGroupController::class, 'store'])->name('store');
        Route::get('/{permissionGroup}/edit', [PermissionGroupController::class, 'edit'])->name('edit');
        Route::put('/{permissionGroup}', [PermissionGroupController::class, 'update'])->name('update');
        Route::delete('/{permissionGroup}', [PermissionGroupController::class, 'destroy'])->name('destroy');
        Route::get('/{permissionGroup}/permissions', [PermissionGroupController::class, 'permissions'])->name('permissions');
        Route::post('/{permissionGroup}/permissions', [PermissionGroupController::class, 'savePermissions'])->name('savePermissions');
        Route::get('/{permissionGroup}/employees', [PermissionGroupController::class, 'assignEmployees'])->name('assign-employees');
        Route::post('/{permissionGroup}/employees', [PermissionGroupController::class, 'saveEmployees'])->name('save-employees');
        Route::get('/{permissionGroup}/profesores', [PermissionGroupController::class, 'assignProfesores'])->name('assign-profesores');
        Route::post('/{permissionGroup}/profesores', [PermissionGroupController::class, 'saveProfesores'])->name('save-profesores');
    });

    Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::put('modules/{module}', [ModuleController::class, 'update'])->name('modules.update');

    Route::prefix('permissions')->name('permissions.')->middleware('admin')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::get('/{permission}/groups', [PermissionController::class, 'assignGroups'])->name('assign-groups');
        Route::post('/{permission}/groups', [PermissionController::class, 'saveGroups'])->name('save-groups');
    });

    Route::resource('navigation-items', NavigationItemController::class)
        ->except(['show'])
        ->middleware('admin');

    Route::get('/incidencias', [IncidenciaController::class, 'index'])->middleware('module_permission:incidencias,view')->name('incidencias.index');
    Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->middleware('module_permission:incidencias,create')->name('incidencias.create');
    Route::post('/incidencias', [IncidenciaController::class, 'store'])->middleware('module_permission:incidencias,create')->name('incidencias.store');
    Route::post('/incidencias/{incidencia}/estado', [IncidenciaController::class, 'updateStatus'])->middleware('module_permission:incidencias,approve')->name('incidencias.estado');
    Route::post('/incidencias/{incidencia}/vista', [IncidenciaController::class, 'markViewed'])->middleware('module_permission:incidencias,view')->name('incidencias.vista');
    Route::post('/incidencias/{incidencia}/firmar', [IncidenciaController::class, 'sign'])->middleware('module_permission:incidencias,view')->name('incidencias.firmar');

    Route::get('/fingerprints', [EmployeeController::class, 'fingerprints'])
        ->middleware('module_permission:empleados,view')
        ->name('fingerprints.index');

    Route::get('/attendances', [AttendanceController::class, 'index'])->middleware('module_permission:asistencias,view')->name('attendances.index');
    Route::post('/attendances/{attendance}/observations', [AttendanceController::class, 'storeObservation'])->middleware('module_permission:asistencias,view')->name('attendances.observations.store');
    Route::get('/puntualidad', [App\Http\Controllers\PuntualidadController::class, 'index'])->middleware('module_permission:puntualidad,view')->name('puntualidad.index');
    Route::get('/attendances/export', [AttendanceController::class, 'export'])->middleware('module_permission:asistencias,export')->name('attendances.export');
    Route::get('/attendances/export/classes', [AttendanceController::class, 'exportClassAttendances'])->middleware('module_permission:asistencias,export')->name('attendances.export.classes');
    Route::get('/attendances/print', [AttendanceController::class, 'print'])->middleware('module_permission:asistencias,export')->name('attendances.print');
    Route::get('/sync-queue', [OperationsController::class, 'queue'])->middleware('admin')->name('operations.queue');
    Route::get('/sync-queue/data', [OperationsController::class, 'queueData'])->middleware('admin')->name('operations.queue.data');
    Route::get('/sync-queue/data-unified', [OperationsController::class, 'queueDataUnified'])->middleware('admin')->name('operations.queue.data.unified');
    Route::post('/sync-queue/{sync}/cancel', [OperationsController::class, 'cancel'])->middleware('admin')->name('operations.cancel');
    Route::post('/sync-queue/{sync}/retry', [OperationsController::class, 'retry'])->middleware('admin')->name('operations.retry');
    Route::delete('/sync-queue/{sync}', [OperationsController::class, 'delete'])->middleware('admin')->name('operations.delete');
    Route::get('/notifications', [OperationsController::class, 'notifications'])->name('operations.notifications');
});
