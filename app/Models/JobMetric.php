<?php

// app/Models/JobMetric.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobMetric extends Model
{
    protected $fillable = [
        'job_id',
        'execution_time',
        'memory_usage',
        'processed_rows',
    ];

    public function job()
    {
        return $this->belongsTo(TemperatureJob::class, 'job_id', 'id');
    }
}
