<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencia_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidencia_id')->constrained('incidencias')->cascadeOnDelete();
            $table->unsignedInteger('sequence')->default(1);
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('approver_user_id')->nullable();
            $table->unsignedBigInteger('approver_employee_id')->nullable();
            $table->string('approver_professor_clave', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('area_id')->references('id')->on('areas')->nullOnDelete();
            $table->foreign('approver_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approver_employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('approver_professor_clave')->references('clave_profesor')->on('profesores')->nullOnDelete();

            $table->index(['incidencia_id', 'sequence']);
            $table->index(['incidencia_id', 'status']);
            $table->index(['approver_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencia_approvals');
    }
};
