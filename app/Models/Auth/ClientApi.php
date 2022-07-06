<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClientApi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'client_api';
    protected $connection = 'auth';
}
