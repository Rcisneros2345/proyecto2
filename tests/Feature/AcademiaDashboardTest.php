<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Academia\Ciclo;
use App\Models\Academia\Curso;
use App\Models\Academia\Grupo;
use App\Models\Academia\HorarioDet;
use App\Models\Academia\Materia;
use App\Models\Academia\Nivel;
use App\Models\Academia\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademiaDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['alumnos_grupos', 'horarios_det', 'alumnos', 'profesores', 'grupos', 'materias', 'planes', 'niveles', 'turnos', 'ciclos', 'cursos'] as $t) {
            DB::table($t)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Crear datos base para pruebas
        DB::table('ciclos')->insert([
            'inicial' => 2026,
            'final' => 2026,
            'periodo' => 1,
            'descripcion' => 'Septiembre - Diciembre 2026',
            'activo' => true,
        ]);

        DB::table('niveles')->insert([
            'nivel' => 'TSU',
            'descripcion' => 'TÉCNICO SUPERIOR UNIVERSITARIO',
            'orden' => 1,
            'activo' => true,
        ]);

        DB::table('turnos')->insert([
            'turno' => 'M',
            'descripcion' => 'MATUTINO',
            'activo' => true,
        ]);

        DB::table('planes')->insert([
            'id_plan' => 10,
            'nombre_plan' => 'TSU EN TECNOLOGÍAS',
            'nivel' => 'TSU',
            'activo' => true,
        ]);

        DB::table('materias')->insert([
            'clave_asignatura' => 'TI-101',
            'nombre_asignatura' => 'PROGRAMACIÓN BÁSICA',
            'id_plan' => 10,
            'semestre' => 1,
            'horas_teoria' => 4,
            'horas_practica' => 2,
            'creditos' => 6,
            'tipo' => 'obligatoria',
            'activa' => true,
        ]);

        DB::table('grupos')->insert([
            'codigo_grupo' => 'G-TI-1',
            'inicial' => 2026,
            'final' => 2026,
            'periodo' => 1,
            'grado' => 1,
            'turno' => 'M',
            'nivel' => 'TSU',
            'activo' => true,
        ]);

        DB::table('profesores')->insert([
            'clave_profesor' => '999',
            'nombre_profesor' => 'PROFESOR',
            'paterno' => 'TITULAR',
            'materno' => 'TI',
            'status_actual' => 'A',
            'origen_horario' => 'HD',
        ]);

        DB::table('horarios_det')->insert([
            'inicial' => 2026,
            'final' => 2026,
            'periodo' => 1,
            'codigo_grupo' => 'G-TI-1',
            'clave_profesor' => '999',
            'clave_asignatura' => 'TI-101',
            'dia' => 1,
            'sesion' => 1,
            'origen_horario' => 'HD',
            'activo' => true,
        ]);

        DB::table('cursos')->insert([
            'inicial' => 2026,
            'final' => 2026,
            'periodo' => 1,
            'id_plan' => 10,
            'clave_curso' => 'CURSO-TI-101',
            'clave_asignatura' => 'TI-101',
            'clave_profesor' => 999,
            'nombre_curso' => 'PROFESOR TITULAR TI',
            'id_campus' => 1,
            'sesiones' => 4,
            'activo' => true,
        ]);
    }

    public function test_academia_dashboard_renders_for_authorized_user(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/academia');

        $response->assertOk();
        $response->assertSee('Dashboard de Académica');
        $response->assertSee('Alumnos Inscritos');
        $response->assertSee('Grupos Formados');
        $response->assertSee('Profesores Asignados');
        $response->assertSee('Horarios Programados');
        $response->assertSee('Materias');
        $response->assertSee('Planes de Estudio');
        $response->assertSee('Desglose Operativo Institucional');
        $response->assertSee('Excel');
        $response->assertSee('CSV');
        $response->assertSee('PDF');
        $response->assertSee('PROGRAMACIÓN BÁSICA');
        $response->assertSee('TSU EN TECNOLOGÍAS');
    }

    public function test_academia_dashboard_switches_cycle_via_parameter(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/academia?ciclo_principal=2026-2026-1');

        $response->assertOk();
        $response->assertSee('2026-2026-1');
        $response->assertSee('Septiembre - Diciembre 2026');
    }

    public function test_academia_dashboard_kpis_json_returns_data(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->getJson('/academia/kpis-json?ciclo=2026-2026-1');

        $response->assertOk();
        $response->assertJsonStructure([
            'kpis',
            'ciclo',
        ]);
    }
}
