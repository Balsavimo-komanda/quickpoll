<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PollUuidTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_polls_store_distinct_uuid_v4_primary_keys_and_use_them_in_urls(): void
    {
        foreach (['Pirma apklausa', 'Antra apklausa'] as $title) {
            $response = $this->post(route('polls.store'), [
                'title' => $title,
                'options' => ['Taip', 'Ne'],
            ]);

            $poll = Poll::where('title', $title)->firstOrFail();

            $this->assertTrue(Str::isUuid($poll->id));
            $this->assertSame('4', $poll->id[14]);
            $this->assertFalse($poll->getIncrementing());
            $this->assertSame('string', $poll->getKeyType());

            $this->assertDatabaseHas('polls', [
                'id' => $poll->id,
                'title' => $title,
            ]);

            $this->assertStringEndsWith(
                '/polls/'.$poll->id,
                route('polls.show', $poll)
            );

            $response->assertRedirect(route('polls.show', $poll));

            $this->get(route('polls.show', $poll))
                ->assertOk()
                ->assertSee($title);

            $this->assertSame(
                [$poll->id],
                $poll->options()->pluck('poll_id')->unique()->all()
            );
        }

        $this->assertCount(2, Poll::pluck('id')->unique());
    }

    public function test_voting_and_eloquent_relations_work_with_uuid_poll_ids(): void
    {
        $poll = Poll::factory()->create();
        $option = $poll->options()->create(['text' => 'Taip']);

        $this->post(route('polls.vote', $poll), [
            'poll_option_id' => $option->id,
            'visitor_id' => 'uuid-test-visitor',
        ])->assertRedirect(route('polls.results', $poll));

        $this->assertDatabaseHas('votes', [
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'visitor_id' => 'uuid-test-visitor',
        ]);

        $vote = $poll->votes()->firstOrFail();

        $this->assertTrue($vote->poll->is($poll));
        $this->assertTrue($option->poll->is($poll));
        $this->assertTrue($vote->option->is($option));

        $this->get(route('polls.results', $poll))
            ->assertOk()
            ->assertSee('Iš viso balsų: 1')
            ->assertSee('(tavo balsas)')
            ->assertSee('100%');
    }

    public function test_duplicate_votes_remain_blocked_for_a_uuid_poll(): void
    {
        $poll = Poll::factory()->create();
        $option = $poll->options()->create(['text' => 'Taip']);

        $input = [
            'poll_option_id' => $option->id,
            'visitor_id' => 'same-visitor',
        ];

        $this->post(route('polls.vote', $poll), $input)
            ->assertRedirect(route('polls.results', $poll));

        $this->post(route('polls.vote', $poll), $input)
            ->assertRedirect(route('polls.results', $poll))
            ->assertSessionHas('error');

        $this->assertSame(1, $poll->votes()->count());
    }

    public function test_an_option_from_another_uuid_poll_cannot_receive_a_vote(): void
    {
        $poll = Poll::factory()->create();
        $otherPoll = Poll::factory()->create();

        $otherOption = $otherPoll->options()->create([
            'text' => 'Svetimas variantas',
        ]);

        $this->from(route('polls.show', $poll))
            ->post(route('polls.vote', $poll), [
                'poll_option_id' => $otherOption->id,
                'visitor_id' => 'wrong-poll-visitor',
            ])
            ->assertRedirect(route('polls.show', $poll))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('votes', 0);
    }

    public function test_poll_codes_still_redirect_to_the_uuid_url(): void
    {
        $poll = Poll::factory()->create();

        $this->get(route('polls.join.code', $poll->code))
            ->assertRedirect(route('polls.show', $poll));

        $this->post(route('polls.join'), [
            'code' => strtolower($poll->code),
        ])->assertRedirect(route('polls.show', $poll));
    }

    public function test_numeric_malformed_and_unknown_uuid_poll_urls_return_not_found(): void
    {
        foreach (['1', 'not-a-uuid', (string) Str::uuid()] as $id) {
            $this->get('/polls/'.$id)->assertNotFound();

            $this->get('/polls/'.$id.'/results')
                ->assertNotFound();

            $this->post('/polls/'.$id.'/vote', [
                'poll_option_id' => 1,
                'visitor_id' => 'unknown-poll-visitor',
            ])->assertNotFound();
        }
    }

    public function test_deleting_a_uuid_poll_cascades_to_its_options_and_votes(): void
    {
        $poll = Poll::factory()->create();
        $option = $poll->options()->create(['text' => 'Taip']);

        $poll->votes()->create([
            'poll_option_id' => $option->id,
            'visitor_id' => 'cascade-visitor',
        ]);

        $poll->delete();

        $this->assertDatabaseCount('polls', 0);
        $this->assertDatabaseCount('poll_options', 0);
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_poll_option_foreign_key_rejects_an_unknown_uuid(): void
    {
        $this->expectException(QueryException::class);

        (new PollOption)->create([
            'poll_id' => (string) Str::uuid(),
            'text' => 'Invalid',
        ]);
    }

    public function test_vote_foreign_key_rejects_an_unknown_uuid(): void
    {
        $poll = Poll::factory()->create();
        $option = $poll->options()->create(['text' => 'Taip']);

        $this->expectException(QueryException::class);

        (new Vote)->create([
            'poll_id' => (string) Str::uuid(),
            'poll_option_id' => $option->id,
            'visitor_id' => 'invalid-reference',
        ]);
    }
}
