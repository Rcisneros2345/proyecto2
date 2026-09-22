<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeUpdateCardTest extends TestCase
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

        $this->device = Device::factory()->create(['name' => 'Entrada principal']);

        $this->employee->devices()->attach($this->device->id, [
            'device_uid' => 1,
            'role' => 0,
            'active' => true,
            'card_number' => '12345',
        ]);
    }

    public function test_update_card_persists_valid_number(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('employees.update-card', $this->employee),
            ['device_id' => $this->device->id, 'card_number' => '67890']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('device_employee', [
            'employee_id' => $this->employee->id,
            'device_id' => $this->device->id,
            'card_number' => '67890',
        ]);
    }

    public function test_update_card_rejects_unenrolled_device(): void
    {
        $other = Device::factory()->create(['name' => 'Otra']);

        $response = $this->actingAs($this->user)
            ->from(route('employees.edit', $this->employee))
            ->post(route('employees.update-card', $this->employee), [
                'device_id' => $other->id,
                'card_number' => '11111',
            ]);

        $response->assertRedirect(route('employees.edit', $this->employee));
        $response->assertSessionHas('error');
    }

    public function test_update_card_rejects_duplicate_on_same_device(): void
    {
        $other = Employee::create(['name' => 'Otro', 'user_id' => 'EMP-04']);
        $other->devices()->attach($this->device->id, [
            'device_uid' => 2,
            'role' => 0,
            'active' => true,
            'card_number' => '99999',
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('employees.edit', $this->employee))
            ->post(route('employees.update-card', $this->employee), [
                'device_id' => $this->device->id,
                'card_number' => '99999',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
