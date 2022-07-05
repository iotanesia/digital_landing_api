<?php

namespace App\Models\Status;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StsTracking extends Model
{
    use HasFactory;
    protected $table = 'status_tracking';
    protected $connection = 'status';
}
