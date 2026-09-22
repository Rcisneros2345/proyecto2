<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->string('section', 60);
            $table->string('label', 100);
            $table->string('route_name', 150);
            $table->string('icon', 80)->nullable();
            $table->string('permission_action', 30)->default('view');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('admin_only')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique('route_name');
            $table->index(['section', 'sort_order', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
