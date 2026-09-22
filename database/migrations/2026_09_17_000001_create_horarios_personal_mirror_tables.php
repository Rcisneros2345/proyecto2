<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Espejo fiel de los catálogos de horarios de personal en Firebird.
     *
     * Origen:
     *  - EMPLEADOS_CFGHORARIOS / EMPLEADOS_CFGHORARIOS_DET / EMPLEADOS_HORARIOS
     *    (jornada administrativa y jornada fija del PTC)
     *  - PROFESORES_HORARIOS / PROFESORES_HORARIOS_DET
     *    (jornada fija real de docentes)
     *
     * Los tipos y columnas replican DATOS.FDB sin transformación, de modo que el
     * ETL pueda insertar directamente el valor devuelto por Firebird. Las horas
     * viven en los *_DET y llegan como TIMESTAMP (base 1899-12-30), por eso se
     * conservan como datetime en lugar de time.
     *
     * No se declaran FKs a employees/profesores: PROFESORES.NUMEMPLEADO no
     * comparte espacio de claves con EMPLEADOS.NUMEMPLEADO (verificado en
     * origen, 0 de 229 coincidencias) y una FK forzaría a INSERT IGNORE a
     * descartar filas silenciosamente.
     */
    public function up(): void
    {
        Schema::create('empleados_cfghorarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedInteger('id_escuela')->default(1);
            $table->unsignedInteger('horario');
            $table->string('nombre_horario', 50)->nullable();
            $table->char('tipo_horario', 1)->nullable()->comment('F=Jornada fija');
            $table->unsignedSmallInteger('tolerancia_entrada')->nullable();
            $table->unsignedSmallInteger('tolerancia_regresodecomer')->nullable();
            $table->timestamps();

            $table->unique(['id_escuela', 'horario'], 'uk_empleados_cfghorarios');
        });

        Schema::create('empleados_cfghorarios_det', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedInteger('id_escuela')->default(1);
            $table->unsignedInteger('horario');
            $table->unsignedTinyInteger('dia_entrada')->nullable();
            $table->dateTime('hora_entrada')->nullable();
            $table->unsignedTinyInteger('dia_salida')->nullable();
            $table->dateTime('hora_salida')->nullable();
            $table->char('receso_comida', 1)->nullable()->comment('S/N');
            $table->unsignedTinyInteger('dia_salidaacomer')->nullable();
            $table->dateTime('hora_salidaacomer')->nullable();
            $table->unsignedTinyInteger('dia_regresodecomer')->nullable();
            $table->dateTime('hora_regresodecomer')->nullable();
            $table->dateTime('horas_variables')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_escuela', 'horario', 'dia_entrada', 'hora_entrada', 'dia_salida', 'hora_salida'],
                'uk_empleados_cfghorarios_det'
            );
            $table->index(['id_escuela', 'horario'], 'idx_empleados_cfghorarios_det_horario');
        });

        Schema::create('empleados_horarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedInteger('id_escuela')->default(1);
            $table->string('numempleado', 30);
            $table->unsignedInteger('horario');
            $table->dateTime('fecha_inicial')->nullable();
            $table->dateTime('fecha_final')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_escuela', 'numempleado', 'horario', 'fecha_inicial', 'fecha_final'],
                'uk_empleados_horarios'
            );
            $table->index(['numempleado', 'horario'], 'idx_empleados_horarios_numempleado');
        });

        Schema::create('profesores_horarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedInteger('id_escuela')->default(1);
            $table->unsignedInteger('id_profesores_horarios');
            $table->string('descripcion', 100)->nullable();
            $table->dateTime('fecha_desde')->nullable();
            $table->dateTime('fecha_hasta')->nullable();
            $table->timestamps();

            $table->unique(['id_escuela', 'id_profesores_horarios'], 'uk_profesores_horarios');
        });

        Schema::create('profesores_horarios_det', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedInteger('id_escuela')->default(1);
            $table->unsignedInteger('id_profesores_horarios');
            $table->unsignedTinyInteger('id_num');
            $table->unsignedTinyInteger('dia')->comment('1=Lun ... 7=Dom');
            $table->dateTime('hora_desde')->nullable();
            $table->dateTime('hora_hasta')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_escuela', 'id_profesores_horarios', 'id_num'],
                'uk_profesores_horarios_det'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesores_horarios_det');
        Schema::dropIfExists('profesores_horarios');
        Schema::dropIfExists('empleados_horarios');
        Schema::dropIfExists('empleados_cfghorarios_det');
        Schema::dropIfExists('empleados_cfghorarios');
    }
};
