<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('sexo', 20)->nullable()->after('name');
            $table->date('fecha_nacimiento')->nullable()->after('fecha_ingreso');
            $table->string('lugar_nacimiento', 100)->nullable()->after('fecha_nacimiento');
            $table->string('estado_nacimiento', 100)->nullable()->after('lugar_nacimiento');
            $table->string('nacionalidad', 50)->nullable()->after('estado_nacimiento');
            $table->string('estado_civil', 30)->nullable()->after('nacionalidad');
            $table->string('domicilio', 200)->nullable()->after('estado_civil');
            $table->string('cp', 10)->nullable()->after('domicilio');
            $table->string('ciudad', 80)->nullable()->after('cp');
            $table->string('estado', 80)->nullable()->after('ciudad');
            $table->string('telefono', 50)->nullable()->after('estado');
            $table->string('celular', 50)->nullable()->after('telefono');
            $table->string('telefono_oficina', 50)->nullable()->after('celular');
            $table->string('email', 120)->nullable()->after('telefono_oficina');
            $table->string('nivel_estudios', 100)->nullable()->after('email');
            $table->string('especialidad', 120)->nullable()->after('nivel_estudios');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'sexo', 'fecha_nacimiento', 'lugar_nacimiento', 'estado_nacimiento',
                'nacionalidad', 'estado_civil', 'domicilio', 'cp', 'ciudad', 'estado',
                'telefono', 'celular', 'telefono_oficina', 'email', 'nivel_estudios',
                'especialidad',
            ]);
        });
    }
};
