<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MKabupaten extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kabupaten';
    protected $connection = 'master';

    public $fillable = [
        'id_propinsi',
        'id_kabupaten',
        'nama',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
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

    public static function getIdClik($id) {
        try {
            // return MKabupaten::where('id_kabupaen', $id)->first()->id_clik;
            return MKabupaten::whereNotNull('id_clik')->first()->id_click;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function refPropinsi()
    {
        return $this->belongsTo(MPropinsi::class,'id_propinsi','id_propinsi');
    }
}
