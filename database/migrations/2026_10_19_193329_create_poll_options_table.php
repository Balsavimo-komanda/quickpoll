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
            $table->id();

            $table->foreignUuid('poll_id')
                ->constrained('polls')
                ->cascadeOnDelete();

            $table->string('text');
            $table->timestamps();
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
