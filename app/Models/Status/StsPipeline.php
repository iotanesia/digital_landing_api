<?php

namespace App\Models\Status;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StsPipeline extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'status_pipeline';
    protected $connection = 'status';
}
