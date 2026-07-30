<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembiayaan extends Model
{
    use HasFactory;

    protected $fillable = ['pengajuan_id', 'nomor_pembiayaan', 'plafond', 'tenor', 'jenis_tenor', 'persen_bunga', 'persen_administrasi', 'biaya_administrasi', 
    'materai', 'biaya_survei', 'dana_diterima', 'tanggal_akad', 'tanggal_pencairan', 'tanggal_jatuh_tempo_pertama', 'status','created_by'];

    protected $casts = [
        'tanggal_akad' => 'date',
        'tanggal_pencairan' => 'date',
        'tanggal_jatuh_tempo_pertama' => 'date',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function angsurans()
    {
        return $this->hasMany(Angsuran::class);
    }

    public function akad()
    {
        return $this->hasOne(Akad::class);
    }

    public function pelunasan()
    {
        return $this->hasOne(Pelunasan::class);
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'review' => '<span class="badge bg-warning">Review</span>',
            'akad' => '<span class="badge bg-info">Akad</span>',
            'dicairkan' => '<span class="badge bg-primary">Aktif</span>',
            'lunas' => '<span class="badge bg-success">Lunas</span>',
            default => '<span class="badge bg-dark">'.$this->status.'</span>',
        };
    }    
}