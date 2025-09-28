<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    protected function createPoll(bool $allowMultiple = false): array
    {
        $poll = Poll::factory()->create([
            'allow_multiple' => $allowMultiple,
        ]);

        $optA = PollOption::factory()->create(['poll_id' => $poll->id]);
        $optB = PollOption::factory()->create(['poll_id' => $poll->id]);

        return [$poll, $optA, $optB];
    }

    public function test_guest_cannot_vote_twice_by_session(): void
    {
        [$poll, $optA] = $this->createPoll();

        $this->withSession(['_seed' => 'keep'])
            ->post(route('polls.vote', $poll->slug), [
                'option_id' => $optA->id,
            ])->assertRedirect(route('polls.show', $poll->slug));

        // Giữ nguyên session hiện tại
        $this->withSession(['_seed' => 'keep'])
            ->post(route('polls.vote', $poll->slug), [
                'option_id' => $optA->id,
            ])->assertSessionHas('error');
    }

    public function test_user_cannot_vote_twice(): void
    {
        [$poll, $optA] = $this->createPoll();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('polls.vote', $poll->slug), [
                'option_id' => $optA->id,
            ])->assertRedirect(route('polls.show', $poll->slug));

        $this->actingAs($user)
            ->post(route('polls.vote', $poll->slug), [
                'option_id' => $optA->id,
            ])->assertSessionHas('error');
    }

    public function test_allow_multiple_per_option_once(): void
    {
        [$poll, $optA, $optB] = $this->createPoll(true);

        $this->withSession([])
            ->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
            ->post(route('polls.vote', $poll->slug), [
                'options' => [$optA->id, $optB->id],
            ])->assertRedirect(route('polls.show', $poll->slug));

        $this->withSession([])
            ->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
            ->post(route('polls.vote', $poll->slug), [
                'options' => [$optA->id],
            ])->assertSessionHas('error');
    }
}


