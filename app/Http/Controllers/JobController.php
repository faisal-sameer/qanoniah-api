<?php
// app/Http/Controllers/JobController.php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\GenerateFileJob;
use App\Models\TemperatureJob;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'rows' => 'required|integer|min:100|max:1000000000'
        ]);

        $jobId = Str::uuid()->toString();

        \App\Models\TemperatureJob::create([
            'id' => $jobId,
            'job_id' => $jobId,
            'user_id' => auth()->id(),
            'rows' => $request->rows,
            'status' => 'pending',
        ]);

        GenerateFileJob::dispatch($request->rows, $jobId);

        return response()->json([
            'message' => 'Job submitted successfully',
            'job_id' => $jobId,
        ]);
    }

    public function index()
    {
        Log::info('📥 API /api/jobs hit');

        $jobs = TemperatureJob::with('results')->withCount('results')->latest()->get();
        $jobs = TemperatureJob::with('metrics')->latest()->get();

        Log::info('📊 Jobs fetched', ['count' => $jobs->count(), 'data' => $jobs]);

        return response()->json($jobs);
    }
}
