<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->string('code', 8)->nullable()->unique()->after('title');
        });

        // kodai jau esamoms apklausoms
        foreach (DB::table('polls')->whereNull('code')->get() as $poll) {
            do {
                $code = strtoupper(Str::random(6));
            } while (DB::table('polls')->where('code', $code)->exists());

            DB::table('polls')->where('id', $poll->id)->update(['code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
