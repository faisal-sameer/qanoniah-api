<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\JobResult;

class TemperatureJob extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'job_id',
        'user_id',
        'rows',
        'status',
        'execution_time',
        'memory_usage',
        'completed_at'
    ];
    protected $table = 'temperature_jobs'; // ✅ اسم الجدول الصحيح

    protected static function booted()
    {
        static::creating(function ($job) {
            $job->id = (string) Str::uuid();
        });
    }
    public function results()
    {
        return $this->hasMany(JobResult::class, 'job_id', 'id');
    }

    public function metrics()
    {
        return $this->hasOne(JobMetric::class, 'job_id', 'id');
    }
}
