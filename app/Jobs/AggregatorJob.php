<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\JobCompleted;
use App\Models\JobMetric;
use App\Models\JobResult;
use App\Models\TemperatureJob;

class AggregatorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $jobId;
    protected int $totalChunks;

    public function __construct(string $jobId, int $totalChunks)
    {
        $this->jobId = $jobId;
        $this->totalChunks = $totalChunks;
    }
    // app/Jobs/AggregatorJob.php

    public function handle(): void
    {
        $start = microtime(true);

        Log::info("▶️ Aggregator started for job: {$this->jobId}");

        $aggregated = [];

        for ($i = 0; $i < $this->totalChunks; $i++) {
            $key = "job:{$this->jobId}:chunk:{$i}";
            $json = Redis::get($key);

            if (!$json) {
                Log::error("❌ Missing chunk #$i for job {$this->jobId}");
                continue;
            }

            $chunkStats = json_decode($json, true);

            foreach ($chunkStats as $city => $data) {
                if (!isset($aggregated[$city])) {
                    $aggregated[$city] = $data;
                } else {
                    $aggregated[$city]['min'] = min($aggregated[$city]['min'], $data['min']);
                    $aggregated[$city]['max'] = max($aggregated[$city]['max'], $data['max']);
                    $aggregated[$city]['sum'] += $data['sum'];
                    $aggregated[$city]['count'] += $data['count'];
                }
            }
        }

        foreach ($aggregated as $city => &$data) {
            $data['avg'] = round($data['sum'] / $data['count'], 2);
        }

        Redis::set("job:{$this->jobId}:final", json_encode($aggregated));
        Log::info("✅ Aggregation complete for job {$this->jobId}");
        Log::info("JobResult started !!!!!!!");


        foreach ($aggregated as $city => $data) {
            JobResult::create([
                'job_id' => $this->jobId,
                'city' => $city,
                'min' => $data['min'],
                'max' => $data['max'],
                'avg' => $data['avg'],
                'sum' => $data['sum'],
                'count' => $data['count'],
            ]);
        }

        Log::info("duration calc start started !!!!!!!");
        $duration = round(microtime(true) - $start, 3);
        $memory = round(memory_get_peak_usage(true) / 1024);

        $temperatureJobId = TemperatureJob::where('job_id', $this->jobId)->first();

        if ($temperatureJobId) {
            Log::info("temperatureJobId found !!!!!");
            JobMetric::updateOrCreate(
                ['job_id' => $temperatureJobId->id],
                [
                    'execution_time' => $duration,
                    'memory_usage' => $memory,
                    'processed_rows' => array_sum(array_column($aggregated, 'count')),
                ]
            );
        } else {
            Log::info("❌ Could not find temperature job for job_id {$this->jobId}");
        }
        Log::info("duration calc start end !!!!!!!");

        // broadcast(new JobCompleted($this->jobId, $aggregated));
    }
}
