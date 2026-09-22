<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\ZktecoConnectionException;
use App\Models\Device;
use App\Models\Employee;
use App\Models\User;
use App\Services\ZktecoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class DeviceSyncControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function actingAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        return $user;
    }

    private function createDeviceEmployee(): array
    {
        $device = Device::create([
            'name' => 'Test Device',
            'serial_number' => 'TEST-'.uniqid(),
            'ip' => '192.168.1.100',
            'port' => 4370,
            'status' => 'online',
        ]);

        $employee = Employee::create([
            'name' => 'Empleado Test',
            'user_id' => 'EMP-'.uniqid(),
            'status_actual' => 'A',
        ]);

        DB::table('device_employee')->insert([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'device_uid' => '1',
            'card_number' => '0',
            'active' => true,
        ]);

        return ['device' => $device, 'employee' => $employee];
    }

    public function test_sync_users(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $response = $this->postJson(
            route('devices.sync-users', $device)
        );

        $response->assertOk();
    }

    public function test_sync_fingerprints(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        // Create some fingerprints first
        $fingerprints = 3;
        for ($i = 0; $i < $fingerprints; $i++) {
            \App\Models\Fingerprint::create([
                'device_id' => $device->id,
                'employee_id' => $employee->id,
                'finger' => $i + 1,
                'template_hash' => 'hash_'.$i,
                'template' => \Illuminate\Support\Str::random(64),
            ]);
        }

        $response = $this->postJson(
            route('devices.sync-fingerprints', $device)
        );

        $response->assertOk();
    }

    public function test_sync_attendances(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        // Create some attendances
        for ($i = 0; $i < 5; $i++) {
            $this->createAttendance($device->id, $employee->id, 0, now()->subHours($i)->toDateTimeString());
        }

        $response = $this->postJson(
            route('devices.sync-attendances', $device)
        );

        $response->assertOk();
    }

    public function test_sync_all(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        // Create some data
        for ($i = 0; $i < 3; $i++) {
            $this->createAttendance($device->id, $employee->id, 0, now()->subHours($i)->toDateTimeString());
        }
        \App\Models\Fingerprint::create([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_1',
            'template' => \Illuminate\Support\Str::random(64),
        ]);

        $response = $this->postJson(
            route('devices.sync-all', $device)
        );

        $response->assertOk();
    }

    public function test_set_time(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('setTime')->once()->with('2026-01-15 10:30:00')->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('devices.set-time', $device),
            ['datetime' => '2026-01-15 10:30:00']
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'La hora del dispositivo fue ajustada.',
        ]);
    }

    public function test_set_time_failure(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('setTime')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('devices.set-time', $device),
            ['datetime' => '2026-01-15 10:30:00']
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No se pudo ajustar la hora del dispositivo.',
        ]);
    }

    public function test_set_time_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('setTime')->once()->andThrow(
            new ZktecoConnectionException('offline', '192.168.1.100')
        );
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('devices.set-time', $device),
            ['datetime' => '2026-01-15 10:30:00']
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }

    public function test_set_time_missing_datetime(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $response = $this->postJson(
            route('devices.set-time', $device)
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('datetime');
    }

    public function test_clear_attendance_success(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('clearAttendance')->once()->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->post(
            route('devices.clear-attendance', $device)
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'Las asistencias del dispositivo fueron eliminadas.',
        ]);
    }

    public function test_clear_attendance_failure(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('clearAttendance')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->post(
            route('devices.clear-attendance', $device)
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No se pudieron eliminar las asistencias del dispositivo.',
        ]);
    }

    public function test_clear_attendance_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('clearAttendance')->once()->andThrow(
            new ZktecoConnectionException('offline', '192.168.1.100')
        );
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->post(
            route('devices.clear-attendance', $device)
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }

    public function test_restore_success(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('restoreDevice')->once()->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->post(
            route('devices.restore', $device)
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'El dispositivo fue restaurado.',
        ]);
    }

    public function test_restore_failure(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('restoreDevice')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->post(
            route('devices.restore', $device)
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No se pudo restaurar el dispositivo.',
        ]);
    }

    public function test_restore_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $device] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('restoreDevice')->once()->andThrow(
            new ZktecoConnectionException('offline', '192.168.1.100')
        );
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->post(
            route('devices.restore', $device)
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }

    private function createAttendance(int $deviceId, ?int $employeeId, int $type, string $recordedAt): void
    {
        DB::table('attendances')->insert([
            'device_id' => $deviceId,
            'employee_id' => $employeeId,
            'user_id' => 'TEST',
            'state' => 0,
            'type' => $type,
            'attendance_type' => 'biometric',
            'source' => 'zkteco',
            'recorded_at' => $recordedAt,
        ]);
    }
}
