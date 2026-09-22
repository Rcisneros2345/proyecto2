<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use App\Models\HorarioLaboral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica que la página de asistencias renderiza correctamente y que
 * el modelo HorarioLaboral apunta a la tabla real horarios_laborales
 * (no el pluralizado automático "horarios_laborals").
 */
class AttendancePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_page_renders_successfully(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/attendances');

        $response->assertOk()
            ->assertSee('Cuadrícula semanal')
            ->assertSee('Registros capturados');
    }

    public function test_horario_laboral_model_uses_correct_table(): void
    {
        $model = new HorarioLaboral;

        $this->assertSame('horarios_laborales', $model->getTable());
    }

    public function test_attendance_summarizes_normal_and_extra_punches_against_base_schedule(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $device = Device::create(['name' => 'Checador', 'ip' => '192.168.1.20']);
        $employee = Employee::create(['user_id' => '301', 'name' => 'Empleado Prueba']);
        $date = today();

        HorarioLaboral::create([
            'employee_id' => $employee->id,
            'dia_semana' => $date->dayOfWeekIso,
            'hora_entrada' => '08:00',
            'hora_salida' => '17:00',
            'activo' => true,
        ]);
        foreach ([
            ['type' => 0, 'time' => '08:05'],
            ['type' => 1, 'time' => '17:10'],
            ['type' => 4, 'time' => '19:00'],
            ['type' => 5, 'time' => '20:00'],
        ] as $punch) {
            Attendance::create([
                'device_id' => $device->id,
                'employee_id' => $employee->id,
                'user_id' => '301',
                'state' => 1,
                'type' => $punch['type'],
                'recorded_at' => $date->copy()->setTimeFromTimeString($punch['time']),
            ]);
        }

        $response = $this->actingAs($user)->get('/attendances');

        $response->assertOk()
            ->assertSee('Entrada:')
            ->assertSee('08:05:00')
            ->assertSee('Extra entrada:')
            ->assertSee('19:00:00')
            ->assertSee('Salida:')
            ->assertSee('17:10:00')
            ->assertSee('Extra salida:')
            ->assertSee('20:00:00')
            ->assertSee('llegó tarde en 5 min')
            ->assertSee('salió tarde en 10 min');
    }

    public function test_class_attendance_export_returns_csv(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('attendances.export.classes'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertHeader('Content-Disposition');
    }
}
