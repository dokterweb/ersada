<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelunasan extends Model
{
    use HasFactory;

    protected $fillable = ['pembiayaan_id', 'nomor_pelunasan', 'tanggal_pelunasan', 'jenis_pelunasan', 'status', 'sisa_pokok', 'sisa_bunga', 'denda', 'total_sebelum_diskon', 'diskon', 'diskon_disetujui', 'total_pelunasan', 'alasan_diskon', 'keterangan', 'created_by', 'approved_by', 'approved_at', 'catatan_approval', 'paid_by', 'paid_at'];

   protected $casts = [
    'tanggal_pelunasan' => 'date',
    'approved_at' => 'datetime',
    'paid_at' => 'datetime',

    'sisa_pokok' => 'integer',
    'sisa_bunga' => 'integer',
    'denda' => 'integer',
    'total_sebelum_diskon' => 'integer',
    'diskon' => 'integer',
    'diskon_disetujui' => 'integer',
    'total_pelunasan' => 'integer',
];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function angsurans()
    {
        return $this->hasMany(Angsuran::class);
    }
}
