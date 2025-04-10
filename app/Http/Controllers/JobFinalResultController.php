<?php
// app/Http/Controllers/JobFinalResultController.php

namespace App\Http\Controllers;

use App\Models\JobResult;
use Illuminate\Http\Request;

class JobFinalResultController extends Controller
{
    public function show(string $jobId)
    {
        $results = JobResult::where('job_id', $jobId)->get();

        if ($results->isEmpty()) {
            return response()->json([
                'job_id' => $jobId,
                'message' => 'No results found in database.'
            ], 404);
        }

        $structured = $results->mapWithKeys(function ($item) {
            return [
                $item->city => [
                    'min' => $item->min,
                    'max' => $item->max,
                    'avg' => round($item->avg, 2),
                    'sum' => $item->sum,
                    'count' => $item->count,
                ]
            ];
        });

        return response()->json([
            'job_id' => $jobId,
            'result' => $structured
        ]);
    }
}
