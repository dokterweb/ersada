<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
        // return auth()->check() && auth()->user()->hasRole('marketing');
    }

    public function rules(): array
    {
        return [
            // Pengajuan
            'tanggal_pengajuan' => 'required|date',
            'nominal_pengajuan' => 'required|numeric|min:0',
            'tenor' => 'required|integer|min:1',
            'tujuan_pinjaman' => 'required|string',
            'catatan' => 'nullable|string',

            // Nasabah
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:30|unique:nasabahs,nik',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'status_perkawinan' => 'required',
            'jumlah_tanggungan' => 'required|integer',
            'status_rumah' => 'required',
            'lama_menetap_tahun' => 'nullable|integer',
            'lama_menetap_bulan' => 'nullable|integer',

            // Dokumen
            'dokumen.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
