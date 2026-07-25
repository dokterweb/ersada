<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyBerkas extends Model
{
    use HasFactory;

    protected $table = 'survey_berkas';

    protected $fillable = ['survey_id', 'ktp_debitur', 'kk_debitur', 'ktp_pasangan', 'kk_pasangan', 'status_peminjam', 'plafond_pinjaman_lama', 'ktp_penjamin', 'kk_penjamin', 'nama_pasangan_penjamin', 'ktp_pasangan_penjamin', 'kk_pasangan_penjamin', 'slip_gaji', 'no_id_karyawan', 'lama_bekerja', 'no_telp_karyawan', 'bpjs', 'no_bpjs', 'buku_tabungan', 'nama_bank', 'kartu_atm', 'pin_atm', 'catatan_kekurangan', 'verified_at'];

    protected $casts = [
        'ktp_debitur' => 'boolean',
        'kk_debitur' => 'boolean',
        'ktp_pasangan' => 'boolean',
        'kk_pasangan' => 'boolean',
        'ktp_penjamin' => 'boolean',
        'kk_penjamin' => 'boolean',
        'ktp_pasangan_penjamin' => 'boolean',
        'kk_pasangan_penjamin' => 'boolean',
        'slip_gaji' => 'boolean',
        'bpjs' => 'boolean',
        'buku_tabungan' => 'boolean',
        'kartu_atm' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
