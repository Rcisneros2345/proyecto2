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
            $table->timestamp('visto_at')->nullable()->after('autorizado_por_user_id');
            $table->unsignedBigInteger('visto_por_user_id')->nullable()->after('visto_at');
            $table->timestamp('firmado_at')->nullable()->after('visto_por_user_id');
            $table->unsignedBigInteger('firmado_por_user_id')->nullable()->after('firmado_at');

            $table->foreign('visto_por_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('firmado_por_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign(['visto_por_user_id']);
            $table->dropForeign(['firmado_por_user_id']);
            $table->dropColumn(['visto_at', 'visto_por_user_id', 'firmado_at', 'firmado_por_user_id']);
        });
    }
};
