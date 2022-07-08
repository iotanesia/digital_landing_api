<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KreditMinimumPenghasilan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kredit_minimum_penghasilan';
    protected $connection = 'master';


}
