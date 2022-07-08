<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MetodePrescreening extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'prescreening_metode';
    protected $connection = 'skema';
}