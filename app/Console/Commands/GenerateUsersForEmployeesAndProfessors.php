<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\Academia\Profesor;
use App\Models\Employee;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateUsersForEmployeesAndProfessors extends Command
{
    /** @var array<string, bool> */
    private array $reservedUsernames = [];

    protected $signature = 'users:generate-for-catalog {--dry-run : Simular sin guardar}';

    protected $description = 'Generar usuarios automáticamente para empleados y profesores sin asignación de usuario';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Buscando empleados y profesores sin usuario asignado...');

        // Empleados sin usuario
        $employeesWithoutUser = Employee::whereNull('auth_user_id')->get();

        // Profesores sin usuario
        $professorsWithoutUser = Profesor::whereNull('auth_user_id')->get();

        $totalToCreate = $employeesWithoutUser->count() + $professorsWithoutUser->count();

        if ($totalToCreate === 0) {
            $this->info('✅ No hay empleados ni profesores sin usuario asignado.');

            return 0;
        }

        $this->warn("Total a crear: {$totalToCreate}");

        if ($dryRun) {
            $this->line("\n<fg=yellow>MODO SIMULACIÓN (--dry-run)</> - No se guardará nada\n");
        }

        $createdEmployees = 0;
        $createdProfessors = 0;
        $usersData = [];

        // Crear usuarios para empleados
        if ($employeesWithoutUser->count() > 0) {
            $this->info("\nEMPLEADOS: {$employeesWithoutUser->count()}");

            foreach ($employeesWithoutUser as $employee) {
                $username = $this->generateUsername($employee->name);
                $password = 'UTE'.($employee->numero_empleado ?: $employee->user_id);
                $email = $this->generateEmail($username);

                $this->line("  - {$employee->name} -> {$username} (pass: {$password})");

                if (! $dryRun) {
                    DB::transaction(function () use ($employee, $username, $email, $password): void {
                        $user = User::create([
                            'name' => $employee->name,
                            'username' => $username,
                            'email' => $email,
                            'password' => Hash::make($password),
                            'role' => Role::Operator,
                            'type' => 'employee',
                        ]);

                        $employee->update(['auth_user_id' => $user->id]);
                        $this->assignDefaultGroup($employee, 'Empleado');
                    });
                    $createdEmployees++;

                    $usersData[] = [
                        'name' => $employee->name,
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'type' => 'Empleado',
                        'reference' => "Employee ID: {$employee->id}",
                    ];
                } else {
                    $createdEmployees++;
                }
            }
        }

        // Crear usuarios para profesores
        if ($professorsWithoutUser->count() > 0) {
            $this->info("\nPROFESORES: {$professorsWithoutUser->count()}");

            foreach ($professorsWithoutUser as $profesor) {
                $username = $this->generateUsername($profesor->nombre_profesor, $profesor->paterno);
                $password = 'UTE'.$profesor->clave_profesor;
                $email = $this->generateEmail($username);

                $this->line("  - {$profesor->nombre_completo} -> {$username} (pass: {$password})");

                if (! $dryRun) {
                    DB::transaction(function () use ($profesor, $username, $email, $password): void {
                        $user = User::create([
                            'name' => $profesor->nombre_completo,
                            'username' => $username,
                            'email' => $email,
                            'password' => Hash::make($password),
                            'role' => Role::Operator,
                            'type' => 'professor',
                        ]);

                        $profesor->update(['auth_user_id' => $user->id]);
                        $this->assignDefaultGroup($profesor, 'Profesor');
                    });
                    $createdProfessors++;

                    $usersData[] = [
                        'name' => $profesor->nombre_completo,
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'type' => 'Profesor',
                        'reference' => "Profesor: {$profesor->clave_profesor}",
                    ];
                } else {
                    $createdProfessors++;
                }
            }
        }

        $total = $createdEmployees + $createdProfessors;

        if (! $dryRun) {
            $this->info("\n✅ USUARIOS CREADOS:");
            $this->info("  • Empleados: {$createdEmployees}");
            $this->info("  • Profesores: {$createdProfessors}");
            $this->info("  • Total: {$total}");

            // Mostrar tabla con credenciales
            if (! empty($usersData)) {
                $this->line("\n📋 CREDENCIALES GENERADAS:\n");
                $this->table(
                    ['Nombre', 'Usuario', 'Email', 'Contraseña', 'Tipo', 'Referencia'],
                    $usersData
                );
            }

            $this->warn("\nGuarda estas credenciales. Las contraseñas no se muestran nuevamente.");
            $this->info("Los usuarios pueden iniciar sesión con el usuario generado.\n");
        } else {
            $this->line("\n<fg=yellow>SIMULACIÓN:</> Se crearían {$total} usuarios ({$createdEmployees} empleados, {$createdProfessors} profesores)");
            $this->line("Ejecuta sin <comment>--dry-run</comment> para crear realmente los usuarios.\n");
        }

        return 0;
    }

    private function generateUsername(string $name, ?string $paterno = null): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($name)) ?: []));
        $firstName = $paterno ? ($parts[0] ?? '') : ($parts[count($parts) - 1] ?? '');
        $paterno = $paterno ?: (count($parts) > 1 ? ($parts[0] ?? '') : '');
        $base = Str::lower(Str::ascii(substr($firstName, 0, 1).$paterno));
        $base = preg_replace('/[^a-z0-9]/', '', $base) ?: 'usuario';
        $username = $base;
        $suffix = 2;

        while (User::where('username', $username)->exists() || isset($this->reservedUsernames[$username])) {
            $username = $base.$suffix++;
        }

        $this->reservedUsernames[$username] = true;

        return $username;
    }

    private function generateEmail(string $username): string
    {
        return $username.'@sistema.local';
    }

    private function assignDefaultGroup(Employee|Profesor $entity, string $groupName): void
    {
        $group = PermissionGroup::where('name', $groupName)->where('is_default', true)->first();

        if ($group) {
            $entity->permissionGroups()->syncWithoutDetaching([$group->id]);
        }
    }
}
