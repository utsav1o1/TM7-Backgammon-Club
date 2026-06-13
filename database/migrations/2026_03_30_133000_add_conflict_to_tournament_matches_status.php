<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'conflict' to the tournament_matches status ENUM.
     * This fixes the 500 error that occurs when both players claim a win.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `tournament_matches` MODIFY `status` ENUM('pending', 'completed', 'finalized', 'conflict') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Remove conflict rows first so rollback is safe
        DB::statement("UPDATE `tournament_matches` SET `status` = 'pending' WHERE `status` = 'conflict'");
        DB::statement("ALTER TABLE `tournament_matches` MODIFY `status` ENUM('pending', 'completed', 'finalized') NOT NULL DEFAULT 'pending'");
    }
};
