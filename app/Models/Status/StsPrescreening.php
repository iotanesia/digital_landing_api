<?php

namespace App\Models\Status;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StsPrescreening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'status_prescreening';
    protected $connection = 'status';

}
