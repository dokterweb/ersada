@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Data Karyawan
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <a href="{{route('karyawans.create')}}" class="btn btn-primary">
                Tambah Karyawan
            </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <form action="{{ route('pencairan.store',$akad) }}" method="POST"  enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pencairan Pembiayaan</h3>
                </div>
                <div class="card-body">
                    {{-- DATA DEBITUR --}}
                    <h4 class="mb-3">Data Debitur</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Akad</label>
                                <input type="text" class="form-control" value="{{ $akad->nomor_akad }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Debitur</label>
                                <input type="text" class="form-control" value="{{ $akad->pembiayaan->pengajuan->nasabah->nama }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Marketing</label>
                                <input type="text" class="form-control" value="{{ $akad->pembiayaan->pengajuan->marketing->user->name ?? '-' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cabang</label>
                                <input type="text" class="form-control" value="{{ $akad->pembiayaan->pengajuan->cabang->nama_cabang ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">Rincian Pembiayaan</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Plafond</th>
                                        <td>Rp {{ number_format($akad->pembiayaan->plafond,0,',','.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Administrasi</th>
                                        <td>Rp {{ number_format($akad->pembiayaan->biaya_administrasi,0,',','.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Materai</th>
                                        <td>Rp {{ number_format($akad->pembiayaan->materai,0,',','.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Biaya Survey</th>
                                        <td>Rp {{ number_format($akad->pembiayaan->biaya_survey,0,',','.') }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <th>Dana Diterima Nasabah</th>
                                        <th>Rp {{ number_format($akad->pembiayaan->dana_diterima,0,',','.') }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{-- FORM --}}
                    <h4 class="mb-3">Data Pencairan</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Pencairan</label>
                            <input type="date" name="tanggal_pencairan" value="{{ old('tanggal_pencairan',date('Y-m-d')) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tgl_telat_bayar" class="form-label">
                                Tanggal Jatuh Tempo
                            </label>

                            <select name="tgl_telat_bayar" id="tgl_telat_bayar" class="form-select"required>
                                @for ($i = 1; $i <= 30; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('tgl_telat_bayar', 10) == $i ? 'selected' : '' }}>
                                        Tanggal {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <div class="form-text">
                                Tanggal setiap bulan yang menjadi batas pembayaran sebelum dihitung terlambat.
                            </div>
                            @error('tgl_telat_bayar')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Metode</label>
                            <select name="metode" id="metode" class="form-select">
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
    
                    </div>
                    <div id="transfer-area" style="display:none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bank</label>
                                <input type="text" name="bank" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No Rekening</label>
                                <input type="text" name="no_rekening" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Atas Nama</label>
                                <input type="text" name="atas_nama" class="form-control">
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <h4 class="mb-3">Dokumen Pencairan</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bukti Pencairan</label>
                            <input type="file" name="bukti_pencairan" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">JPG, JPEG, PNG atau PDF.Maksimal 50 MB.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto Akad 1</label>
                            <input type="file" name="foto_akad1" class="form-control" accept=".jpg,.jpeg,.png">
                            <div class="form-text">JPG, JPEG atau PNG.Maksimal 50 MB.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto Akad 2</label>
                            <input type="file" name="foto_akad2" class="form-control" accept=".jpg,.jpeg,.png">
                            <div class="form-text">JPG, JPEG atau PNG.Maksimal 50 MB.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto Akad 3</label>
                            <input type="file" name="foto_akad3" class="form-control" accept=".jpg,.jpeg,.png">
                            <div class="form-text">JPG, JPEG atau PNG.Maksimal 50 MB.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Video Pencairan</label>
                            <input type="file" name="video" class="form-control"
                                accept="video/mp4,video/mov,video/avi,video/mkv,video/webm">
                            <div class="form-text">Format: MP4, MOV, AVI, MKV atau WEBM.Maksimal 100 MB.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('akad.show',$akad) }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button class="btn btn-primary">
                        Simpan Pencairan
                    </button>
                </div>
            </div>
        </form>       
      </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
$(function(){
    function toggleTransfer(){
        if($('#metode').val()=='transfer'){
            $('#transfer-area').slideDown();
        }else{
            $('#transfer-area').slideUp();
        }
    }
    toggleTransfer();
    $('#metode').change(toggleTransfer);
});

</script>
@endsection