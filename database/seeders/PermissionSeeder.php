<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\NavigationItem;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'slug' => 'empleados',
                'name' => 'Empleados',
                'description' => 'Administración del catálogo de empleados.',
                'active' => true,
            ],
            [
                'slug' => 'incidencias',
                'name' => 'Incidencias',
                'description' => 'Módulo de incidencias y autorizaciones.',
                'active' => true,
            ],
            [
                'slug' => 'puntualidad',
                'name' => 'Puntualidad',
                'description' => 'Revisión de retrasos, llegadas tempranas y salidas.',
                'active' => true,
            ],
            [
                'slug' => 'asistencias',
                'name' => 'Asistencias',
                'description' => 'Consulta y exportación de asistencias.',
                'active' => true,
            ],
            [
                'slug' => 'academia',
                'name' => 'Academia',
                'description' => 'Módulo académico y ciclos.',
                'active' => true,
            ],
            [
                'slug' => 'dispositivos',
                'name' => 'Dispositivos',
                'description' => 'Administración de checadores y sincronización.',
                'active' => true,
            ],
        ];

        foreach ($modules as $moduleData) {
            $module = Module::query()->firstOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );

            $definitions = [
                'empleados' => [
                    ['slug' => 'empleados.view', 'action' => 'view', 'name' => 'Ver empleados'],
                    ['slug' => 'empleados.create', 'action' => 'create', 'name' => 'Crear empleados'],
                    ['slug' => 'empleados.update', 'action' => 'update', 'name' => 'Actualizar empleados'],
                    ['slug' => 'empleados.delete', 'action' => 'delete', 'name' => 'Eliminar empleados'],
                ],
                'incidencias' => [
                    ['slug' => 'incidencias.view', 'action' => 'view', 'name' => 'Ver incidencias'],
                    ['slug' => 'incidencias.create', 'action' => 'create', 'name' => 'Crear incidencias'],
                    ['slug' => 'incidencias.update', 'action' => 'update', 'name' => 'Actualizar incidencias'],
                    ['slug' => 'incidencias.approve', 'action' => 'approve', 'name' => 'Autorizar incidencias'],
                ],
                'puntualidad' => [
                    ['slug' => 'puntualidad.view', 'action' => 'view', 'name' => 'Ver puntualidad'],
                    ['slug' => 'puntualidad.export', 'action' => 'export', 'name' => 'Exportar puntualidad'],
                ],
                'asistencias' => [
                    ['slug' => 'asistencias.view', 'action' => 'view', 'name' => 'Ver asistencias'],
                    ['slug' => 'asistencias.export', 'action' => 'export', 'name' => 'Exportar asistencias'],
                ],
                'academia' => [
                    ['slug' => 'academia.view', 'action' => 'view', 'name' => 'Ver academia'],
                    ['slug' => 'academia.create', 'action' => 'create', 'name' => 'Crear registros académicos'],
                    ['slug' => 'academia.update', 'action' => 'update', 'name' => 'Actualizar registros académicos'],
                    ['slug' => 'academia.delete', 'action' => 'delete', 'name' => 'Eliminar registros académicos'],
                ],
                'dispositivos' => [
                    ['slug' => 'dispositivos.view', 'action' => 'view', 'name' => 'Ver dispositivos'],
                    ['slug' => 'dispositivos.sync', 'action' => 'sync', 'name' => 'Sincronizar dispositivos'],
                ],
            ];

            foreach ($definitions[$module->slug] ?? [] as $permissionData) {
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

        $groupDefinitions = [
            'Administrador' => [
                'description' => 'Acceso completo al sistema.',
                'permissions' => Permission::query()->pluck('id'),
            ],
            'Empleado' => [
                'description' => 'Consulta y registro de incidencias propias.',
                'permissions' => Permission::query()
                    ->whereIn('slug', ['incidencias.view', 'incidencias.create'])
                    ->pluck('id'),
            ],
            'Jefe de área' => [
                'description' => 'Consulta y autorización de incidencias de su área.',
                'permissions' => Permission::query()
                    ->whereIn('slug', ['incidencias.view', 'incidencias.approve'])
                    ->pluck('id'),
            ],
            'Profesor' => [
                'description' => 'Consulta y registro de incidencias propias.',
                'permissions' => Permission::query()
                    ->whereIn('slug', ['incidencias.view', 'incidencias.create'])
                    ->pluck('id'),
            ],
        ];

        foreach ($groupDefinitions as $name => $definition) {
            $group = PermissionGroup::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $definition['description'], 'is_default' => true]
            );
            $group->permissions()->sync($definition['permissions']);
        }

        $moduleIds = Module::query()->pluck('id', 'slug');
        $navigation = [
            ['section' => 'Módulos', 'label' => 'Panel de control', 'route_name' => 'dashboard', 'icon' => 'bi-speedometer2', 'module' => null, 'order' => 10],
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
            ['section' => 'Administración', 'label' => 'Cola de sincronización', 'route_name' => 'operations.queue', 'icon' => 'bi-list-task', 'module' => null, 'order' => 60, 'admin_only' => true],
            ['section' => 'Administración', 'label' => 'Navegación', 'route_name' => 'navigation-items.index', 'icon' => 'bi-list-ul', 'module' => null, 'order' => 70, 'admin_only' => true],
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
    }
}
