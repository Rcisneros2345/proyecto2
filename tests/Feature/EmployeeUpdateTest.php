<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);

        $this->employee = Employee::create([
            'name' => 'Maria Garcia',
            'user_id' => 'EMP-03',
        ]);

        $this->device1 = Device::factory()->create(['name' => 'Entrada principal']);
        $this->device2 = Device::factory()->create(['name' => 'Entrada secundaria']);

        foreach ([$this->device1, $this->device2] as $i => $device) {
            $this->employee->devices()->attach($device->id, [
                'device_uid' => $i + 1,
                'role' => 0,
                'active' => true,
                'card_number' => '1000'.($i + 1),
            ]);
        }
    }

    public function test_update_persists_pin_and_role_on_all_enrollments(): void
    {
        $response = $this->actingAs($this->user)->put(
            route('employees.update', $this->employee),
            ['name' => 'Maria Garcia', 'password' => '4321', 'role' => '13']
        );

        $response->assertRedirect();

        // Segunda lectura desde BD: demuestra persistencia real, no memoria/sesión.
        $fresh = Employee::find($this->employee->id);
        $this->assertSame('Maria Garcia', $fresh->name);

        $pivots = $fresh->devices()->orderBy('devices.id')->get();
        $this->assertCount(2, $pivots);
        foreach ($pivots as $enrolled) {
            // El cast 'encrypted' descifra al leer: comparamos valor real.
            $this->assertSame('4321', $enrolled->pivot->password);
            $this->assertSame(13, (int) $enrolled->pivot->role);
        }
    }

    public function test_update_without_credentials_conserves_existing_values(): void
    {
        // Card A: no envía password/role → los enrolamientos no deben cambiar.
        $response = $this->actingAs($this->user)->put(
            route('employees.update', $this->employee),
            ['name' => 'Maria G.']
        );

        $response->assertRedirect();

        $fresh = Employee::find($this->employee->id);
        $this->assertSame('Maria G.', $fresh->name);
        foreach ($fresh->devices as $enrolled) {
            $this->assertNull($enrolled->pivot->password);
            $this->assertSame(0, (int) $enrolled->pivot->role);
        }
    }
}
