<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bracket_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('snapshot_label');
            $table->enum('tournament_type', ['team', 'individual']);
            $table->longText('snapshot_data'); // JSON blob of all match rows
            $table->unsignedBigInteger('deleted_by')->nullable(); // FK to users.id
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bracket_snapshots');
    }
};
