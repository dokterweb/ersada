<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akad extends Model
{
    use HasFactory;

    protected $fillable = ['pembiayaan_id','nomor_akad','tanggal_akad','tempat_akad','nomor_perjanjian','catatan','status',
    'file_word','file_pdf','generated_at','created_by',];

    protected $casts = [
        'tanggal_akad' => 'date',
        'generated_at' => 'date',
    ];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function pencairan()
    {
        return $this->hasOne(Pencairan::class);
    }
}
