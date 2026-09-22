<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\SyncProgressUpdated;
use App\Exceptions\ZktecoConnectionException;
use App\Jobs\SyncDeviceJob;
use App\Models\Device;
use App\Models\DeviceSync;
use App\Services\ZktecoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DeviceSyncController extends Controller
{
    public function syncUsers(Device $device, Request $request): JsonResponse
    {
        $sync = $device->syncs()->create([
            'status' => 'queued',
            'operation' => 'users',
            'stage' => 'Preparando',
        ]);

        try {
            SyncDeviceJob::dispatch($device, $sync, 'users');
        } catch (\Throwable $e) {
            $sync->update([
                'status' => 'failed',
                'stage' => 'Error',
                'error_message' => 'No se pudo iniciar la sincronización: '.$e->getMessage(),
                'finished_at' => now(),
            ]);

            return response()->json([
                'status' => 'failed',
                'operation' => 'users',
                'stage' => 'Error',
                'processed' => 0,
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'error' => 'No se pudo iniciar la sincronización. Inténtalo de nuevo.',
            ], 500);
        }

        event(new SyncProgressUpdated($sync, 'queued'));

        return response()->json([
            'status' => 'queued',
            'operation' => 'users',
            'stage' => 'Preparando',
            'sync_id' => $sync->id,
        ]);
    }

    /**
     * Synchronize fingerprints from a ZKTeco device.
     */
    public function syncFingerprints(Device $device, Request $request): JsonResponse
    {
        $sync = $device->syncs()->create([
            'status' => 'queued',
            'operation' => 'fingerprints',
            'stage' => 'Preparando',
        ]);

        try {
            SyncDeviceJob::dispatch($device, $sync, 'fingerprints');
        } catch (\Throwable $e) {
            $sync->update([
                'status' => 'failed',
                'stage' => 'Error',
                'error_message' => 'No se pudo iniciar la sincronización: '.$e->getMessage(),
                'finished_at' => now(),
            ]);

            return response()->json([
                'status' => 'failed',
                'operation' => 'fingerprints',
                'stage' => 'Error',
                'processed' => 0,
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'error' => 'No se pudo iniciar la sincronización. Inténtalo de nuevo.',
            ], 500);
        }

        event(new SyncProgressUpdated($sync, 'queued'));

        return response()->json([
            'status' => 'queued',
            'operation' => 'fingerprints',
            'stage' => 'Preparando',
            'sync_id' => $sync->id,
        ]);
    }

    /**
     * Synchronize attendances from a ZKTeco device.
     */
    public function syncAttendances(Device $device, Request $request): JsonResponse
    {
        $sync = $device->syncs()->create([
            'status' => 'queued',
            'operation' => 'attendances',
            'stage' => 'Preparando',
        ]);

        try {
            SyncDeviceJob::dispatch($device, $sync, 'attendances');
        } catch (\Throwable $e) {
            $sync->update([
                'status' => 'failed',
                'stage' => 'Error',
                'error_message' => 'No se pudo iniciar la sincronización: '.$e->getMessage(),
                'finished_at' => now(),
            ]);

            return response()->json([
                'status' => 'failed',
                'operation' => 'attendances',
                'stage' => 'Error',
                'processed' => 0,
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'error' => 'No se pudo iniciar la sincronización. Inténtalo de nuevo.',
            ], 500);
        }

        event(new SyncProgressUpdated($sync, 'queued'));

        return response()->json([
            'status' => 'queued',
            'operation' => 'attendances',
            'stage' => 'Preparando',
            'sync_id' => $sync->id,
        ]);
    }

    /**
     * Full synchronization: users → attendances → fingerprints.
     *
     * Crea un registro DeviceSync, dispacha SyncDeviceJob(operation='all')
     * y retorna status 'queued' para que el frontend muestre la barra de progreso.
     */
    public function syncAll(Device $device, Request $request): JsonResponse
    {
        // 1. Crear registro de sincronización para tracking de progreso
        $sync = $device->syncs()->create([
            'status' => 'queued',
            'operation' => 'all',
            'stage' => 'Preparando',
        ]);

        try {
            // 2. Dispatchar job de sincronización asíncrono
            SyncDeviceJob::dispatch($device, $sync, 'all');
        } catch (Throwable $e) {
            // Si no se puede dispatchar el job, marcar como fallido
            $sync->update([
                'status' => 'failed',
                'stage' => 'Error',
                'error_message' => 'No se pudo iniciar la sincronización: '.$e->getMessage(),
                'finished_at' => now(),
            ]);

            return response()->json([
                'status' => 'failed',
                'operation' => 'all',
                'stage' => 'Error',
                'processed' => 0,
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'error' => 'No se pudo iniciar la sincronización. Inténtalo de nuevo.',
            ], 500);
        }

        // 3. Retornar status 'queued' para que el frontend inicie polling
        event(new SyncProgressUpdated($sync, 'queued'));

        return response()->json([
            'status' => 'queued',
            'operation' => 'all',
            'stage' => 'Preparando',
            'sync_id' => $sync->id,
        ]);
    }

    /**
     * Set the time on a ZKTeco device.
     */
    public function setTime(Device $device, Request $request): JsonResponse
    {
        $request->validate([
            'datetime' => ['required', 'string'],
        ]);

        try {
            $service = app(ZktecoService::class, ['device' => $device]);

            if ($service->setTime($request->input('datetime'))) {
                return response()->json([
                    'status' => 'completed',
                    'message' => 'La hora del dispositivo fue ajustada.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo ajustar la hora del dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al ajustar la hora.',
            ], 500);
        }
    }

    /**
     * Clear attendance records for a device.
     */
    public function clearAttendance(Device $device): JsonResponse
    {
        try {
            $service = app(ZktecoService::class, ['device' => $device]);

            if ($service->clearAttendance()) {
                return response()->json([
                    'status' => 'completed',
                    'message' => 'Las asistencias del dispositivo fueron eliminadas.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudieron eliminar las asistencias del dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al eliminar asistencias.',
            ], 500);
        }
    }

    /**
     * Restore/enable a device.
     */
    public function restore(Device $device): JsonResponse
    {
        try {
            $service = app(ZktecoService::class, ['device' => $device]);

            if ($service->restoreDevice()) {
                return response()->json([
                    'status' => 'completed',
                    'message' => 'El dispositivo fue restaurado.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo restaurar el dispositivo.',
            ], 500);
        } catch (ZktecoConnectionException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo conectar al dispositivo: '.$e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado al restaurar el dispositivo.',
            ], 500);
        }
    }
}
