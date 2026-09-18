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


     {{-- REPEAT ORDER / TOP UP --}}

@if($akad->pembiayaan->pengajuan->status_customer === 'repeat_order')

    <hr>

    <div class="alert alert-info">

        <div class="d-flex">

            <div>
                <i class="ti ti-refresh fs-2 me-2"></i>
            </div>

            <div>

                <h4 class="alert-title">
                    Repeat Order / Top Up
                </h4>

                <div class="text-secondary">

                    Pengajuan ini merupakan repeat order.
                    Sebagian dana pencairan pembiayaan baru
                    akan digunakan untuk melunasi pembiayaan lama.

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         PEMBIAYAAN LAMA
    ============================================================= --}}

    <div class="card border-primary mb-4">

        <div class="card-header">

            <h3 class="card-title">
                Pembiayaan Lama
            </h3>

        </div>


        <div class="card-body">

            @if(!$pembiayaanLama)

                <div class="alert alert-danger mb-0">

                    <strong>
                        Pembiayaan lama tidak ditemukan.
                    </strong>

                    <br>

                    Hubungan pembiayaan lama belum tersedia.

                </div>

            @else

                <div class="table-responsive">

                    <table class="table table-bordered mb-0">

                        <tr>

                            <th width="40%">
                                Nomor Pembiayaan
                            </th>

                            <td>
                                {{ $pembiayaanLama->nomor_pembiayaan }}
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Plafond Lama
                            </th>

                            <td>
                                Rp
                                {{ number_format(
                                    $pembiayaanLama->plafond,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                        </tr>


                        @if($perhitunganPelunasan)

                            <tr>

                                <th>
                                    Sisa Pokok
                                </th>

                                <td>
                                    Rp
                                    {{ number_format(
                                        $perhitunganPelunasan['sisa_pokok'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                            </tr>


                            <tr>

                                <th>
                                    Sisa Bunga
                                </th>

                                <td>
                                    Rp
                                    {{ number_format(
                                        $perhitunganPelunasan['sisa_bunga'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                            </tr>


                            <tr>

                                <th>
                                    Denda
                                </th>

                                <td>
                                    Rp
                                    {{ number_format(
                                        $perhitunganPelunasan['denda'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                            </tr>


                            <tr class="table-warning">

                                <th>
                                    Total Pelunasan
                                </th>

                                <th>

                                    Rp
                                    {{ number_format(
                                        $perhitunganPelunasan['total_sebelum_diskon'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </th>

                            </tr>

                        @endif

                    </table>

                </div>

            @endif

        </div>

    </div>
@if($pembiayaanLama && $perhitunganPelunasan)

    @php

        $danaPembiayaanBaru =
            (int) $akad->pembiayaan->dana_diterima;

        $totalPelunasanLama =
            (int) $perhitunganPelunasan['total_sebelum_diskon'];

        $danaTopUp =
            $danaPembiayaanBaru -
            $totalPelunasanLama;

    @endphp


    <div
        class="alert
        {{ $danaTopUp >= 0
            ? 'alert-success'
            : 'alert-danger'
        }}"
    >

        <div class="d-flex">

            <div>
                <i class="ti ti-cash fs-2 me-2"></i>
            </div>

            <div class="flex-fill">

                <h4 class="alert-title">
                    Perhitungan Dana Top Up
                </h4>


                <div class="table-responsive mt-3">

                    <table class="table table-bordered mb-0">

                        <tr>

                            <th width="50%">
                                Dana Diterima Pembiayaan Baru
                            </th>

                            <td>
                                Rp
                                {{ number_format(
                                    $danaPembiayaanBaru,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Pelunasan Pembiayaan Lama
                            </th>

                            <td>
                                Rp
                                {{ number_format(
                                    $totalPelunasanLama,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                        </tr>


                        <tr class="table-success">

                            <th>
                                Dana Top Up Diterima Nasabah
                            </th>

                            <th class="fs-2">

                                @if($danaTopUp >= 0)

                                    Rp
                                    {{ number_format(
                                        $danaTopUp,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                @else

                                    Tidak Mencukupi

                                @endif

                            </th>

                        </tr>

                    </table>

                </div>


                @if($danaTopUp < 0)

                    <div class="text-danger mt-3">

                        Dana diterima pembiayaan baru
                        tidak mencukupi untuk melunasi pembiayaan lama.

                    </div>

                @else

                    <div class="text-secondary mt-3">

                        Dana Top Up merupakan selisih antara
                        dana pembiayaan baru setelah biaya
                        dengan total pelunasan pembiayaan lama.

                    </div>

                @endif

            </div>

        </div>

    </div>

@endif

@endif                 
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

@section('scripts')

<script>

$(function () {

    /*
    |--------------------------------------------------------------------------
    | TRANSFER AREA
    |--------------------------------------------------------------------------
    */

    function toggleTransfer() {

        if ($('#metode').val() === 'transfer') {

            $('#transfer-area').slideDown();

        } else {

            $('#transfer-area').slideUp();

        }
    }

    toggleTransfer();

    $('#metode').change(toggleTransfer);


    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    function formatRupiah(angka) {

        angka = parseInt(angka) || 0;

        return 'Rp ' + angka.toLocaleString('id-ID');

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT PEMBIAYAAN LAMA
    |--------------------------------------------------------------------------
    */

    $('#pembiayaan_lama_id').on('change', function () {

        const pembiayaanId = $(this).val();

        /*
        | Reset
        */

        $('#info-pembiayaan-lama').hide();

        $('#info-topup').hide();

        if (!pembiayaanId) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        $('#info-pembiayaan-lama').show();

        $('#lama-nomor').html(
            '<span class="text-muted">Menghitung...</span>'
        );

        $('#lama-plafond').html(
            '<span class="text-muted">Menghitung...</span>'
        );

        $('#lama-sisa-pokok').html(
            '<span class="text-muted">Menghitung...</span>'
        );

        $('#lama-sisa-bunga').html(
            '<span class="text-muted">Menghitung...</span>'
        );

        $('#lama-denda').html(
            '<span class="text-muted">Menghitung...</span>'
        );

        $('#lama-pelunasan').html(
            '<span class="text-muted">Menghitung...</span>'
        );


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url:
                "{{ url('/pencairan/pembiayaan-lama') }}/"
                + pembiayaanId,

            type: 'GET',

            dataType: 'json',

            success: function (response) {

                if (!response.success) {

                    alert(
                        response.message ||
                        'Gagal menghitung pelunasan.'
                    );

                    $('#info-pembiayaan-lama').hide();

                    return;
                }


                const data = response.data;


                /*
                |--------------------------------------------------------------------------
                | INFORMASI PEMBIAYAAN LAMA
                |--------------------------------------------------------------------------
                */

                $('#lama-nomor').text(
                    data.nomor_pembiayaan
                );

                $('#lama-plafond').text(
                    formatRupiah(data.plafond)
                );

                $('#lama-sisa-pokok').text(
                    formatRupiah(data.sisa_pokok)
                );

                $('#lama-sisa-bunga').text(
                    formatRupiah(data.sisa_bunga)
                );

                $('#lama-denda').text(
                    formatRupiah(data.denda)
                );

                $('#lama-pelunasan').text(
                    formatRupiah(data.total_pelunasan)
                );


                /*
                |--------------------------------------------------------------------------
                | HITUNG DANA TOP UP
                |--------------------------------------------------------------------------
                */

                const danaBaru =
                    parseInt(
                        "{{ (int) $akad->pembiayaan->dana_diterima }}"
                    ) || 0;

                const pelunasanLama =
                    parseInt(
                        data.total_pelunasan
                    ) || 0;


                const danaTopUp =
                    danaBaru -
                    pelunasanLama;


                /*
                |--------------------------------------------------------------------------
                | TAMPILKAN DANA
                |--------------------------------------------------------------------------
                */

                $('#topup-dana-baru').text(
                    formatRupiah(danaBaru)
                );

                $('#topup-pelunasan-lama').text(
                    formatRupiah(pelunasanLama)
                );

                $('#topup-dana-diterima').text(
                    formatRupiah(
                        Math.max(0, danaTopUp)
                    )
                );

                $('#info-topup').slideDown();


                /*
                |--------------------------------------------------------------------------
                | JIKA PELUNASAN > DANA BARU
                |--------------------------------------------------------------------------
                */

                if (danaTopUp < 0) {

                    $('#info-topup')
                        .removeClass('alert-success')
                        .addClass('alert-danger');

                    $('#topup-dana-diterima').text(
                        'Tidak mencukupi'
                    );

                } else {

                    $('#info-topup')
                        .removeClass('alert-danger')
                        .addClass('alert-success');

                }

            },

            error: function (xhr) {

                let message =
                    'Gagal mengambil data pembiayaan lama.';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }

                $('#info-pembiayaan-lama').hide();

                $('#info-topup').hide();

                alert(message);

            }

        });

    });

});

</script>

@endsection