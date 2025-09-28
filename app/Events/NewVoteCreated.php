<?php

namespace App\Events;

use App\Models\Vote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Removed broadcasting implementation
class NewVoteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Vote $vote;

    public function __construct(Vote $vote)
    {
        $this->vote = $vote;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('poll.' . $this->vote->poll_id);
    }

    public function broadcastAs(): string
    {
        return 'NewVoteCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'poll_id' => $this->vote->poll_id,
            'option_id' => $this->vote->poll_option_id,
            'rank' => $this->vote->rank,
            'voter_identifier' => $this->vote->voter_identifier,
            'voter_name' => $this->vote->voter_name,
            'created_at' => optional($this->vote->created_at)->toDateTimeString(),
        ];
    }
}


