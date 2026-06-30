<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cabang extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cabangs';

    protected $fillable=['kode','nama_cabang','alamat'];

    public function cabangs()
    {
        return $this->hasMany(Cabang::class,'cabang_id');
    }
}
