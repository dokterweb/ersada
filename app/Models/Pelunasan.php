<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelunasan extends Model
{
    use HasFactory;

    protected $fillable = ['pembiayaan_id','nomor_pelunasan','tanggal_pelunasan','sisa_pokok','sisa_bunga',
                            'denda','diskon','total_pelunasan','keterangan','created_by',];

    protected $casts = [
        'tanggal_pelunasan' => 'date',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
