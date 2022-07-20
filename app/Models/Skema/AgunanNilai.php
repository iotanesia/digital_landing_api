<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgunanNilai extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'agunan_skema_nilai';
    protected $connection = 'skema';
    public $fillable = [
        'id_agunan',
        'kolom',
        'nilai',
        'kode',
        'kode',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
