<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campo type a users: solo puede ser 'employee' O 'professor'
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'type')) {
                $table->enum('type', ['employee', 'professor'])->nullable()->after('role')
                    ->comment('Tipo de usuario: employee (empleado) o professor (profesor). Null = sin asignación');
            }
        });

        // Hacer auth_user_id unique en employees: cada usuario solo puede asignarse a 1 empleado
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'auth_user_id')) {
                $table->foreignId('auth_user_id')->nullable()->after('puesto_id')->constrained('users')->nullOnDelete();
            } else {
                // Si la columna ya existe, solo agregar el índice unique
                $table->unique('auth_user_id')->change();
            }
        });

        // Hacer auth_user_id unique en profesores: cada usuario solo puede asignarse a 1 profesor
        Schema::table('profesores', function (Blueprint $table) {
            if (! Schema::hasColumn('profesores', 'auth_user_id')) {
                $table->foreignId('auth_user_id')->nullable()->after('telefono')->constrained('users')->nullOnDelete();
            } else {
                // Si la columna ya existe, solo agregar el índice unique
                $table->unique('auth_user_id')->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('profesores', function (Blueprint $table) {
            $table->dropUnique(['auth_user_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['auth_user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
