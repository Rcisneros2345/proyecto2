<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);

        // Create employee enrolled in 2 devices
        $this->employee = Employee::create([
            'name' => 'Maria Garcia',
            'user_id' => 'EMP-03',
        ]);

        $device1 = Device::factory()->create(['name' => 'Entrada principal']);
        $device2 = Device::factory()->create(['name' => 'Entrada secundaria']);

        $this->employee->devices()->attach($device1->id, [
            'device_uid' => 1,
            'role' => 0,
            'active' => true,
            'card_number' => '12345',
        ]);

        $this->employee->devices()->attach($device2->id, [
            'device_uid' => 2,
            'role' => 1,
            'active' => true,
            'card_number' => '67890',
        ]);
    }

    public function test_edit_returns_200_with_admin_permission(): void
    {
        $response = $this->actingAs($this->user)->get(route('employees.edit', $this->employee));

        $response->assertOk();
    }

    public function test_edit_contains_tabs(): void
    {
        $response = $this->actingAs($this->user)->get(route('employees.edit', $this->employee));

        $response->assertSee('Identidad');
        $response->assertSee('Enrolamientos');
    }

    public function test_edit_contains_kpi_grid(): void
    {
        $response = $this->actingAs($this->user)->get(route('employees.edit', $this->employee));

        // KPI grid with 4 cards (contrato real de employees.edit)
        $response->assertSee('Huellas disponibles');
        $response->assertSee('Dispositivos enrolados');
        $response->assertSee('Tarjetas asignadas');
        $response->assertSee('Último sync');
    }

    public function test_edit_contains_table_cards(): void
    {
        $response = $this->actingAs($this->user)->get(route('employees.edit', $this->employee));

        // Table with device cards
        $response->assertSee('Entrada principal');
        $response->assertSee('Entrada secundaria');
    }

    public function test_edit_contains_sync_drawer(): void
    {
        $response = $this->actingAs($this->user)->get(route('employees.edit', $this->employee));

        // Sync preview drawer
        $response->assertSee('sync-devices-form');
        $response->assertSee('syncDiffOffcanvas');
        $response->assertSee('sync-progress-panel');
    }

    public function test_edit_contains_js_hooks(): void
    {
        $response = $this->actingAs($this->user)->get(route('employees.edit', $this->employee));

        // JavaScript preview and polling inline (hooks reales de edit.blade.php)
        $response->assertSee('data-sync-name', false);
        $response->assertSee('data-select-all-devices', false);
        $response->assertSee('sync-diff-table');
        $response->assertSee('employee-credentials-form');
    }
}
