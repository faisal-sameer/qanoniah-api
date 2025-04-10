<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class JobCompleted implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(
        public string $jobId,
        public array $result
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("job.{$this->jobId}");
    }

    public function broadcastAs(): string
    {
        return 'job-completed';
    }
}
