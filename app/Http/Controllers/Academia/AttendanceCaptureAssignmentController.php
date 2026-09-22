<?php

namespace App\Http\Controllers\Academia;

use App\Http\Controllers\Controller;
use App\Models\Academia\Ciclo;
use App\Models\Academia\Nivel;
use App\Models\Academia\Sede;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceCaptureAssignmentController extends Controller
{
    public function edit(User $user): View
    {
        return view('preferencia.captura-asistencia', [
            'user' => $user->load('attendanceCaptureAssignments'),
            'assignments' => $user->attendanceCaptureAssignments()->orderBy('nivel')->orderBy('id_campus')->get(),
            'niveles' => Nivel::query()->activo()->get(),
            'sedes' => Sede::query()->activo()->orderBy('descripcion')->get(),
            'ciclos' => Ciclo::query()
                ->orderByDesc('inicial')
                ->orderByDesc('final')
                ->orderByDesc('periodo')
                ->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'assignments' => ['nullable', 'array'],
            'assignments.*.nivel' => ['nullable', 'string', 'max:30'],
            'assignments.*.id_campus' => ['nullable', 'string', 'max:30'],
            'assignments.*.inicial' => ['nullable', 'integer'],
            'assignments.*.final' => ['nullable', 'integer'],
            'assignments.*.periodo' => ['nullable', 'integer'],
            'assignments.*.active' => ['nullable', 'boolean'],
        ]);

        $user->attendanceCaptureAssignments()->delete();

        foreach ($data['assignments'] ?? [] as $assignment) {
            if (blank($assignment['nivel'] ?? null) && blank($assignment['id_campus'] ?? null)) {
                continue;
            }

            $user->attendanceCaptureAssignments()->create([
                'nivel' => $assignment['nivel'] ?? null,
                'id_campus' => $assignment['id_campus'] ?? null,
                'inicial' => $assignment['inicial'] ?? null,
                'final' => $assignment['final'] ?? null,
                'periodo' => $assignment['periodo'] ?? null,
                'active' => array_key_exists('active', $assignment)
                    ? filter_var($assignment['active'], FILTER_VALIDATE_BOOLEAN)
                    : true,
            ]);
        }

        return redirect()->route('preferencia.usuarios.captura-asistencia.edit', $user)
            ->with('success', 'Asignaciones de captura actualizadas correctamente.');
    }
}
