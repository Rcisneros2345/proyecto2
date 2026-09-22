<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->unsignedBigInteger('head_employee_id')->nullable()->after('parent_id');
            $table->string('code', 50)->nullable()->after('head_employee_id')->unique();
            $table->string('name', 120)->nullable()->after('code');
            $table->unsignedTinyInteger('level')->default(1)->after('name');
            $table->string('status', 20)->default('active')->after('level');

            $table->foreign('parent_id')
                ->references('id')
                ->on('areas')
                ->nullOnDelete();

            $table->foreign('head_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->index(['parent_id', 'status']);
            $table->index(['head_employee_id', 'status']);
        });

        DB::table('areas')->whereNull('parent_id')->update([
            'level' => 1,
            'status' => 'active',
            'name' => DB::raw('COALESCE(name, descripcion)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['head_employee_id']);
            $table->dropIndex(['areas_parent_id_status_index']);
            $table->dropIndex(['areas_head_employee_id_status_index']);
            $table->dropColumn(['parent_id', 'head_employee_id', 'code', 'name', 'level', 'status']);
        });
    }
};
