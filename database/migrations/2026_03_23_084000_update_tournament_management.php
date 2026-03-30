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
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->boolean('p1_claimed_win')->default(false);
            $table->boolean('p2_claimed_win')->default(false);
            $table->boolean('is_conflict')->default(false);
        });

        Schema::create('tournament_winners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'team' or 'solo'
            $table->string('month');
            $table->string('year');
            $table->timestamps();
        });

        Schema::create('tournament_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn(['p1_claimed_win', 'p2_claimed_win', 'is_conflict']);
        });
        Schema::dropIfExists('tournament_winners');
        Schema::dropIfExists('tournament_settings');
    }
};
