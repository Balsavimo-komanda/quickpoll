<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the 'votes' table to track anonymous votes using browser fingerprints.
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('poll_id')
                ->constrained('polls')
                ->cascadeOnDelete();

            $table->foreignId('poll_option_id')
                ->constrained('poll_options')
                ->cascadeOnDelete();

            $table->string('visitor_id');
            $table->unique(['poll_id', 'visitor_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
