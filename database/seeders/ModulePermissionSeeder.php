<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\NavigationItem;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class ModulePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define all 23 modules with their standard actions
        $modules = [
            [
                'slug' => 'dashboard',
                'name' => 'Panel de control',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'dashboard.view', 'action' => 'view', 'name' => 'Ver panel'],
                ],
            ],
            [
                'slug' => 'dispositivos',
                'name' => 'Dispositivos',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'dispositivos.view', 'action' => 'view', 'name' => 'Ver dispositivos'],
                    ['slug' => 'dispositivos.sync', 'action' => 'sync', 'name' => 'Sincronizar dispositivos'],
                    ['slug' => 'dispositivos.create', 'action' => 'create', 'name' => 'Crear dispositivo'],
                    ['slug' => 'dispositivos.update', 'action' => 'update', 'name' => 'Actualizar dispositivo'],
                    ['slug' => 'dispositivos.delete', 'action' => 'delete', 'name' => 'Eliminar dispositivo'],
                ],
            ],
            [
                'slug' => 'empleados',
                'name' => 'Empleados',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'empleados.view', 'action' => 'view', 'name' => 'Ver empleados'],
                    ['slug' => 'empleados.create', 'action' => 'create', 'name' => 'Crear empleado'],
                    ['slug' => 'empleados.update', 'action' => 'update', 'name' => 'Actualizar empleado'],
                    ['slug' => 'empleados.delete', 'action' => 'delete', 'name' => 'Eliminar empleado'],
                    ['slug' => 'empleados.enroll', 'action' => 'enroll', 'name' => 'Inscribir empleado'],
                    ['slug' => 'empleados.sync', 'action' => 'sync', 'name' => 'Sincronizar empleados'],
                ],
            ],
            [
                'slug' => 'huellas',
                'name' => 'Huellas',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'huellas.view', 'action' => 'view', 'name' => 'Ver huellas'],
                    ['slug' => 'huellas.create', 'action' => 'create', 'name' => 'Registrar huella'],
                    ['slug' => 'huellas.delete', 'action' => 'delete', 'name' => 'Eliminar huella'],
                ],
            ],
            [
                'slug' => 'asistencias',
                'name' => 'Asistencias',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'asistencias.view', 'action' => 'view', 'name' => 'Ver asistencias'],
                    ['slug' => 'asistencias.export', 'action' => 'export', 'name' => 'Exportar asistencias'],
                    ['slug' => 'asistencias.print', 'action' => 'print', 'name' => 'Imprimir asistencias'],
                ],
            ],
            [
                'slug' => 'puntualidad',
                'name' => 'Puntualidad',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'puntualidad.view', 'action' => 'view', 'name' => 'Ver puntualidad'],
                    ['slug' => 'puntualidad.export', 'action' => 'export', 'name' => 'Exportar puntualidad'],
                    ['slug' => 'puntualidad.approve', 'action' => 'approve', 'name' => 'Autorizar puntualidad'],
                ],
            ],
            [
                'slug' => 'incidencias',
                'name' => 'Incidencias',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'incidencias.view', 'action' => 'view', 'name' => 'Ver incidencias'],
                    ['slug' => 'incidencias.create', 'action' => 'create', 'name' => 'Crear incidencia'],
                    ['slug' => 'incidencias.update', 'action' => 'update', 'name' => 'Actualizar incidencia'],
                    ['slug' => 'incidencias.approve', 'action' => 'approve', 'name' => 'Autorizar incidencia'],
                ],
            ],
            [
                'slug' => 'notificaciones',
                'name' => 'Notificaciones',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'notificaciones.view', 'action' => 'view', 'name' => 'Ver notificaciones'],
                ],
            ],
            [
                'slug' => 'sync-queue',
                'name' => 'Cola de sincronización',
                'group_name' => 'Módulos',
                'actions' => [
                    ['slug' => 'sync-queue.view', 'action' => 'view', 'name' => 'Ver cola sincronización'],
                    ['slug' => 'sync-queue.manage', 'action' => 'manage', 'name' => 'Gestionar cola'],
                ],
            ],
            // Academia submódulos (separados por DEC-001)
            [
                'slug' => 'academia',
                'name' => 'Academia',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.view', 'action' => 'view', 'name' => 'Ver academia'],
                ],
            ],
            [
                'slug' => 'academia.ciclos',
                'name' => 'Ciclos Escolares',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.ciclos.view', 'action' => 'view', 'name' => 'Ver ciclos'],
                    ['slug' => 'academia.ciclos.create', 'action' => 'create', 'name' => 'Crear ciclo'],
                    ['slug' => 'academia.ciclos.update', 'action' => 'update', 'name' => 'Actualizar ciclo'],
                    ['slug' => 'academia.ciclos.delete', 'action' => 'delete', 'name' => 'Eliminar ciclo'],
                    ['slug' => 'academia.ciclos.activo', 'action' => 'activo', 'name' => 'Marcar activo'],
                ],
            ],
            [
                'slug' => 'academia.grupos',
                'name' => 'Grupos',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.grupos.view', 'action' => 'view', 'name' => 'Ver grupos'],
                    ['slug' => 'academia.grupos.asistencia', 'action' => 'asistencia', 'name' => 'Asistencia grupos'],
                ],
            ],
            [
                'slug' => 'academia.alumnos',
                'name' => 'Alumnos',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.alumnos.view', 'action' => 'view', 'name' => 'Ver alumnos'],
                    ['slug' => 'academia.alumnos.kardex', 'action' => 'kardex', 'name' => 'Kardex'],
                    ['slug' => 'academia.alumnos.historial', 'action' => 'historial', 'name' => 'Historial'],
                ],
            ],
            [
                'slug' => 'academia.profesores',
                'name' => 'Profesores',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.profesores.view', 'action' => 'view', 'name' => 'Ver profesores'],
                    ['slug' => 'academia.profesores.horario', 'action' => 'horario', 'name' => 'Horario'],
                    ['slug' => 'academia.profesores.usuario', 'action' => 'usuario', 'name' => 'Usuario'],
                ],
            ],
            [
                'slug' => 'academia.horarios',
                'name' => 'Horarios',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.horarios.view', 'action' => 'view', 'name' => 'Ver horarios'],
                ],
            ],
            [
                'slug' => 'academia.cursos',
                'name' => 'Cursos',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.cursos.view', 'action' => 'view', 'name' => 'Ver cursos'],
                    ['slug' => 'academia.cursos.create', 'action' => 'create', 'name' => 'Crear curso'],
                    ['slug' => 'academia.cursos.update', 'action' => 'update', 'name' => 'Actualizar curso'],
                    ['slug' => 'academia.cursos.delete', 'action' => 'delete', 'name' => 'Eliminar curso'],
                    ['slug' => 'academia.cursos.materia', 'action' => 'materia', 'name' => 'Materia'],
                ],
            ],
            [
                'slug' => 'academia.planes',
                'name' => 'Planes de Estudio',
                'group_name' => 'Academia',
                'actions' => [
                    ['slug' => 'academia.planes.view', 'action' => 'view', 'name' => 'Ver planes'],
                    ['slug' => 'academia.planes.create', 'action' => 'create', 'name' => 'Crear plan'],
                    ['slug' => 'academia.planes.update', 'action' => 'update', 'name' => 'Actualizar plan'],
                    ['slug' => 'academia.planes.delete', 'action' => 'delete', 'name' => 'Eliminar plan'],
                ],
            ],
            // Administración módulos
            [
                'slug' => 'areas',
                'name' => 'Áreas',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'areas.view', 'action' => 'view', 'name' => 'Ver áreas'],
                    ['slug' => 'areas.create', 'action' => 'create', 'name' => 'Crear área'],
                    ['slug' => 'areas.update', 'action' => 'update', 'name' => 'Actualizar área'],
                    ['slug' => 'areas.delete', 'action' => 'delete', 'name' => 'Eliminar área'],
                ],
            ],
            [
                'slug' => 'puestos',
                'name' => 'Puestos',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'puestos.view', 'action' => 'view', 'name' => 'Ver puestos'],
                    ['slug' => 'puestos.create', 'action' => 'create', 'name' => 'Crear puesto'],
                    ['slug' => 'puestos.update', 'action' => 'update', 'name' => 'Actualizar puesto'],
                    ['slug' => 'puestos.delete', 'action' => 'delete', 'name' => 'Eliminar puesto'],
                ],
            ],
            [
                'slug' => 'permission-groups',
                'name' => 'Grupos de permisos',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'permission-groups.view', 'action' => 'view', 'name' => 'Ver grupos'],
                    ['slug' => 'permission-groups.create', 'action' => 'create', 'name' => 'Crear grupo'],
                    ['slug' => 'permission-groups.update', 'action' => 'update', 'name' => 'Actualizar grupo'],
                    ['slug' => 'permission-groups.delete', 'action' => 'delete', 'name' => 'Eliminar grupo'],
                    ['slug' => 'permission-groups.assign', 'action' => 'assign', 'name' => 'Asignar permisos'],
                ],
            ],
            [
                'slug' => 'permissions',
                'name' => 'Permisos',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'permissions.view', 'action' => 'view', 'name' => 'Ver permisos'],
                    ['slug' => 'permissions.create', 'action' => 'create', 'name' => 'Crear permiso'],
                    ['slug' => 'permissions.update', 'action' => 'update', 'name' => 'Actualizar permiso'],
                    ['slug' => 'permissions.delete', 'action' => 'delete', 'name' => 'Eliminar permiso'],
                    ['slug' => 'permissions.assign', 'action' => 'assign', 'name' => 'Asignar permiso'],
                ],
            ],
            [
                'slug' => 'firebird',
                'name' => 'Sincronizar Firebird',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'firebird.view', 'action' => 'view', 'name' => 'Ver Firebird'],
                    ['slug' => 'firebird.sync', 'action' => 'sync', 'name' => 'Sincronizar'],
                    ['slug' => 'firebird.execute', 'action' => 'execute', 'name' => 'Ejecutar'],
                    ['slug' => 'firebird.cancel', 'action' => 'cancel', 'name' => 'Cancelar'],
                    ['slug' => 'firebird.retry', 'action' => 'retry', 'name' => 'Reintentar'],
                ],
            ],
            [
                'slug' => 'navigation',
                'name' => 'Navegación',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'navigation.view', 'action' => 'view', 'name' => 'Ver navegación'],
                    ['slug' => 'navigation.create', 'action' => 'create', 'name' => 'Crear navegación'],
                    ['slug' => 'navigation.update', 'action' => 'update', 'name' => 'Actualizar navegación'],
                    ['slug' => 'navigation.delete', 'action' => 'delete', 'name' => 'Eliminar navegación'],
                ],
            ],
            [
                'slug' => 'usuarios',
                'name' => 'Usuarios',
                'group_name' => 'Administración',
                'actions' => [
                    ['slug' => 'usuarios.view', 'action' => 'view', 'name' => 'Ver usuarios'],
                    ['slug' => 'usuarios.create', 'action' => 'create', 'name' => 'Crear usuario'],
                    ['slug' => 'usuarios.update', 'action' => 'update', 'name' => 'Actualizar usuario'],
                    ['slug' => 'usuarios.delete', 'action' => 'delete', 'name' => 'Eliminar usuario'],
                    ['slug' => 'usuarios.reset-password', 'action' => 'reset-password', 'name' => 'Resetear contraseña'],
                ],
            ],
        ];

        // Create modules and their permissions
        foreach ($modules as $moduleData) {
            // Remove 'actions' key before creating module, as it's not a module column
            $moduleAttributes = [
                'slug' => $moduleData['slug'],
                'name' => $moduleData['name'],
                'group_name' => $moduleData['group_name'],
                'is_system' => true,
                'sort_order' => 0,
            ];

            $module = Module::query()->firstOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleAttributes
            );

            // Create permissions for this module
            foreach ($moduleData['actions'] as $permissionData) {
                Permission::query()->firstOrCreate(
                    ['module_id' => $module->id, 'slug' => $permissionData['slug']],
                    [
                        'module_id' => $module->id,
                        'slug' => $permissionData['slug'],
                        'action' => $permissionData['action'],
                        'name' => $permissionData['name'],
                    ]
                );
            }
        }

        // Update NavigationItem records with correct module_id
        $moduleIds = Module::query()->pluck('id', 'slug');

        $navigation = [
            ['section' => 'Módulos', 'label' => 'Panel de control', 'route_name' => 'dashboard', 'icon' => 'bi-speedometer2', 'module' => 'dashboard', 'order' => 10],
            ['section' => 'Módulos', 'label' => 'Dispositivos', 'route_name' => 'devices.index', 'icon' => 'bi-hdd-network', 'module' => 'dispositivos', 'order' => 20],
            ['section' => 'Módulos', 'label' => 'Empleados', 'route_name' => 'employees.index', 'icon' => 'bi-people', 'module' => 'empleados', 'order' => 30],
            ['section' => 'Módulos', 'label' => 'Huellas', 'route_name' => 'fingerprints.index', 'icon' => 'bi-fingerprint', 'module' => 'empleados', 'order' => 40],
            ['section' => 'Módulos', 'label' => 'Asistencias', 'route_name' => 'attendances.index', 'icon' => 'bi-calendar-check', 'module' => 'asistencias', 'order' => 50],
            ['section' => 'Módulos', 'label' => 'Puntualidad', 'route_name' => 'puntualidad.index', 'icon' => 'bi-alarm', 'module' => 'puntualidad', 'order' => 60],
            ['section' => 'Módulos', 'label' => 'Incidencias', 'route_name' => 'incidencias.index', 'icon' => 'bi-exclamation-triangle', 'module' => 'incidencias', 'order' => 70],
            ['section' => 'Módulos', 'label' => 'Notificaciones', 'route_name' => 'operations.notifications', 'icon' => 'bi-bell', 'module' => null, 'order' => 80],
            ['section' => 'Academia', 'label' => 'Dashboard Académico', 'route_name' => 'academia.dashboard', 'icon' => 'bi-mortarboard', 'module' => 'academia', 'order' => 10],
            ['section' => 'Academia', 'label' => 'Ciclos Escolares', 'route_name' => 'academia.ciclos.index', 'icon' => 'bi-calendar-event', 'module' => 'academia', 'order' => 20],
            ['section' => 'Academia', 'label' => 'Grupos', 'route_name' => 'academia.grupos.index', 'icon' => 'bi-people', 'module' => 'academia', 'order' => 30],
            ['section' => 'Academia', 'label' => 'Alumnos', 'route_name' => 'academia.alumnos.index', 'icon' => 'bi-mortarboard', 'module' => 'academia', 'order' => 40],
            ['section' => 'Academia', 'label' => 'Profesores', 'route_name' => 'academia.profesores.index', 'icon' => 'bi-person-badge', 'module' => 'academia', 'order' => 50],
            ['section' => 'Academia', 'label' => 'Horarios', 'route_name' => 'academia.horarios.clase', 'icon' => 'bi-calendar-week', 'module' => 'academia', 'order' => 60],
            ['section' => 'Academia', 'label' => 'Cursos', 'route_name' => 'academia.cursos.index', 'icon' => 'bi-book', 'module' => 'academia', 'order' => 70],
            ['section' => 'Academia', 'label' => 'Planes de Estudio', 'route_name' => 'academia.planes.index', 'icon' => 'bi-journal-bookmark', 'module' => 'academia', 'order' => 80],
            ['section' => 'Administración', 'label' => 'Áreas', 'route_name' => 'areas.index', 'icon' => 'bi-diagram-3', 'module' => null, 'order' => 10, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Puestos', 'route_name' => 'puestos.index', 'icon' => 'bi-briefcase', 'module' => null, 'order' => 20, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Grupos de permisos', 'route_name' => 'permission-groups.index', 'icon' => 'bi-shield-check', 'module' => null, 'order' => 30, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Permisos', 'route_name' => 'permissions.index', 'icon' => 'bi-key', 'module' => null, 'order' => 40, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Sincronizar Firebird', 'route_name' => 'firebird.index', 'icon' => 'bi-cloud-download', 'module' => null, 'order' => 50, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Cola de sincronización', 'route_name' => 'operations.queue', 'icon' => 'bi-list-task', 'module' => 'sync-queue', 'order' => 60, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Navegación', 'route_name' => 'navigation-items.index', 'icon' => 'bi-list-ul', 'module' => null, 'order' => 70, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Usuarios', 'route_name' => 'preferencia.usuarios.index', 'icon' => 'bi-person-gear', 'module' => 'usuarios', 'order' => 80, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Catálogo de módulos', 'route_name' => 'modules.index', 'icon' => 'bi-grid-3x3-gap', 'module' => null, 'order' => 90, 'admin_only' => true],
        ];

        foreach ($navigation as $item) {
            NavigationItem::query()->updateOrCreate(
                ['route_name' => $item['route_name']],
                [
                    'module_id' => $item['module'] ? ($moduleIds[$item['module']] ?? null) : null,
                    'section' => $item['section'],
                    'label' => $item['label'],
                    'icon' => $item['icon'],
                    'permission_action' => 'view',
                    'sort_order' => $item['order'],
                    'admin_only' => $item['admin_only'] ?? false,
                    'active' => true,
                ]
            );
        }

        // Set sort_order for modules based on their creation order
        $sortOrderMap = [
            'dashboard' => 1,
            'dispositivos' => 2,
            'empleados' => 3,
            'huellas' => 4,
            'asistencias' => 5,
            'puntualidad' => 6,
            'incidencias' => 7,
            'notificaciones' => 8,
            'sync-queue' => 9,
            'academia' => 10,
            'academia.ciclos' => 11,
            'academia.grupos' => 12,
            'academia.alumnos' => 13,
            'academia.profesores' => 14,
            'academia.horarios' => 15,
            'academia.cursos' => 16,
            'academia.planes' => 17,
            'areas' => 18,
            'puestos' => 19,
            'permission-groups' => 20,
            'permissions' => 21,
            'firebird' => 22,
            'navigation' => 23,
        ];

        foreach ($sortOrderMap as $slug => $sortOrder) {
            Module::where('slug', $slug)->update(['sort_order' => $sortOrder]);
        }

        // Assign all new permissions to Administrador group
        $adminGroup = PermissionGroup::where('name', 'Administrador')->first();
        if ($adminGroup) {
            $adminPermissions = Permission::whereIn('module_id', function ($query) {
                $query->select('id')->from('modules')->where('is_system', true);
            })->pluck('id');

            // If not enough permissions fetched, get all permissions
            if ($adminPermissions->isEmpty()) {
                $adminPermissions = Permission::all()->pluck('id');
            }

            $adminGroup->permissions()->sync($adminPermissions->toArray());
        }

        $this->command->info('ModulePermissionSeeder ejecutado: 24 módulos y permisos registrados.');
    }
}
