<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->rebuildTables(useUuids: true);
    }

    public function down(): void
    {
        $this->rebuildTables(useUuids: false);
    }

    /**
     * Rebuild the related tables together, preserving option and vote IDs.
     * A rollback assigns fresh integer poll IDs while preserving their relations.
     */
    private function rebuildTables(bool $useUuids): void
    {
        DB::transaction(function () use ($useUuids): void {
            $prefix = $useUuids
                ? 'uuid_conversion_'
                : 'integer_conversion_';

            $pollsTable = $prefix.'polls';
            $optionsTable = $prefix.'poll_options';
            $votesTable = $prefix.'votes';

            Schema::create(
                $pollsTable,
                function (Blueprint $table) use ($useUuids): void {
                    if ($useUuids) {
                        $table->uuid('id')->primary();
                    } else {
                        $table->id();
                    }

                    $table->string('title');
                    $table->timestamps();
                    $table->string('code', 8)->nullable()->unique();
                }
            );

            Schema::create(
                $optionsTable,
                function (Blueprint $table) use (
                    $useUuids,
                    $pollsTable
                ): void {
                    $table->id();

                    if ($useUuids) {
                        $table->foreignUuid('poll_id')
                            ->constrained($pollsTable)
                            ->cascadeOnDelete();
                    } else {
                        $table->foreignId('poll_id')
                            ->constrained($pollsTable)
                            ->cascadeOnDelete();
                    }

                    $table->string('text');
                    $table->timestamps();
                }
            );

            Schema::create(
                $votesTable,
                function (Blueprint $table) use (
                    $useUuids,
                    $pollsTable,
                    $optionsTable
                ): void {
                    $table->id();

                    if ($useUuids) {
                        $table->foreignUuid('poll_id')
                            ->constrained($pollsTable)
                            ->cascadeOnDelete();
                    } else {
                        $table->foreignId('poll_id')
                            ->constrained($pollsTable)
                            ->cascadeOnDelete();
                    }

                    $table->foreignId('poll_option_id')
                        ->constrained($optionsTable)
                        ->cascadeOnDelete();

                    $table->string('visitor_id');
                    $table->unique(['poll_id', 'visitor_id']);
                    $table->timestamps();
                }
            );

            $pollIds = [];
            $nextId = 0;

            DB::table('polls')
                ->orderBy('id')
                ->chunkById(
                    500,
                    function (Collection $polls) use (
                        &$pollIds,
                        &$nextId,
                        $useUuids,
                        $pollsTable
                    ): void {
                        $rows = [];

                        foreach ($polls as $poll) {
                            $newId = $useUuids
                                ? (string) Str::uuid()
                                : ++$nextId;

                            $pollIds[(string) $poll->id] = $newId;

                            $rows[] = array_replace(
                                (array) $poll,
                                ['id' => $newId]
                            );
                        }

                        DB::table($pollsTable)->insert($rows);
                    }
                );

            foreach ([
                         'poll_options' => $optionsTable,
                         'votes' => $votesTable,
                     ] as $source => $target) {
                DB::table($source)
                    ->orderBy('id')
                    ->chunkById(
                        500,
                        function (Collection $records) use (
                            $pollIds,
                            $target
                        ): void {
                            $rows = [];

                            foreach ($records as $record) {
                                if (! isset(
                                    $pollIds[(string) $record->poll_id]
                                )) {
                                    throw new RuntimeException(
                                        'Cannot convert an orphaned poll reference in '
                                        .$target
                                    );
                                }

                                $rows[] = array_replace(
                                    (array) $record,
                                    [
                                        'poll_id' => $pollIds[
                                        (string) $record->poll_id
                                        ],
                                    ]
                                );
                            }

                            DB::table($target)->insert($rows);
                        }
                    );
            }

            Schema::drop('votes');
            Schema::drop('poll_options');
            Schema::drop('polls');

            Schema::rename($pollsTable, 'polls');
            Schema::rename($optionsTable, 'poll_options');
            Schema::rename($votesTable, 'votes');

            Schema::table(
                'polls',
                function (Blueprint $table) use ($prefix): void {
                    $table->dropUnique($prefix.'polls_code_unique');
                    $table->unique('code', 'polls_code_unique');
                }
            );
        });
    }
};
