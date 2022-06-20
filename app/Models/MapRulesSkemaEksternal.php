<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MapRulesSkemaEksternal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'map_rules_skema_eksternal';
}
