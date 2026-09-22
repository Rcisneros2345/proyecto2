<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_capture_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nivel', 30)->nullable();
            $table->string('id_campus', 30)->nullable();
            $table->unsignedInteger('inicial')->nullable();
            $table->unsignedInteger('final')->nullable();
            $table->unsignedInteger('periodo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'active']);
            $table->index(['nivel', 'id_campus']);
            $table->index(['inicial', 'final', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_capture_assignments');
    }
};
