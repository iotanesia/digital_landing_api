<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leads extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'leads';
    protected $connection = 'transaksi';

    public $fillable = [
        "nomor_aplikasi",
        "nik",
        "cif",
        "nama",
        "no_hp",
        "email",
        "tempat_lahir",
        "tgl_lahir",
        "npwp",
        "alamat",
        "id_jenis_kelamin",
        "id_agama",
        "id_status_perkawinan",
        "nama_pasangan",
        "tempat_lahir_pasangan",
        "tgl_lahir_pasangan",
        "alamat_pasangan",
        "id_produk",
        "id_sub_produk",
        "plafond",
        "jangka_waktu",
        "id_cabang",
        "id_user",
        "foto",
        "is_pipeline",
        "is_cutoff",
        "is_prescreening",
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at"
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model){
            $model->created_by = request()->current_user->id;
        });
        static::updating(function ($model){
            $model->updated_by = request()->current_user->id;
        });
        static::deleting(function ($model){
            $model->deleted_by = request()->current_user->id;
        });
    }
}
