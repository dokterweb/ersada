<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanStep1Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
        && auth()->user()->hasRole('marketing|spvmarketing|admincabang');
    }

   public function rules(): array
    {
        return [
            'tanggal_pengajuan' => 'required|date',
            'nominal_pengajuan' => 'required|numeric|min:1000000',
            'tenor'             => 'required|integer|min:1',
            'kategori_nasabah'  => 'required',
            'status_customer'   => 'required|in:walk_in,baru,repeat_order',
            'tujuan_pinjaman'   => 'required|string|max:1000',
            'catatan'           => 'nullable|string|max:1000',
        ];
    }

   protected function prepareForValidation(): void
    {
        $this->merge([
            'nominal_pengajuan' => parse_rupiah(
                $this->input('nominal_pengajuan')
            ),
        ]);
    }
    public function messages(): array
    {
        return [
            'nominal_pengajuan.min' =>'Minimal pengajuan Rp.1.000.000',
        ];
    }
}
