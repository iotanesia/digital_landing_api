<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function refRole()
    {
        return $this->belongsTo(Role::class,'kode_role','kode_role');
    }

    public function refCabang()
    {
        return $this->belongsTo(MCabang::class,'kode_cabang','kode_cabang');
    }

    public function refRoleProduk()
    {
        return $this->belongsTo(RoleProduk::class,'id','id_user');
    }

    public function manyRoleProduk()
    {
        return $this->hasMany(RoleProduk::class,'id_user','id');
    }
}
