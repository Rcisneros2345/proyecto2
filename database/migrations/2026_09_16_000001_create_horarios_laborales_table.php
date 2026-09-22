<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_laborales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana'); // 1=Lunes, 7=Domingo
            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->time('hora_salida_comer')->nullable();
            $table->time('hora_regreso_comer')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['employee_id', 'dia_semana'], 'uk_horario_laboral_emp_dia');
            $table->index(['employee_id', 'activo'], 'idx_horario_laboral_emp_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_laborales');
    }
};
