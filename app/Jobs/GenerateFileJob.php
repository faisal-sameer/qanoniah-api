<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\ChunkProcessingJob;
use App\Jobs\AggregatorJob;
use SplFileObject;

class GenerateFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $rowCount;
    protected string $jobId;

    public function __construct(int $rowCount, string $jobId)
    {
        $this->rowCount = $rowCount;
        $this->jobId = $jobId;
    }

    public function handle(): void
    {
        $cities = ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina'];
        $filePath = "measurements/{$this->jobId}.txt";
        $fullPath = storage_path("app/{$filePath}");

        Log::info("🔨 Creating file: {$fullPath}");

        Storage::makeDirectory('measurements');

        $handle = @fopen($fullPath, 'w');
        if (!$handle) {
            Log::error("❌ Failed to open file for writing: {$fullPath}");
            return;
        }

        Log::info("✍️ Writing {$this->rowCount} lines...");

        for ($i = 0; $i < $this->rowCount; $i++) {
            $city = $cities[array_rand($cities)];
            $temperature = number_format(rand(200, 500) / 10, 1);
            fwrite($handle, "{$city};{$temperature}\n");
        }

        fclose($handle);
        Log::info("✅ File created: {$fullPath}");

        $maxTries = 5;
        $waitMs = 500;
        for ($i = 0; $i < $maxTries; $i++) {
            if (file_exists($fullPath)) break;
            Log::warning("⏳ Waiting for file to exist... try #$i");
            usleep($waitMs * 1000);
        }

        if (!file_exists($fullPath)) {
            Log::error("❌ Still missing file: {$fullPath}");
            return;
        }

        $chunkSize = 100000;
        $chunk = [];
        $chunkIndex = 0;
        $totalLines = 0;

        $reader = new SplFileObject($fullPath);
        $reader->setFlags(SplFileObject::DROP_NEW_LINE);

        while (!$reader->eof()) {
            $line = $reader->fgets();
            if (trim($line) === '') continue;

            $chunk[] = $line;
            $totalLines++;

            if (count($chunk) === $chunkSize) {
                Log::info("📦 Dispatching chunk #$chunkIndex with " . count($chunk) . " lines");
                ChunkProcessingJob::dispatch($chunk, $this->jobId, $chunkIndex);
                $chunk = [];
                $chunkIndex++;
            }
        }

        if (count($chunk) > 0) {
            Log::info("📦 Dispatching chunk #$chunkIndex with " . count($chunk) . " lines");
            ChunkProcessingJob::dispatch($chunk, $this->jobId, $chunkIndex);
            $chunkIndex++;
        }

        Redis::set("job:{$this->jobId}:total_chunks", $chunkIndex);

        Log::info("✅ Total lines: {$totalLines}");
        Log::info("✅ Split into {$chunkIndex} chunks");

        AggregatorJob::dispatch($this->jobId, $chunkIndex)->delay(now()->addSeconds(5));
        Log::info("📊 AggregatorJob dispatched for job: {$this->jobId}");
    }
}
