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
            $table->id(); // Primary key for the vote.

            // Foreign key linking this vote to overall poll.
            // If the poll is deleted, its votes are automatically deleted too.
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();

            // Foreign key linking this vote to the specific option chosen.
            // If the poll is deleted, it will delete its options too, thus its votes.
            $table->foreignId('poll_option_id')->constrained('poll_options')->cascadeOnDelete();

            // Stores the browser fingerprint hash.
            $table->string('visitor_id');

            // This is not a database column, but a constraint!
            // Ensures a visitor can only register ONE vote per poll total.
            $table->unique(['poll_id', 'visitor_id']);

            $table->timestamps(); // created_at and updated_at timestamps.
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
