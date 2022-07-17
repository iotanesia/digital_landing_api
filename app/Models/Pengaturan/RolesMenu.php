<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolesMenu extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'roles_menu';
    protected $connection = 'pengaturan';
}
