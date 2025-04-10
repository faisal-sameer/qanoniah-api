<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\JobProgressUpdated;

class ChunkProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $lines;
    protected string $jobId;
    protected int $chunkIndex;

    public function __construct(array $lines, string $jobId, int $chunkIndex)
    {
        $this->lines = $lines;
        $this->jobId = $jobId;
        $this->chunkIndex = $chunkIndex;
    }

    public function handle(): void
    {
        \Log::info("Chunk job running for: {$this->jobId} | chunk {$this->chunkIndex}");
        \Log::info("Line count: " . count($this->lines));

        $stats = [];

        foreach ($this->lines as $line) {
            [$city, $temp] = explode(';', trim($line));

            $temp = floatval($temp);

            if (!isset($stats[$city])) {
                $stats[$city] = [
                    'min' => $temp,
                    'max' => $temp,
                    'sum' => $temp,
                    'count' => 1
                ];
            } else {
                $stats[$city]['min'] = min($stats[$city]['min'], $temp);
                $stats[$city]['max'] = max($stats[$city]['max'], $temp);
                $stats[$city]['sum'] += $temp;
                $stats[$city]['count'] += 1;
            }
        }
        \Log::info("Storing to Redis: job:{$this->jobId}:chunk:{$this->chunkIndex}");


        // تخزين نتائج chunk في Redis
        Redis::set("job:{$this->jobId}:chunk:{$this->chunkIndex}", json_encode($stats));
        // 📡 بث التقدم

        broadcast(new JobProgressUpdated(
            $this->jobId,
            $this->chunkIndex,
            count(Redis::keys("job:{$this->jobId}:chunk:*")),
            count($this->lines)
        ));
    }
}
