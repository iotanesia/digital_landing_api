<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserRole extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_roles';
    protected $connection = 'auth';

    protected $fillable = [
        'id_user',
        'id_role',
        'is_current',
    ];

}
