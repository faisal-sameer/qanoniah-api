<?php

namespace App\Http\Controllers;

use App\Models\JobResult;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
    public function download(string $jobId)
    {
        $results = JobResult::where('job_id', $jobId)->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found for this job.'], 404);
        }

        $csv = "City,Min,Max,Avg,Sum,Count\n";
        foreach ($results as $row) {
            $csv .= "{$row->city},{$row->min},{$row->max},{$row->avg},{$row->sum},{$row->count}\n";
        }

        $filename = "job-{$jobId}-results.csv";

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
