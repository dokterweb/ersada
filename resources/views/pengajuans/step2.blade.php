@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('pengajuan.storeStep2',$pengajuan->id) }}" method="POST" class="card"  enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item active">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">ANALISA</li>
                            <li class="step-item">JAMINAN</li>
                            <li class="step-item">ANALISA KAPITAL</li>
                            <li class="step-item">REVIEW FINAL</li>
                        </ul>
        
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA NASABAH DAN PEKERJAAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nama Sesuai KTP</label>
                             <input type="text" name="nama" class="form-control" value="{{ old('nama', $nasabah?->nama) }}">
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ old('nik', $nasabah?->nik) }}">
                            @error('nik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $nasabah?->tempat_lahir) }}">
                            @error('tempat_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $nasabah?->tgl_lahir) }}">
                            @error('tgl_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="number" name="no_hp" class="form-control" value="{{ old('no_hp', $nasabah?->no_hp) }}">
                            @error('no_hp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $nasabah?->alamat) }}">
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Perkawinan</label>
                             <select name="status_perkawinan" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="menikah"
                                    {{ old('status_perkawinan', $nasabah?->status_perkawinan) == 'menikah' ? 'selected' : '' }}>
                                    Menikah
                                </option>
                                <option value="belum_menikah"
                                    {{ old('status_perkawinan', $nasabah?->status_perkawinan) == 'belum_menikah' ? 'selected' : '' }}>
                                    Belum Menikah
                                </option>
                            </select>
                            @error('status_perkawinan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jlh Tanggungan</label>
                            <input type="number" name="jumlah_tanggungan" class="form-control" value="{{ old('jumlah_tanggungan', $nasabah?->jumlah_tanggungan) }}">
                            @error('jumlah_tanggungan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Rumah</label>
                            <select name="status_rumah" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach([
                                    'milik_sendiri' => 'Milik Sendiri',
                                    'milik_keluarga' => 'Milik Keluarga',
                                    'dinas' => 'Dinas',
                                    'sewa' => 'Sewa',
                                    'kost' => 'Kost',
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_rumah', $nasabah?->status_rumah) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_rumah')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Menetap (Tahun)</label>
                            <input type="number" name="lama_menetap_tahun" class="form-control" value="{{ old('lama_menetap_tahun', $nasabah?->lama_menetap_tahun) }}">
                            @error('lama_menetap_tahun')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Menetap (bulan)</label>
                            <input type="number" name="lama_menetap_bulan" class="form-control" value="{{ old('lama_menetap_bulan', $nasabah?->lama_menetap_bulan) }}">
                            @error('lama_menetap_bulan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto Nasabah</label>
                            <input type="file" name="foto_nasabah" class="form-control" accept="image/jpeg,image/png,image/jpg">

                            @error('foto_nasabah')
                                <div class="text-danger small">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if($nasabah?->foto_nasabah)
                                <div class="mt-2">
                                     <div class="mb-1">
                                        <small class="text-muted">
                                            Foto saat ini:
                                        </small>
                                    </div>
                                    <img src="{{ asset('storage/' . $nasabah->foto_nasabah) }}" alt="Foto Nasabah"
                                        class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">KTP Nasabah</label>
                            <input type="file" name="ktp_nasabah" class="form-control" accept="image/jpeg,image/png,image/jpg">

                            @error('ktp_nasabah')
                                <div class="text-danger small">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if($nasabah?->ktp_nasabah)
                                <div class="mt-2">
                                     <div class="mb-1">
                                        <small class="text-muted">
                                            Foto saat ini:
                                        </small>
                                    </div>
                                    <img src="{{ asset('storage/' . $nasabah->ktp_nasabah) }}" alt="Foto Nasabah"
                                        class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Akte Lahir Nasabah</label>
                            <input type="file" name="akte_lahir_nasabah" class="form-control" accept="image/jpeg,image/png,image/jpg">

                            @error('akte_lahir_nasabah')
                                <div class="text-danger small">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if($nasabah?->akte_lahir_nasabah)
                                <div class="mt-2">
                                     <div class="mb-1">
                                        <small class="text-muted">
                                            Foto saat ini:
                                        </small>
                                    </div>
                                    <img src="{{ asset('storage/' . $nasabah->akte_lahir_nasabah) }}" alt="Foto Nasabah"
                                        class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <h1>Data Pekerjaan</h1>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jenis Pekerjaan</label>
                            <select name="jenis_pekerjaan" class="form-select">
                                <option value="wiraswasta"
                                    {{ old('jenis_pekerjaan', $pekerjaan?->jenis_pekerjaan) == 'wiraswasta' ? 'selected' : '' }}>
                                    Wiraswasta
                                </option>
                                <option value="karyawan"
                                    {{ old('jenis_pekerjaan', $pekerjaan?->jenis_pekerjaan) == 'karyawan' ? 'selected' : '' }}>
                                    Karyawan
                                </option>
                            </select>
                            @error('jenis_pekerjaan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Penghasilan</label>
                            {{-- <input type="text" name="penghasilan" class="form-control" value="{{ old('penghasilan',$pekerjaan?->penghasilan) }}"> --}}
                            <input type="text" name="penghasilan" id="penghasilan" class="form-control rupiah-input"
                            value="{{ old('penghasilan', format_rupiah($pekerjaan?->penghasilan)) }}" inputmode="numeric" autocomplete="off">
                            @error('penghasilan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nama Usaha</label>
                            <input type="text" name="nama_usaha" class="form-control" value="{{ old('nama_usaha',$pekerjaan?->nama_usaha) }}">
                            @error('nama_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jenis Usaha</label>
                            <input type="text" name="jenis_usaha" class="form-control" value="{{ old('jenis_usaha',$pekerjaan?->jenis_usaha) }}">
                            @error('jenis_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Usaha</label>
                            <input type="text" name="lama_usaha" class="form-control" value="{{ old('lama_usaha',$pekerjaan?->lama_usaha) }}">
                            @error('lama_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jlh Pegawai</label>
                            <input type="text" name="jumlah_pegawai" class="form-control" value="{{ old('jumlah_pegawai',$pekerjaan?->jumlah_pegawai) }}">
                            @error('jumlah_pegawai')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Alamat Usaha</label>
                            <input type="text" name="alamat_usaha" class="form-control" value="{{ old('alamat_usaha',$pekerjaan?->alamat_usaha) }}">
                            @error('alamat_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telephone Usaha</label>
                            <input type="text" name="telpon_usaha" class="form-control" value="{{ old('telpon_usaha',$pekerjaan?->telpon_usaha) }}">
                            @error('telpon_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Bangunan Usaha</label>
                             <select name="bangunan_usaha" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach([
                                    'permanen' => 'Permanen',
                                    'kpr' => 'KPR',
                                    'kontrak' => 'Kontrak',
                                    'biskon' => 'Biskon',
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('bangunan_usaha', $pekerjaan?->bangunan_usaha) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bangunan_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Tempat Usaha</label>
                           <select name="status_tempat_usaha" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach([
                                    'hak_milik' => 'Hak Milik',
                                    'semi_permanen' => 'Semi Permanen',
                                    'tenda' => 'Tenda',
                                    'gerobak' => 'Gerobak',
                                    'meja' => 'Meja',
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_tempat_usaha', $pekerjaan?->status_tempat_usaha) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_tempat_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Aktivitas Usaha</label>
                            <select name="aktivitas_usaha" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="ramai"
                                    {{ old('aktivitas_usaha', $pekerjaan?->aktivitas_usaha) == 'ramai' ? 'selected' : '' }}>
                                    Ramai
                                </option>
                                <option value="sedang"
                                    {{ old('aktivitas_usaha', $pekerjaan?->aktivitas_usaha) == 'sedang' ? 'selected' : '' }}>
                                    Sedang
                                </option>
                                <option value="sepi"
                                    {{ old('aktivitas_usaha', $pekerjaan?->aktivitas_usaha) == 'sepi' ? 'selected' : '' }}>
                                    Sepi
                                </option>
                            </select>
                            @error('aktivitas_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('pengajuan.step1',$pengajuan->id) }}"class="btn btn-warning">Previous</a>
                    <button type="submit" class="btn btn-primary"> Simpan & Lanjut Step 3</button>
                </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    function formatRupiah(value) {
        let angka = value.toString().replace(/[^0-9]/g, '');

        if (angka === '') {
            return '';
        }

        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Format saat user mengetik
    $('.rupiah-input').on('input', function () {
        $(this).val(formatRupiah($(this).val()));
    });

    // Format nilai awal
    $('.rupiah-input').each(function () {
        if ($(this).val()) {
            $(this).val(formatRupiah($(this).val()));
        }
    });

    // Bersihkan format sebelum submit
    $('form').on('submit', function () {

        $('.rupiah-input').each(function () {

            let value = $(this).val()
                .toString()
                .replace(/[^0-9]/g, '');

            $(this).val(value);
        });

    });

});
</script>
@endsection