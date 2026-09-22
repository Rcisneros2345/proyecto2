<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeEnrollmentDiffTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);

        // Create employee enrolled in device1
        $this->employee = Employee::create([
            'name' => 'Carlos Ruiz',
            'user_id' => 'EMP-07',
        ]);

        $this->device1 = Device::factory()->create(['name' => 'Entrada principal']);
        $this->device2 = Device::factory()->create(['name' => 'Entrada secundaria']);

        // Enroll employee in device1 only. fingerprint_count 0 porque el
        // paquete se deriva del estado real (sin Fingerprint models en el test),
        // así el caso noop es determinista.
        $this->employee->devices()->attach($this->device1->id, [
            'device_uid' => 1,
            'role' => 0,
            'active' => true,
            'card_number' => '12345',
            'fingerprint_count' => 0,
        ]);
        // device2 has no enrolment for this employee
    }

    private function diffGet(array $params = [])
    {
        // call() pasa $params como query en GET (get()/getJson() usan el 2do arg para headers).
        return $this->actingAs($this->user)->call(
            'GET',
            route('employees.enrollment-diff', $this->employee),
            $params,
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        );
    }

    public function test_enrollment_diff_returns_200_with_valid_device_ids(): void
    {
        $response = $this->diffGet(['device_ids' => [$this->device1->id]]);

        $response->assertOk();
        $data = $response->json();

        $this->assertArrayHasKey('employee_id', $data);
        $this->assertArrayHasKey('employee_name', $data);
        $this->assertArrayHasKey('diff', $data);

        $this->assertCount(1, $data['diff']);
        $diff = $data['diff'][0];

        $this->assertArrayHasKey('device_id', $diff);
        $this->assertArrayHasKey('device_name', $diff);
        $this->assertArrayHasKey('action', $diff);
        $this->assertArrayHasKey('changes', $diff);
        $this->assertArrayHasKey('warnings', $diff);

        // action should be one of: create, update, noop
        $this->assertContains($diff['action'], ['create', 'update', 'noop']);

        // changes should have the right structure (5 fields: role, card_number, pin, fingerprint_count, active)
        $this->assertCount(5, $diff['changes']);

        // PIN should have from/to as null
        $pinChange = collect($diff['changes'])->where('field', 'pin')->first();
        $this->assertEquals(null, $pinChange['from']);
        $this->assertEquals(null, $pinChange['to']);

        // warnings should be array
        $this->assertIsArray($diff['warnings']);
    }

    public function test_enrollment_diff_422_without_device_ids(): void
    {
        $response = $this->diffGet(['device_ids' => []]);

        // Missing required device_ids should return 422
        $response->assertStatus(422);
    }

    public function test_enrollment_diff_case_noop(): void
    {
        // Employee enrolled in device1, request same device - should be noop if nothing changed
        $response = $this->diffGet(['device_ids' => [$this->device1->id]]);

        $data = $response->json();
        $diff = $data['diff'][0];

        // Since the package is generated from current state, action should be 'noop'
        $this->assertEquals('noop', $diff['action']);
    }

    public function test_enrollment_diff_case_create(): void
    {
        // device2 has NO enrollement for this employee → action should be 'create'
        $response = $this->diffGet(['device_ids' => [$this->device2->id]]);

        $data = $response->json();
        $diff = $data['diff'][0];

        // No enrollement → create
        $this->assertEquals('create', $diff['action']);
    }

    public function test_enrollment_diff_warnings_empty_without_duplicates(): void
    {
        // Cada tarjeta es única por dispositivo (unique device_id+card_number en BD):
        // sin duplicados posibles, warnings debe ser arreglo vacío.
        $response = $this->diffGet(['device_ids' => [$this->device1->id]]);

        $data = $response->json();
        $diff = $data['diff'][0];

        $this->assertIsArray($diff['warnings']);
        $this->assertSame([], $diff['warnings']);
    }
}
