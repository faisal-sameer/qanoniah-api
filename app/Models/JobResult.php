<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobResult extends Model
{
    protected $fillable = [
        'job_id',
        'city',
        'min',
        'max',
        'avg',
        'sum',
        'count'
    ];
}
