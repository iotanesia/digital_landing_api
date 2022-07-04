<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class UserRole extends Model
{
    use HasFactory;
    protected $table = 'users_roles';
    protected $connection = 'auth';

    protected $fillable = [
        'id_user',
        'id_role',
        'is_current',
    ];

    const is_current = 1;

    public function refRole()
    {
        return $this->belongsTo(Role::class,'id_role','id');
    }

}
