<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MSubSubProduk extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sub_sub_produk';
    protected $connection = 'master';

    public $fillable = [
        'id_sub_produk',
        'nama',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
