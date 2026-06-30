<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanStep3Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [

            /*
            ============================================
            CHECKBOX FLAGS
            ============================================
            */

            'has_pasangan' => 'nullable|boolean',
            'has_pekerjaan_pasangan' => 'nullable|boolean',

            'has_penjamin' => 'nullable|boolean',
            'has_pekerjaan_penjamin' => 'nullable|boolean',


            /*
            ============================================
            PASANGAN
            wajib jika has_pasangan = 1
            ============================================
            */

            'pasangan.nama' => 'required_if:has_pasangan,1|nullable|string|max:255',

            'pasangan.tempat_lahir' => 'required_if:has_pasangan,1|nullable|string|max:255',

            'pasangan.tgl_lahir' => 'required_if:has_pasangan,1|nullable|date',


            /*
            ============================================
            PEKERJAAN PASANGAN
            wajib jika checkbox pekerjaan pasangan dicentang
            ============================================
            */

            'pasangan.pekerjaan.penghasilan' =>
                'required_if:has_pekerjaan_pasangan,1|nullable|numeric',

            'pasangan.pekerjaan.nama_usaha' =>
                'nullable|string|max:255',

            'pasangan.pekerjaan.jenis_usaha' =>
                'nullable|string|max:255',

            'pasangan.pekerjaan.lama_usaha' =>
                'nullable|integer',

            'pasangan.pekerjaan.jumlah_pegawai' =>
                'nullable|integer',

            'pasangan.pekerjaan.alamat_usaha' =>
                'nullable|string',



            /*
            ============================================
            PENJAMIN
            wajib jika has_penjamin = 1
            ============================================
            */

            'penjamin.nama' =>
                'required_if:has_penjamin,1|nullable|string|max:255',

            'penjamin.tempat_lahir' =>
                'required_if:has_penjamin,1|nullable|string|max:255',

            'penjamin.tgl_lahir' =>
                'required_if:has_penjamin,1|nullable|date',

            'penjamin.hubungan' =>
                'required_if:has_penjamin,1|nullable|string|max:255',

            'penjamin.no_hp' =>
                'required_if:has_penjamin,1|nullable|string|max:20',

            'penjamin.alamat' =>
                'required_if:has_penjamin,1|nullable|string',



            /*
            ============================================
            PEKERJAAN PENJAMIN
            ============================================
            */

            'penjamin.pekerjaan.penghasilan' =>
                'required_if:has_pekerjaan_penjamin,1|nullable|numeric',

            'penjamin.pekerjaan.nama_usaha' =>
                'nullable|string|max:255',

            'penjamin.pekerjaan.jenis_usaha' =>
                'nullable|string|max:255',

            'penjamin.pekerjaan.lama_usaha' =>
                'nullable|integer',

            'penjamin.pekerjaan.jumlah_pegawai' =>
                'nullable|integer',

            'penjamin.pekerjaan.alamat_usaha' =>
                'nullable|string',



            /*
            ============================================
            SAUDARA
            optional, dynamic
            ============================================
            */

            'saudara.*.nama' =>
                'nullable|string|max:255',

            'saudara.*.tempat_lahir' =>
                'nullable|string|max:255',

            'saudara.*.tgl_lahir' =>
                'nullable|date',

            'saudara.*.hubungan' =>
                'nullable|string|max:255',

            'saudara.*.no_hp' =>
                'nullable|string|max:20',

            'saudara.*.alamat' =>
                'nullable|string',
        ];
    }


    /*
    custom messages
    */

    public function messages(): array
    {
        return [

            'pasangan.nama.required_if' =>
                'Nama pasangan wajib diisi',

            'pasangan.tempat_lahir.required_if' =>
                'Tempat lahir pasangan wajib diisi',

            'pasangan.tgl_lahir.required_if' =>
                'Tanggal lahir pasangan wajib diisi',


            'penjamin.nama.required_if' =>
                'Nama penjamin wajib diisi',

            'penjamin.no_hp.required_if' =>
                'No HP penjamin wajib diisi',

            'penjamin.hubungan.required_if' =>
                'Hubungan penjamin wajib diisi',


            'pasangan.pekerjaan.penghasilan.required_if' =>
                'Penghasilan pasangan wajib diisi',

            'penjamin.pekerjaan.penghasilan.required_if' =>
                'Penghasilan penjamin wajib diisi',
        ];
    }
}