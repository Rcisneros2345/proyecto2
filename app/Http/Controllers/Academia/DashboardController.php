<?php

declare(strict_types=1);

namespace App\Http\Controllers\Academia;

use App\Http\Controllers\Controller;
use App\Models\Academia\HorarioDet;
use App\Services\AcademiaDashboardService;
use App\Services\CicloActualService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected CicloActualService $cicloService,
        protected AcademiaDashboardService $dashboardService,
    ) {}

    public function index(Request $request): View
    {
        $ciclo = $this->cicloService->resolve($request);
        $summary = $this->dashboardService->build($ciclo);

        $kpis = $summary['kpis'];

        // Horarios por día (para gráfico)
        $horariosPorDia = \App\Models\Academia\HorarioDet::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->selectRaw('dia, COUNT(*) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->mapWithKeys(fn ($h) => [$h->dia => $h->total])
            ->toArray();

        // Distribución por origen horario
        $porOrigen = HorarioDet::porCiclo($ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->activo()
            ->selectRaw('origen_horario, COUNT(*) as total')
            ->groupBy('origen_horario')
            ->get()
            ->mapWithKeys(fn ($h) => [$h->origen_horario ?: 'SIN_DEFINIR' => $h->total])
            ->toArray();

        return view('academia.dashboard.index', [
            'ciclo' => $ciclo,
            'kpis' => $kpis,
            'dashboardSummary' => $summary,
            'horariosPorDia' => $horariosPorDia,
            'porOrigen' => $porOrigen,
        ]);
    }
}
