<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();
        return view('cabangs.index',compact('cabangs'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kode'          => 'required|string|max:255',
            'nama_cabang'   => 'required|string|max:255',
            'alamat'        => 'required|string|max:255',
        ]);
        try {
            Cabang::create([
                'kode'          => $request->kode,
                'nama_cabang'   => $request->nama_cabang,
                'alamat'        => $request->alamat,
            ]);
            // Notifikasi sukses
            return redirect()->route('cabangs')
                             ->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Notifikasi error jika terjadi kesalahan
            return redirect()->route('cabangs')
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Cabang $cabang)
    {
        $cabangview = Cabang::all();
        // dd($cabang);
        return view('cabangs.edit',compact('cabang','cabangview'));
    }

    public function update(Request $request, Cabang $cabang)
    {
        $request->validate([
            'kode'          => 'required|string|max:255',
            'nama_cabang'   => 'required|string|max:255',
            'alamat'        => 'required|string|max:255',
        ]);

        try {
            $cabang->update([
                'kode'          => $request->kode,
                'nama_cabang'   => $request->nama_cabang,
                'alamat'        => $request->alamat,
            ]);

            return redirect()->route('cabangs')->with('success', 'Data kelasnya berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
