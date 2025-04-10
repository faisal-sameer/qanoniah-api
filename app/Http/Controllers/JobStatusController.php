<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\JsonResponse;

class JobStatusController extends Controller
{
    public function show(string $jobId): JsonResponse
    {
        $totalChunksKey = "job:{$jobId}:total_chunks";
        $finalResultKey = "job:{$jobId}:final";

        $totalChunks = (int) Redis::get($totalChunksKey);

        if ($totalChunks === 0) {
            return response()->json([
                'job_id' => $jobId,
                'status' => 'pending',
                'message' => 'No chunks recorded yet'
            ], 202);
        }

        $completedChunks = 0;
        for ($i = 0; $i < $totalChunks; $i++) {
            if (Redis::exists("job:{$jobId}:chunk:{$i}")) {
                $completedChunks++;
            }
        }

        $progress = round(($completedChunks / $totalChunks) * 100, 2);
        $status = Redis::exists($finalResultKey) ? 'completed' : ($completedChunks > 0 ? 'in_progress' : 'pending');

        return response()->json([
            'job_id' => $jobId,
            'status' => $status,
            'progress' => "{$progress}%",
            'completed_chunks' => $completedChunks,
            'total_chunks' => $totalChunks
        ]);
    }
}
