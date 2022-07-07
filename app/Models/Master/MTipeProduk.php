<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MTipeProduk extends Model
{
    use HasFactory;
    protected $table = 'tipe_produk';
    protected $connection = 'master';

    public $fillable = [
        'nama',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
