<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CPromo extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'content_promo';

    public $fillable = [
        'judul',
        'deskripsi',
        'foto',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
