<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class JobResultController extends Controller
{
    public function show(string $jobId)
    {
        $key = "job:{$jobId}:final";
        $result = Redis::get($key);

        if (!$result) {
            return response()->json([
                'message' => 'Result not available yet',
            ], 404);
        }

        return response()->json([
            'job_id' => $jobId,
            'result' => json_decode($result, true),
        ]);
    }
}
