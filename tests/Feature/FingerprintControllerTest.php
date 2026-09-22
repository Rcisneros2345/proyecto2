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

class FingerprintControllerTest extends TestCase
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

    public function test_assign_fingerprint(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $response = $this->postJson(
            route('devices.sync-fingerprints', $device),
            ['employee_id' => $employee->id, 'device_id' => $device->id]
        );

        $response->assertOk();
        $response->assertJson(['status' => 'queued']);
    }

    public function test_copy_fingerprint(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $response = $this->postJson(
            route('devices.sync-fingerprints', $device),
            ['employee_id' => $employee->id, 'device_id' => $device->id, 'target_device_id' => Device::create([
                'name' => 'Target Device',
                'serial_number' => 'TARGET-'.uniqid(),
                'ip' => '192.168.1.101',
                'port' => 4370,
                'status' => 'online',
            ])->id]
        );

        $response->assertOk();
        $response->assertJson(['status' => 'queued']);
    }

    public function test_delete_fingerprint_success(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_test',
            'template' => 'template_data',
        ]);

        // Verificar que la huella existe antes
        $this->assertDatabaseHas('fingerprints', ['id' => $fingerprint->id]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('removeFingerprint')->once()
            ->with(
                Mockery::on(fn ($arg) => $arg instanceof Employee && $arg->id === $employee->id),
                Mockery::on(fn ($arg) => $arg instanceof \App\Models\Fingerprint && $arg->id === $fingerprint->id)
            )
            ->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->delete(
            route('employees.delete-fingerprint', [$employee, $fingerprint])
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'La huella fue eliminada del dispositivo.',
        ]);

        // Verificar que la huella fue borrada localmente
        $this->assertDatabaseMissing('fingerprints', ['id' => $fingerprint->id]);

        // Verificar que fingerprint_count se actualizó
        $pivot = $device->employees()->whereKey($employee->getKey())->first();
        $this->assertEquals(0, $pivot->pivot->fingerprint_count);
    }

    public function test_delete_fingerprint_failure(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_test',
            'template' => 'template_data',
        ]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('removeFingerprint')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->delete(
            route('employees.delete-fingerprint', [$employee, $fingerprint])
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
        ]);

        // Verificar que la huella NO fue borrada localmente
        $this->assertDatabaseHas('fingerprints', ['id' => $fingerprint->id]);
    }

    public function test_upload_fingerprints_on_device_success(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('uploadFingerprints')->once()
            ->with(Mockery::on(fn ($arg) => $arg instanceof Employee && $arg->id === $employee->id))
            ->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('devices.employees.upload-fingerprints', [$device, $employee])
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'Las huellas del empleado fueron subidas al dispositivo.',
        ]);
    }

    public function test_upload_fingerprints_on_device_failure(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('uploadFingerprints')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('devices.employees.upload-fingerprints', [$device, $employee])
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No se pudieron subir las huellas al dispositivo.',
        ]);
    }

    public function test_upload_fingerprints_on_device_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('uploadFingerprints')->once()->andThrow(
            new ZktecoConnectionException('offline', '192.168.1.100')
        );
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('devices.employees.upload-fingerprints', [$device, $employee])
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }

    public function test_copy_fingerprint_success(): void
    {
        $this->actingAdmin();
        ['device' => $sourceDevice, 'employee' => $employee] = $this->createDeviceEmployee();

        // Enroll employee on target device too
        $targetDevice = Device::create([
            'name' => 'Target Device',
            'serial_number' => 'TARGET-'.uniqid(),
            'ip' => '192.168.1.101',
            'port' => 4370,
            'status' => 'online',
        ]);
        DB::table('device_employee')->insert([
            'device_id' => $targetDevice->id,
            'employee_id' => $employee->id,
            'device_uid' => '2',
            'card_number' => '0',
            'active' => true,
        ]);

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $sourceDevice->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_copy',
            'template' => 'template_data_copy',
        ]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('uploadFingerprint')->once()->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('employees.copy-fingerprint', [$employee, $fingerprint]),
            ['target_device_id' => $targetDevice->id]
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'La huella fue copiada al dispositivo.',
        ]);

        // Verify local fingerprint was created on target device
        $this->assertDatabaseHas('fingerprints', [
            'employee_id' => $employee->id,
            'device_id' => $targetDevice->id,
            'finger' => 1,
            'template_hash' => 'hash_copy',
        ]);

        // Verify fingerprint_count was updated on target device pivot
        $pivot = $targetDevice->employees()->whereKey($employee->getKey())->first();
        $this->assertEquals(1, $pivot->pivot->fingerprint_count);
    }

    public function test_copy_fingerprint_failure(): void
    {
        $this->actingAdmin();
        ['device' => $sourceDevice, 'employee' => $employee] = $this->createDeviceEmployee();

        $targetDevice = Device::create([
            'name' => 'Target Device',
            'serial_number' => 'TARGET-'.uniqid(),
            'ip' => '192.168.1.101',
            'port' => 4370,
            'status' => 'online',
        ]);
        DB::table('device_employee')->insert([
            'device_id' => $targetDevice->id,
            'employee_id' => $employee->id,
            'device_uid' => '2',
            'card_number' => '0',
            'active' => true,
        ]);

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $sourceDevice->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_copy',
            'template' => 'template_data_copy',
        ]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('uploadFingerprint')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('employees.copy-fingerprint', [$employee, $fingerprint]),
            ['target_device_id' => $targetDevice->id]
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No se pudo copiar la huella al dispositivo.',
        ]);

        // Verify no local fingerprint was created on target device
        $this->assertDatabaseMissing('fingerprints', [
            'employee_id' => $employee->id,
            'device_id' => $targetDevice->id,
            'finger' => 1,
        ]);
    }

    public function test_copy_fingerprint_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $sourceDevice, 'employee' => $employee] = $this->createDeviceEmployee();

        $targetDevice = Device::create([
            'name' => 'Target Device',
            'serial_number' => 'TARGET-'.uniqid(),
            'ip' => '192.168.1.101',
            'port' => 4370,
            'status' => 'online',
        ]);
        DB::table('device_employee')->insert([
            'device_id' => $targetDevice->id,
            'employee_id' => $employee->id,
            'device_uid' => '2',
            'card_number' => '0',
            'active' => true,
        ]);

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $sourceDevice->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_copy',
            'template' => 'template_data_copy',
        ]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('uploadFingerprint')->once()->andThrow(
            new ZktecoConnectionException('offline', '192.168.1.101')
        );
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->postJson(
            route('employees.copy-fingerprint', [$employee, $fingerprint]),
            ['target_device_id' => $targetDevice->id]
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }

    public function test_copy_fingerprint_missing_target_device(): void
    {
        $this->actingAdmin();
        ['device' => $sourceDevice, 'employee' => $employee] = $this->createDeviceEmployee();

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $sourceDevice->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_copy',
            'template' => 'template_data_copy',
        ]);

        $response = $this->postJson(
            route('employees.copy-fingerprint', [$employee, $fingerprint]),
            []
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('target_device_id');
    }

    public function test_copy_fingerprint_same_device(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_copy',
            'template' => 'template_data_copy',
        ]);

        $response = $this->postJson(
            route('employees.copy-fingerprint', [$employee, $fingerprint]),
            ['target_device_id' => $device->id]
        );

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'La huella ya pertenece al dispositivo destino.',
        ]);
    }

    public function test_remove_from_device_success(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        // Crear una huella para este empleado en este dispositivo
        \App\Models\Fingerprint::create([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_test',
            'template' => 'template_data',
        ]);

        // Verificar que el pivot y la huella existen antes
        $this->assertDatabaseHas('device_employee', [
            'device_id' => $device->id,
            'employee_id' => $employee->id,
        ]);
        $this->assertDatabaseHas('fingerprints', [
            'device_id' => $device->id,
            'employee_id' => $employee->id,
        ]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('removeUserFromDevice')->once()->with(1)->andReturn(true);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->delete(
            route('devices.employees.remove', [$device, $employee])
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'completed',
            'message' => 'El empleado fue removido del dispositivo.',
        ]);

        // Verificar que el pivot fue borrado localmente
        $this->assertDatabaseMissing('device_employee', [
            'device_id' => $device->id,
            'employee_id' => $employee->id,
        ]);

        // Verificar que las huellas fueron borradas localmente
        $this->assertDatabaseMissing('fingerprints', [
            'device_id' => $device->id,
            'employee_id' => $employee->id,
        ]);
    }

    public function test_remove_from_device_failure(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('removeUserFromDevice')->once()->andReturn(false);
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->delete(
            route('devices.employees.remove', [$device, $employee])
        );

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
        ]);

        // Verificar que el pivot NO fue borrado localmente
        $this->assertDatabaseHas('device_employee', [
            'device_id' => $device->id,
            'employee_id' => $employee->id,
        ]);
    }

    public function test_remove_from_device_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('removeUserFromDevice')->once()->with(1)->andThrow(
            new ZktecoConnectionException('offline', '192.168.1.100')
        );
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->delete(
            route('devices.employees.remove', [$device, $employee])
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }

    public function test_delete_fingerprint_connection_error(): void
    {
        $this->actingAdmin();
        ['device' => $device, 'employee' => $employee] = $this->createDeviceEmployee();

        $fingerprint = \App\Models\Fingerprint::create([
            'device_id' => $device->id,
            'employee_id' => $employee->id,
            'finger' => 1,
            'template_hash' => 'hash_test',
            'template' => 'template_data',
        ]);

        $mock = Mockery::mock(ZktecoService::class);
        $mock->shouldReceive('removeFingerprint')->once()
            ->with(
                Mockery::on(fn ($arg) => $arg instanceof Employee && $arg->id === $employee->id),
                Mockery::on(fn ($arg) => $arg instanceof \App\Models\Fingerprint && $arg->id === $fingerprint->id)
            )
            ->andThrow(new ZktecoConnectionException('offline', '192.168.1.100'));
        app()->bind(ZktecoService::class, fn () => $mock);

        $response = $this->delete(
            route('employees.delete-fingerprint', [$employee, $fingerprint])
        );

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
        $response->assertJsonFragment(['message' => 'No se pudo conectar al dispositivo: offline']);
    }
}
