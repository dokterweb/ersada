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
            |--------------------------------------------------------------------------
            | CHECKBOX FLAGS
            |--------------------------------------------------------------------------
            */

            'has_pasangan' => ['nullable', 'boolean'],
            'has_pekerjaan_pasangan' => ['nullable', 'boolean'],
            'has_pekerjaan_penjamin' => ['nullable', 'boolean'],

            /*
            |--------------------------------------------------------------------------
            | PASANGAN
            |--------------------------------------------------------------------------
            */

            'pasangan.nama' => [
                'required_if:has_pasangan,1',
                'nullable',
                'string',
                'max:255',
            ],

            'pasangan.tempat_lahir' => [
                'required_if:has_pasangan,1',
                'nullable',
                'string',
                'max:255',
            ],

            'pasangan.tgl_lahir' => [
                'required_if:has_pasangan,1',
                'nullable',
                'date',
            ],

            'pasangan.foto_pasangan' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            /*
            |--------------------------------------------------------------------------
            | PEKERJAAN PASANGAN
            |--------------------------------------------------------------------------
            */

            'pasangan.pekerjaan.penghasilan' => [
                'required_if:has_pekerjaan_pasangan,1',
                'nullable',
                'numeric',
            ],

            'pasangan.pekerjaan.nama_usaha' => [
                'nullable',
                'string',
                'max:255',
            ],

            'pasangan.pekerjaan.jenis_usaha' => [
                'nullable',
                'string',
                'max:255',
            ],

            'pasangan.pekerjaan.lama_usaha' => [
                'nullable',
                'integer',
            ],

            'pasangan.pekerjaan.jumlah_pegawai' => [
                'nullable',
                'integer',
            ],

            'pasangan.pekerjaan.alamat_usaha' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | PENJAMIN
            | WAJIB UNTUK SETIAP PENGAJUAN
            |--------------------------------------------------------------------------
            */

            'penjamin' => ['required', 'array'],
            'penjamin.nama' => ['required', 'string', 'max:255'],
            'penjamin.tempat_lahir' => ['required', 'string', 'max:255'],
            'penjamin.tgl_lahir' => ['required', 'date'],
            'penjamin.hubungan' => ['required', 'string', 'max:100'],
            'penjamin.no_hp' => ['required', 'string', 'max:20'],
            'penjamin.alamat' => ['required', 'string'],
            /*
            |--------------------------------------------------------------------------
            | PEKERJAAN PENJAMIN
            |--------------------------------------------------------------------------
            */

            'penjamin.pekerjaan.penghasilan' => [
                'required_if:has_pekerjaan_penjamin,1',
                'nullable',
                'numeric',
            ],

            'penjamin.pekerjaan.nama_usaha' => [
                'nullable',
                'string',
                'max:255',
            ],

            'penjamin.pekerjaan.jenis_usaha' => [
                'nullable',
                'string',
                'max:255',
            ],

            'penjamin.pekerjaan.lama_usaha' => [
                'nullable',
                'integer',
            ],

            'penjamin.pekerjaan.jumlah_pegawai' => [
                'nullable',
                'integer',
            ],

            'penjamin.pekerjaan.alamat_usaha' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | SAUDARA
            |--------------------------------------------------------------------------
            */

            'saudara' => [
                'nullable',
                'array',
            ],

            'saudara.*.id' => [
                'nullable',
                'integer',
            ],

            'saudara.*.nama' => [
                'nullable',
                'string',
                'max:255',
            ],

            'saudara.*.tempat_lahir' => [
                'nullable',
                'string',
                'max:255',
            ],

            'saudara.*.tgl_lahir' => [
                'nullable',
                'date',
            ],

            'saudara.*.hubungan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'saudara.*.no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'saudara.*.alamat' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pasangan.nama.required_if' =>
                'Nama pasangan wajib diisi.',

            'pasangan.tempat_lahir.required_if' =>
                'Tempat lahir pasangan wajib diisi.',

            'pasangan.tgl_lahir.required_if' =>
                'Tanggal lahir pasangan wajib diisi.',

            'penjamin.required' =>
                'Data penjamin wajib diisi.',

            'penjamin.array' =>
                'Format data penjamin tidak valid.',

            'penjamin.nama.required' =>
                'Nama penjamin wajib diisi.',

            'penjamin.tempat_lahir.required' =>
                'Tempat lahir penjamin wajib diisi.',

            'penjamin.tgl_lahir.required' =>
                'Tanggal lahir penjamin wajib diisi.',

            'penjamin.tgl_lahir.date' =>
                'Tanggal lahir penjamin tidak valid.',

            'penjamin.hubungan.required' =>
                'Hubungan penjamin wajib diisi.',

            'penjamin.no_hp.required' =>
                'Nomor HP penjamin wajib diisi.',

            'penjamin.alamat.required' =>
                'Alamat penjamin wajib diisi.',

            'pasangan.pekerjaan.penghasilan.required_if' =>
                'Penghasilan pasangan wajib diisi.',

            'penjamin.pekerjaan.penghasilan.required_if' =>
                'Penghasilan penjamin wajib diisi.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $pasangan = $this->input('pasangan', []);
        $penjamin = $this->input('penjamin', []);

        if (isset($pasangan['pekerjaan']['penghasilan'])) {
            $pasangan['pekerjaan']['penghasilan'] =
                parse_rupiah(
                    $pasangan['pekerjaan']['penghasilan']
                );
        }

        if (isset($penjamin['pekerjaan']['penghasilan'])) {
            $penjamin['pekerjaan']['penghasilan'] =
                parse_rupiah(
                    $penjamin['pekerjaan']['penghasilan']
                );
        }

        $this->merge([
            'pasangan' => $pasangan,
            'penjamin' => $penjamin,
        ]);
    }
}