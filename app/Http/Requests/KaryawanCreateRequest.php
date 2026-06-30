<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KaryawanCreateRequest extends FormRequest
{
  
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('superadmin');
    }

  
    public function rules(): array
    {
        return [

            // users
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png',

            // karyawan
            'cabang_id'     => 'required|exists:cabangs,id',
            'nik'           => 'required|string|max:255|unique:karyawans,nik',
            'tempat_lahir'  => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'alamat'        => 'required|string',
            'no_hp'         => 'required|string|max:20',
            'tgl_masuk'     => 'required|date',
            'kelamin'       => 'required|in:laki-laki,perempuan',
            'status'        => 'required|in:0,1',

            // role spatie
            'role'          => 'required|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required'     => 'Role wajib dipilih.',
            'nik.unique'        => 'NIK sudah digunakan.',
            'email.unique'      => 'Email sudah digunakan.',
            'cabang_id.exists'  => 'Cabang tidak ditemukan.',
        ];
    }
}
