<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Employee;
use App\Models\HorarioLaboral;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for authentication
        $this->user = User::factory()->create(['role' => 'admin']);

        // Create some employees with diverse data for filter tests
        Employee::create([
            'name' => 'Juan Perez',
            'sexo' => 'F',
            'fecha_nacimiento' => '1990-05-10',
            'nacionalidad' => 'Mexicana',
            'estado_civil' => 'Soltera',
            'telefono' => '5551234567',
            'email' => 'juan@example.test',
            'user_id' => 'USER-001',
            'cargo' => 'Gerente',
            'departamento' => 'Ventas',
            'id_campus' => '1',
            'status_actual' => 'A',
            'contrato' => 'Indefinido',
            'nivel' => 'N1',
        ]);
    }

    public function test_employees_returns_200(): void
    {
        $response = $this->actingAs($this->user)->get('/employees');

        // Try to get the exception
        $errorMessage = '';
        if ($response->status() === 500) {
            $content = $response->getContent();
            // Extract error info from the response
            if (preg_match('/<code>([^<]+)<\/code>/', $content, $matches)) {
                $errorMessage = $matches[1];
            }
        }

        $this->assertEquals(200, $response->status(), "Status should be 200 but was 500. Error: $errorMessage");
    }

    public function test_employees_shows_position_area_and_contracted_schedule(): void
    {
        $area = Area::create([
            'identificador' => 'RH',
            'descripcion' => 'Recursos Humanos',
        ]);
        $puesto = Puesto::create([
            'identificador' => 'ANALISTA',
            'descripcion' => 'Analista de personal',
            'area_id' => $area->id,
        ]);
        $employee = Employee::firstOrFail();
        $employee->update(['area_id' => $area->id, 'puesto_id' => $puesto->id]);
        HorarioLaboral::create([
            'employee_id' => $employee->id,
            'dia_semana' => 1,
            'hora_entrada' => '08:00',
            'hora_salida' => '17:00',
            'activo' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/employees');

        $response->assertOk()
            ->assertSee('Analista de personal')
            ->assertSee('Recursos Humanos')
            ->assertSee('Horario contratado')
            ->assertSee('Femenino')
            ->assertSee('10/05/1990')
            ->assertSee('Mexicana')
            ->assertSee('08:00')
            ->assertSee('17:00');
    }
}
