<?php

namespace Tests\Feature;

use App\Models\Poll;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class PollUuidMigrationTest extends TestCase
{
    use DatabaseMigrations;

    private function migration(): Migration
    {
        return require database_path(
            'migrations/2026_10_30_102158_convert_poll_ids_to_uuids.php'
        );
    }

    private function seedLegacyPolls(): void
    {
        $this->migration()->down();

        foreach ([17 => 'FIRST1', 91 => 'SECOND'] as $id => $code) {
            DB::table('polls')->insert([
                'id' => $id,
                'title' => 'Sena apklausa '.$id,
                'code' => $code,
                'created_at' => '2026-10-20 09:00:00',
                'updated_at' => '2026-10-20 09:00:00',
            ]);

            DB::table('poll_options')->insert([
                'id' => $id,
                'poll_id' => $id,
                'text' => 'Senas atsakymas '.$id,
                'created_at' => '2026-10-20 09:00:00',
                'updated_at' => '2026-10-20 09:00:00',
            ]);

            DB::table('votes')->insert([
                'id' => $id,
                'poll_id' => $id,
                'poll_option_id' => $id,
                'visitor_id' => 'visitor-'.$id,
                'created_at' => '2026-10-20 09:00:00',
                'updated_at' => '2026-10-20 09:00:00',
            ]);
        }
    }

    public function test_existing_polls_options_votes_codes_and_timestamps_survive_conversion(): void
    {
        $this->seedLegacyPolls();
        $this->migration()->up();

        foreach ([17 => 'FIRST1', 91 => 'SECOND'] as $oldId => $code) {
            $poll = Poll::where('code', $code)->firstOrFail();

            $this->assertTrue(Str::isUuid($poll->id));
            $this->assertSame('4', $poll->id[14]);
            $this->assertSame('Sena apklausa '.$oldId, $poll->title);

            $this->assertSame(
                '2026-10-20 09:00:00',
                $poll->getRawOriginal('created_at')
            );

            $this->assertSame(
                $oldId,
                $poll->options()->firstOrFail()->id
            );

            $this->assertSame(
                $poll->id,
                $poll->options()->firstOrFail()->poll_id
            );

            $vote = $poll->votes()->firstOrFail();

            $this->assertSame($oldId, $vote->id);
            $this->assertSame($oldId, $vote->poll_option_id);
            $this->assertSame('visitor-'.$oldId, $vote->visitor_id);

            $this->assertSame(
                '2026-10-20 09:00:00',
                $vote->getRawOriginal('created_at')
            );

            $this->assertTrue($vote->option->poll->is($poll));

            $this->get(route('polls.join.code', $code))
                ->assertRedirect(route('polls.show', $poll));
        }

        $this->assertDatabaseCount('polls', 2);
        $this->assertDatabaseCount('poll_options', 2);
        $this->assertDatabaseCount('votes', 2);

        $this->assertSame(
            [],
            DB::select('PRAGMA foreign_key_check')
        );

        $newPoll = Poll::factory()->create();

        $this->assertTrue(Str::isUuid($newPoll->id));

        $option = $newPoll->options()->create([
            'text' => 'Naujas atsakymas',
        ]);

        $this->assertGreaterThan(91, $option->id);

        $this->post(route('polls.vote', $newPoll), [
            'poll_option_id' => $option->id,
            'visitor_id' => 'new-visitor',
        ])->assertRedirect(route('polls.results', $newPoll));

        $this->assertSame(1, $newPoll->votes()->count());
    }

    public function test_rollback_preserves_records_and_relations_and_allows_migrating_again(): void
    {
        $this->seedLegacyPolls();

        $migration = $this->migration();
        $migration->up();
        $migration->down();

        $poll = DB::table('polls')
            ->where('code', 'SECOND')
            ->first();

        $this->assertIsInt($poll->id);

        $this->assertDatabaseHas('poll_options', [
            'id' => 91,
            'poll_id' => $poll->id,
        ]);

        $this->assertDatabaseHas('votes', [
            'id' => 91,
            'poll_id' => $poll->id,
            'poll_option_id' => 91,
        ]);

        $this->assertSame(
            [],
            DB::select('PRAGMA foreign_key_check')
        );

        $migration->up();

        $this->assertTrue(
            Str::isUuid(
                Poll::where('code', 'SECOND')->firstOrFail()->id
            )
        );

        $this->assertDatabaseCount('votes', 2);
    }

    public function test_conversion_rolls_back_if_an_existing_reference_is_invalid(): void
    {
        $this->seedLegacyPolls();

        Schema::disableForeignKeyConstraints();

        DB::table('poll_options')->insert([
            'poll_id' => 999,
            'text' => 'Orphan',
        ]);

        Schema::enableForeignKeyConstraints();

        try {
            $this->migration()->up();

            $this->fail(
                'An orphaned reference must prevent migration.'
            );
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString(
                'orphaned poll reference',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseHas('polls', [
            'id' => 17,
            'code' => 'FIRST1',
        ]);

        $this->assertDatabaseCount('poll_options', 3);
        $this->assertDatabaseCount('votes', 2);

        $this->assertFalse(
            Schema::hasTable('uuid_conversion_polls')
        );

        DB::table('poll_options')
            ->where('poll_id', 999)
            ->delete();

        $this->migration()->up();
    }
}
