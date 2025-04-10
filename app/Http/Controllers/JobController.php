<?php
// app/Http/Controllers/JobController.php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\GenerateFileJob;

class JobController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'rows' => 'required|integer|min:100|max:1000000000'
        ]);

        $jobId = Str::uuid()->toString();
        GenerateFileJob::dispatch($request->rows, $jobId);

        return response()->json([
            'message' => 'Job submitted successfully',
            'job_id' => $jobId
        ]);
    }
}
