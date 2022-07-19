<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanDepositoAset extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_deposito_aset';
    protected $connection = 'transaksi';
}
