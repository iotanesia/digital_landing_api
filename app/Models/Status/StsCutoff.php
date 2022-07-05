<?php

namespace App\Models\Status;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StsCutoff extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'status_cutoff';
    protected $connection = 'status';
}
