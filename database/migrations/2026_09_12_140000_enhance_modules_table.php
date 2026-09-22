<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('icon');
            $table->boolean('is_system')->default(false)->after('sort_order');
            $table->string('group_name')->default('Módulos')->after('is_system');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('icon');
            $table->dropColumn('sort_order');
            $table->dropColumn('is_system');
            $table->dropColumn('group_name');
        });
    }
};
