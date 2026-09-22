<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->date('fecha_falta_programada')->nullable()->after('fecha_justificacion');
            $table->string('tipo_duracion', 20)->nullable()->after('fecha_falta_programada');
            $table->time('hora_inicio')->nullable()->after('tipo_duracion');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->text('comentarios')->nullable()->after('motivo');
            $table->text('solicitud')->nullable()->after('comentarios');

            $table->index(['fecha_falta_programada', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropIndex(['fecha_falta_programada', 'estado']);
            $table->dropColumn([
                'fecha_falta_programada',
                'tipo_duracion',
                'hora_inicio',
                'hora_fin',
                'comentarios',
                'solicitud',
            ]);
        });
    }
};
