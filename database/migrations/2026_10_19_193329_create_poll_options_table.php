<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     * Creates the 'poll_options' table to store choices for each poll.
     */
    public function up(): void
    {
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id(); // Primary key for the option.

            // Foreign key linking this option to a specific poll.
            // If the poll is deleted, its options are automatically deleted too.
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();

            $table->string('text'); // The text of the choice.
            $table->timestamps(); // created_at and updated_at timestamps.
        });
    }

    /**
      * Reverse the migrations.
      */
    public function down(): void
    {
        Schema::dropIfExists('poll_options');
    }
};
