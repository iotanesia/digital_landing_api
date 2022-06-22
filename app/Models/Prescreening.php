<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Prescreening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'prescreening_aktifitas';

    public $fillable = [
        'metode',
        'keterangan',
        'status',
        'id_eform',
        'id_map_rules_skema_eksternal',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
    ];

    public function refMetodeEksternal()
    {
        return $this->belongsTo(MMetodeEksternal::class,'id','id_map_rules_skema_eksternal');
    }

    public function refLog()
    {
        return $this->belongsTo(LogPrescreening::class,'id','id_prescreening');
    }

    public static function boot()
    {
        parent::boot();

        
    }
}
