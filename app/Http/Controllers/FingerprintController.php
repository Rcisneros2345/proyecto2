<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ZktecoConnectionException;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Fingerprint;
use App\Services\ZktecoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FingerprintController extends Controller
{
    /**
     * Copy a fingerprint from one device to another.
     *
     * uploadFingerprint() usa $this->device como DESTINO: busca el enrolamiento
     * ahí, lee la plantilla desde la fuente si es cross-device y escribe en el
     * destino. Por eso el servicio se instancia con el dispositivo DESTINO
     * (target_device_id del request), no con el dispositivo origen de la huella.
     */
    public function copyFingerprint(Employee $employee, Fingerprint $fingerprint, Request $request): JsonResponse
    {
        $request->validate([
            'target_device_id' => ['required', 'exists:devices,id'],
        ]);

        $targetDevice = Device::findOrFail($request->input('target_device_id'));

        if (! $fingerprint->device_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'La huella no está asociada a ningún dispositivo origen.',
            ], 404);
        }

        if ((int) $fingerprint->device_id === (int) $targetDevice->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'La huella ya pertenece al dispositivo destino.',
            ], 422);
        }

        try {
            // Instanciar con el DESTINO — uploadFingerprint escribe en $this->device
            $service = app(ZktecoService::class, ['device' => $targetDevice]);

            if ($service->uploadFingerprint($employee, $fingerprint)) {
                // Estado local: crear fingerprint en dispositivo destino + actualizar count
                DB::transaction(function () use ($employee, $fingerprint, $targetDevice) {
                    Fingerprint::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'device_id' => $targetDevice->id,
                            'finger' => $fingerprint->finger,
                        ],
                        [
                            'template' => $fingerprint->template,
                            'template_hash' => $fingerprint->template_hash,
                        ]
                    );

                    $remaining = Fingerprint::where('device_id', $targetDevice->id)
                        ->where('employee_id', $employee->id)
                        ->count();
                    $targetDevice->employees()->updateExistingPivot($employee->id, [
                        'fingerprint_count' => $remaining,
                    ]);
                });

                return response()->json([
                    'status' => 'completed',
                    'message' => 'La huella fue copiada al dispositivo.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo copiar la huella al dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al copiar la huella.',
            ], 500);
        }
    }

    /**
     * Delete a fingerprint from a device.
     */
    public function deleteFingerprint(Employee $employee, Fingerprint $fingerprint): JsonResponse
    {
        try {
            $device = $fingerprint->device;

            if (! $device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La huella no está asociada a ningún dispositivo.',
                ], 404);
            }

            $service = app(ZktecoService::class, ['device' => $device]);

            if ($service->removeFingerprint($employee, $fingerprint)) {
                DB::transaction(function () use ($fingerprint, $device, $employee) {
                    $fingerprint->delete();

                    $remaining = Fingerprint::where('device_id', $device->id)
                        ->where('employee_id', $employee->id)
                        ->count();
                    $device->employees()->updateExistingPivot($employee->id, [
                        'fingerprint_count' => $remaining,
                    ]);
                });

                return response()->json([
                    'status' => 'completed',
                    'message' => 'La huella fue eliminada del dispositivo.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar la huella del dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al eliminar la huella.',
            ], 500);
        }
    }

    /**
     * Upload fingerprints to a specific device.
     */
    public function uploadFingerprintsOnDevice(Device $device, Employee $employee): JsonResponse
    {
        try {
            $service = app(ZktecoService::class, ['device' => $device]);

            if ($service->uploadFingerprints($employee)) {
                return response()->json([
                    'status' => 'completed',
                    'message' => 'Las huellas del empleado fueron subidas al dispositivo.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudieron subir las huellas al dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al subir las huellas.',
            ], 500);
        }
    }

    /**
     * Remove an employee from a device (including fingerprints).
     */
    public function removeFromDevice(Device $device, Employee $employee): JsonResponse
    {
        try {
            $pivot = $device->employees()->whereKey($employee->getKey())->first();

            if (! $pivot) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El empleado no está registrado en este dispositivo.',
                ], 404);
            }

            $uid = (int) $pivot->pivot->device_uid;
            $service = app(ZktecoService::class, ['device' => $device]);

            if ($service->removeUserFromDevice($uid)) {
                DB::transaction(function () use ($device, $employee) {
                    Fingerprint::where('device_id', $device->id)
                        ->where('employee_id', $employee->id)
                        ->delete();

                    $device->employees()->detach($employee->id);
                });

                return response()->json([
                    'status' => 'completed',
                    'message' => 'El empleado fue removido del dispositivo.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo remover al empleado del dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al remover empleado del dispositivo.',
            ], 500);
        }
    }
}
