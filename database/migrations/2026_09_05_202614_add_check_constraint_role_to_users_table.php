<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL: Add CHECK constraint for role column
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` ADD CONSTRAINT `users_role_check` CHECK (`role` IN ('admin','operator'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `users` DROP CHECK `users_role_check`');
        }
    }
};
