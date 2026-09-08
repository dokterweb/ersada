@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                    Data Angsuran
                    </h2>
                </div>
                @if($pembiayaan->status !== 'lunas')
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('pelunasan.create', $pembiayaan) }}" class="btn btn-primary">
                         <i class="ti ti-cash"></i>
                        Ajukan Pelunasan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ringkasan Pembiayaan</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="250">Nomor Pembiayaan</th>
                                            <td>{{ $pembiayaan->nomor_pembiayaan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Debitur</th>
                                            <td>{{ $pembiayaan->pengajuan->nasabah->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Marketing</th>
                                            <td>{{ optional($pembiayaan->pengajuan->marketing)->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cabang</th>
                                            <td>{{ optional($pembiayaan->pengajuan->cabang)->nama_cabang}}</td>
                                        </tr>
                                        <tr>
                                            <th>Plafond</th>
                                            <td>Rp {{ number_format($pembiayaan->plafond,0,',','.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tenor</th>
                                            <td>{{ $pembiayaan->tenor }} Bulan</td>
                                        </tr>
                                        <tr>
                                            <th>Tgl Pencairan</th>
                                            <td>{{ optional($pembiayaan->tanggal_pencairan)->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><span class="badge bg-success">{{ ucfirst($pembiayaan->status) }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="250">Total Angsuran</th>
                                            <td>{{ $totalAngsuran }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sudah Dibayar</th>
                                            <td>{{ $sudahDibayar }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sisa Angsuran</th>
                                            <td>{{ $sisaAngsuran }}</td>
                                        </tr>
                                        <tr>
                                            <th>Outstanding</th>
                                            <td>Rp {{ number_format($outstanding,0,',','.') }}</td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Jadwal Angsuran</h3>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ke</th>
                                                {{-- <th>ID</th> --}}
                                                <th>Jatuh Tempo</th>
                                                <th>Pokok</th>
                                                <th>Bunga</th>
                                                <th>Total</th>
                                                <th>Total Terbayar</th>
                                                <th>Sisa Tagihan</th>
                                                <th>Status</th>
                                                <th>Sumber Pembayaran</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pembiayaan->angsurans as $item)
                                        <tr id="row-{{ $item->id }}">
                                            <td>{{ $item->angsuran_ke }}</td>
                                            {{-- <td>{{ $item->id }}</td> --}}
                                            <td>{{ $item->tanggal_jatuh_tempo->format('d-m-Y') }}</td>
                                            <td>Rp {{ number_format($item->pokok_angsuran,0,',','.') }}</td>
                                            <td>Rp {{ number_format($item->bunga_angsuran,0,',','.') }}</td>
                                            <td><strong>Rp {{ number_format($item->total_angsuran,0,',','.') }}</strong></td>
                                            <td>
                                                <span id="terbayar-{{ $item->id }}">
                                                Rp {{ number_format($item->total_terbayar,0,',','.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span id="sisa-{{ $item->id }}">
                                                Rp {{ number_format($item->sisa_tagihan,0,',','.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span id="status-{{ $item->id }}" class="badge
                                                @if($item->status=='dibayar')
                                                bg-success
                                                @elseif($item->status=='jatuh_tempo')
                                                bg-danger
                                                @else
                                                bg-secondary
                                                @endif
                                                ">
                                                {{ ucfirst(str_replace('_',' ',$item->status)) }}
                                                </span>
                                            </td>
                                            <td>
    @if($item->pelunasan)

        <span class="badge bg-info">
            Pelunasan
        </span>

        <div class="small text-muted mt-1">
            {{ $item->pelunasan->nomor_pelunasan }}
        </div>

    @elseif($item->status === 'dibayar')

        <span class="badge bg-secondary">
            Angsuran Biasa
        </span>

    @else

        <span class="text-muted">
            -
        </span>

    @endif
</td>
                                            <td>
                                                @if($item->status!='dibayar')
                                                   {{--  <a href="{{ route('angsuran.create',$item) }}" class="btn btn-success btn-sm">
                                                        Bayar
                                                    </a> --}}
                                                    <button id="btn-{{ $item->id }}"  class="btn btn-success btn-sm btn-bayar"
                                                        data-id="{{ $item->id }}">Bayar
                                                    </button>
                                                    @if(
                                                        $item->status != 'dibayar' &&
                                                        ($item->preview_denda['denda_tersisa'] ?? 0) > 0
                                                    )
                                                        <a href="{{ route('diskon-denda.create', $item) }}" class="btn btn-warning btn-sm">
                                                            <i class="ti ti-discount"></i>
                                                            Diskon Denda
                                                        </a>
                                                    @endif
                                                @endif
                                                   {{--  <a href="{{ route('angsuran.history',$item) }}" class="btn btn-info btn-sm">
                                                        History
                                                    </a> --}}
                                                    <button class="btn btn-info btn-sm btn-history" data-id="{{ $item->id }}">
                                                            History
                                                    </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pembayaran Angsuran</h5>
                <button type="button"class="btn-close"data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- id angsuran --}}
                <input type="hidden" id="angsuran_id">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Debitur</label>
                        <input type="text" id="m_nama" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Pembiayaan</label>
                        <input type="text" id="m_nomor" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Angsuran Ke</label>
                        <input type="text" id="m_ke" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jatuh Tempo</label>
                        <input type="text" id="m_jatuh_tempo" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <input type="text" id="m_status" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pokok</label>
                        <input type="text" id="m_pokok" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bunga</label>
                        <input type="text" id="m_bunga" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Angsuran</label>
                        <input type="text" id="m_total" class="form-control fw-bold text-primary" readonly>
                    </div>
                    <hr>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Bayar</label>
                        <input type="date" id="tanggal_bayar" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Metode</label>
                        <select id="metode" class="form-select">
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
    <label class="form-label">Denda Berjalan</label>
    <input type="text" id="m_denda_berjalan" class="form-control" readonly>
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">
        Diskon Denda Disetujui
    </label>
    <input type="text" id="m_diskon_denda" class="form-control text-success" readonly>
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">Denda Setelah Diskon</label>
    <input
        type="text"
        id="m_denda"
        class="form-control fw-bold"
        readonly
    >
</div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Admin Keterlambatan
                        </label>
                        <input type="text" id="m_admin_keterlambatan" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hari Terlambat</label>
                        <input type="text" id="m_hari_terlambat" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Total Tambahan
                        </label>
                        <input type="text" id="m_total_tambahan" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Terbayar</label>
                        <input type="text" id="m_total_terbayar" class="form-control" readonly>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sisa Tagihan</label>
                        <input type="text" id="m_sisa_tagihan" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nominal Bayar</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="form-control fw-bold"  required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Keterangan</label>
                        <textarea id="keterangan" rows="3" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
                <button type="button" id="btnSimpanBayar" class="btn btn-primary"> 
                    <i class="ti ti-device-floppy"></i>
                    Simpan Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistory" tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    History Pembayaran Angsuran
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                {{-- ===================================================== --}}
                {{-- DATA ANGSURAN --}}
                {{-- ===================================================== --}}

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Debitur
                        </label>

                        <input
                            id="h_nama"
                            class="form-control"
                            readonly
                        >

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            No Pembiayaan
                        </label>

                        <input
                            id="h_nomor"
                            class="form-control"
                            readonly
                        >

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Angsuran Ke
                        </label>

                        <input
                            id="h_ke"
                            class="form-control"
                            readonly
                        >

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <input
                            id="h_status"
                            class="form-control"
                            readonly
                        >

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- HISTORY PEMBAYARAN --}}
                {{-- ===================================================== --}}

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="table-light">

                            <tr>

                                <th>Tanggal</th>

                                <th>No Bukti</th>

                                <th class="text-end">
                                    Angsuran
                                </th>

                                <th class="text-end">
                                    Denda
                                </th>

                                <th class="text-end">
                                    Diskon Denda
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th>
                                    Metode
                                </th>

                                <th>
                                    Kasir
                                </th>

                                <th width="80">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody id="historyBody">

                        </tbody>

                    </table>

                </div>


                <hr>


                {{-- ===================================================== --}}
                {{-- RINGKASAN --}}
                {{-- ===================================================== --}}

                <div class="row">

                    <div class="col-md-4">

                        <label class="form-label">
                            Total Tagihan
                        </label>

                        <input
                            id="h_tagihan"
                            class="form-control"
                            readonly
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Total Terbayar
                        </label>

                        <input
                            id="h_terbayar"
                            class="form-control"
                            readonly
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Sisa Tagihan
                        </label>

                        <input
                            id="h_sisa"
                            class="form-control"
                            readonly
                        >

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FOOTER --}}
            {{-- ========================================================= --}}

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>


                <a
                    id="btnCetakHistory"
                    target="_blank"
                    class="btn btn-danger"
                >

                    <i class="ti ti-file-type-pdf"></i>

                    Cetak History

                </a>

            </div>

        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>

$(function () {

    /*
    |--------------------------------------------------------------------------
    | GLOBAL DATA
    |--------------------------------------------------------------------------
    */

    let currentAngsuranId = null;
    let currentSisaTagihan = 0;
    let currentTotalTambahan = 0;


    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    function formatRupiah(angka)
    {
        angka = parseInt(angka) || 0;

        return new Intl.NumberFormat('id-ID').format(angka);
    }


    $('.btn-bayar').on('click', function () {

    let id = $(this).data('id');

    $.ajax({

        url: "/angsuran/jadwal/" + id + "/json",

        type: "GET",

        dataType: "json",

        beforeSend: function () {

            $('#btn-' + id)
                .prop('disabled', true)
                .text('Memuat...');

        },

        success: function (r) {

            /*
            |--------------------------------------------------------------------------
            | DATA UTAMA
            |--------------------------------------------------------------------------
            */

            $('#angsuran_id').val(r.id);

            $('#m_nama').val(r.nama);

            $('#m_nomor').val(
                r.nomor_pembiayaan
            );

            $('#m_ke').val(
                r.angsuran_ke
            );

            $('#m_jatuh_tempo').val(
                r.tanggal_jatuh_tempo
            );

            $('#m_status').val(
                r.status
            );

            $('#m_pokok').val(
                r.pokok
            );

            $('#m_bunga').val(
                r.bunga
            );

            $('#m_total').val(
                r.total
            );


            /*
            |--------------------------------------------------------------------------
            | PEMBAYARAN
            |--------------------------------------------------------------------------
            */

            $('#m_total_terbayar').val(
                'Rp ' + r.total_terbayar_format
            );

            $('#m_sisa_tagihan').val(
                'Rp ' + r.sisa_tagihan_format
            );


            /*
            |--------------------------------------------------------------------------
            | DENDA
            |--------------------------------------------------------------------------
            */

            $('#m_denda_berjalan').val(
                'Rp ' + r.denda_berjalan_format
            );

            $('#m_diskon_denda').val(
                'Rp ' + r.diskon_denda_format
            );

            $('#m_denda').val(
                'Rp ' + r.denda_format
            );

            $('#m_admin_keterlambatan').val(
                'Rp ' + r.admin_keterlambatan_format
            );

            $('#m_hari_terlambat').val(
                r.hari_terlambat_format
            );

            $('#m_total_tambahan').val(
                'Rp ' + r.total_tambahan_format
            );


            /*
            |--------------------------------------------------------------------------
            | TANGGAL & METODE
            |--------------------------------------------------------------------------
            */

            $('#tanggal_bayar').val(
                '{{ now()->format("Y-m-d") }}'
            );

            $('#metode').val(
                r.metode
            );


            /*
            |--------------------------------------------------------------------------
            | DEFAULT NOMINAL BAYAR
            |--------------------------------------------------------------------------
            */

            $('#jumlah_bayar').val(
                r.default_bayar
            );


            $('#keterangan').val('');


            /*
            |--------------------------------------------------------------------------
            | SIMPAN ID
            |--------------------------------------------------------------------------
            */

            $('#angsuran_id').val(
                r.id
            );


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN MODAL
            |--------------------------------------------------------------------------
            */

            const modal =
                new bootstrap.Modal(
                    document.getElementById(
                        'modalBayar'
                    )
                );

            modal.show();

        },

        error: function (xhr) {

            console.log(
                xhr.responseText
            );

            let message =
                'Gagal mengambil data angsuran.';

            if (
                xhr.responseJSON &&
                xhr.responseJSON.message
            ) {

                message =
                    xhr.responseJSON.message;
            }

            alert(message);
        },

        complete: function () {

            $('#btn-' + id)
                .prop('disabled', false)
                .text('Bayar');

        }

    });

});

    /*
    |--------------------------------------------------------------------------
    | PERUBAHAN TANGGAL BAYAR
    |--------------------------------------------------------------------------
    */

    $('#tanggal_bayar').on(
        'change',
        function () {

            let tanggalBayar =
                $(this).val();

            let angsuranId =
                $('#angsuran_id').val();


            if (!angsuranId || !tanggalBayar) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | LOADING
            |--------------------------------------------------------------------------
            */

            $('#m_denda').val('Menghitung...');

            $('#m_admin_keterlambatan').val(
                'Menghitung...'
            );

            $('#m_hari_terlambat').val(
                'Menghitung...'
            );

            $('#m_total_tambahan').val(
                'Menghitung...'
            );


            /*
            |--------------------------------------------------------------------------
            | PREVIEW DENDA
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url:
                    '/angsuran/jadwal/' +
                    angsuranId +
                    '/preview-denda',

                type: 'GET',

                data: {
                    tanggal_bayar:
                        tanggalBayar
                },

                success: function (r) {

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE DATA
                    |--------------------------------------------------------------------------
                    */

                    currentSisaTagihan =
                        parseInt(
                            r.sisa_tagihan
                        ) || 0;

                    currentTotalTambahan =
                        parseInt(
                            r.total_tambahan
                        ) || 0;


                    /*
                    |--------------------------------------------------------------------------
                    | DENDA
                    |--------------------------------------------------------------------------
                    */

                    $('#m_denda').val(
                        r.denda_format
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | ADMIN
                    |--------------------------------------------------------------------------
                    */

                    $('#m_admin_keterlambatan').val(
                        r.admin_keterlambatan_format
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | HARI TERLAMBAT
                    |--------------------------------------------------------------------------
                    */

                    $('#m_hari_terlambat').val(
                        r.hari_terlambat
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL TAMBAHAN
                    |--------------------------------------------------------------------------
                    */

                    $('#m_total_tambahan').val(
                        r.total_tambahan_format
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | DEFAULT NOMINAL BAYAR
                    |--------------------------------------------------------------------------
                    */

                    let totalKewajiban =
                        currentSisaTagihan +
                        currentTotalTambahan;

                    $('#jumlah_bayar').val(
                        totalKewajiban
                    );

                },

                error: function (xhr) {

                    console.error(xhr);

                    alert(
                        'Gagal menghitung denda.'
                    );

                }
            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    $('#btnSimpanBayar').on(
        'click',
        function () {

            let angsuranId =
                $('#angsuran_id').val();

            let tanggalBayar =
                $('#tanggal_bayar').val();

            let jumlahBayar =
                parseInt(
                    $('#jumlah_bayar').val()
                ) || 0;

            let metode =
                $('#metode').val();

            let keterangan =
                $('#keterangan').val();


            /*
            |--------------------------------------------------------------------------
            | VALIDASI
            |--------------------------------------------------------------------------
            */

            if (!angsuranId) {

                alert(
                    'Angsuran belum dipilih.'
                );

                return;
            }


            if (!tanggalBayar) {

                alert(
                    'Tanggal bayar wajib diisi.'
                );

                return;
            }


            if (jumlahBayar <= 0) {

                alert(
                    'Nominal pembayaran harus lebih besar dari nol.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | DISABLE BUTTON
            |--------------------------------------------------------------------------
            */

            let button =
                $(this);

            button.prop(
                'disabled',
                true
            );

            button.html(
                '<i class="ti ti-loader-2"></i> Menyimpan...'
            );


            /*
            |--------------------------------------------------------------------------
            | SIMPAN
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url:
                    '/angsuran/jadwal/' +
                    angsuranId +
                    '/bayar',

                type: 'POST',

                headers: {

                    'X-CSRF-TOKEN':
                        $('meta[name="csrf-token"]')
                        .attr('content')
                },

                data: {

                    tanggal_bayar:
                        tanggalBayar,

                    jumlah_bayar:
                        jumlahBayar,

                    metode:
                        metode,

                    keterangan:
                        keterangan
                },

                success: function (r) {

                    if (!r.success) {

                        alert(
                            r.message
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE ROW
                    |--------------------------------------------------------------------------
                    */

                    $('#terbayar-' + angsuranId)
                        .text(
                            'Rp ' +
                            r.row.total_terbayar_format
                        );


                    $('#sisa-' + angsuranId)
                        .text(
                            'Rp ' +
                            r.row.sisa_tagihan_format
                        );


                    $('#status-' + angsuranId)

                        .removeClass(
                            'bg-success bg-danger bg-secondary'
                        )

                        .addClass(
                            r.row.badge
                        )

                        .text(
                            r.row.status
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | TUTUP MODAL
                    |--------------------------------------------------------------------------
                    */

                    const modalElement =
                        document.getElementById(
                            'modalBayar'
                        );

                    const modal =
                        bootstrap.Modal.getInstance(
                            modalElement
                        );

                    if (modal) {
                        modal.hide();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    alert(
                        r.message
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SUMMARY
                    |--------------------------------------------------------------------------
                    */

                    if (
                        r.summary
                    ) {

                        $('#totalAngsuran')
                            .text(
                                r.summary.total_angsuran
                            );

                        $('#sudahDibayar')
                            .text(
                                r.summary.sudah_dibayar
                            );

                        $('#sisaAngsuran')
                            .text(
                                r.summary.sisa_angsuran
                            );

                        $('#outstanding')
                            .text(
                                'Rp ' +
                                r.summary.outstanding_format
                            );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA LUNAS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        r.row.status === 'Dibayar'
                    ) {

                        $('#btn-' + angsuranId)
                            .remove();

                    }

                },

                error: function (xhr) {

                    console.error(xhr);

                    let message =
                        'Pembayaran gagal.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;
                    }

                    alert(message);

                },

                complete: function () {

                    button.prop(
                        'disabled',
                        false
                    );

                    button.html(
                        '<i class="ti ti-device-floppy"></i> Simpan Pembayaran'
                    );

                }

            });

        }
    );

});

$(document).on(
    'click',
    '.btn-history',
    function () {

        let id = $(this).data('id');


        $.ajax({

            url:
                "/angsuran/jadwal/"
                +
                id
                +
                "/history",

            type:
                "GET",

            dataType:
                "json",


            beforeSend: function () {

                /*
                |--------------------------------------------------------------------------
                | Bersihkan history lama
                |--------------------------------------------------------------------------
                */

                $('#historyBody').html(`
                    <tr>
                        <td colspan="9" class="text-center">
                            Memuat history...
                        </td>
                    </tr>
                `);

            },


            success: function (r) {

                /*
                |--------------------------------------------------------------------------
                | DATA ANGSURAN
                |--------------------------------------------------------------------------
                */

                $('#h_nama')
                    .val(r.nama);

                $('#h_nomor')
                    .val(
                        r.nomor_pembiayaan
                    );

                $('#h_ke')
                    .val(
                        r.angsuran_ke
                    );

                $('#h_status')
                    .val(
                        r.status
                    );


                /*
                |--------------------------------------------------------------------------
                | RINGKASAN
                |--------------------------------------------------------------------------
                */

                $('#h_tagihan')
                    .val(
                        "Rp " +
                        r.total_tagihan
                    );

                $('#h_terbayar')
                    .val(
                        "Rp " +
                        r.total_terbayar
                    );

                $('#h_sisa')
                    .val(
                        "Rp " +
                        r.sisa_tagihan
                    );


                /*
                |--------------------------------------------------------------------------
                | HISTORY
                |--------------------------------------------------------------------------
                */

                let rows = [];


                if (
                    !r.history ||
                    r.history.length === 0
                ) {

                    rows.push(`

                        <tr>

                            <td
                                colspan="9"
                                class="text-center text-muted py-4"
                            >

                                Belum ada pembayaran.

                            </td>

                        </tr>

                    `);

                } else {


                    $.each(
                        r.history,
                        function (
                            i,
                            item
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | STATUS DISKON
                            |--------------------------------------------------------------------------
                            */

                            let diskonHtml =
                                'Rp ' +
                                item.diskon_denda_format;


                            if (
                                item.diskon_denda > 0 &&
                                item.status_diskon === 'disetujui'
                            ) {

                                diskonHtml += `
                                    <br>
                                    <span class="badge bg-success mt-1">
                                        Disetujui
                                    </span>
                                `;

                            }


                            rows.push(`

                                <tr>

                                    <td>
                                        ${item.tanggal}
                                    </td>


                                    <td>
                                        ${item.nomor}
                                    </td>


                                    <td class="text-end">
                                        Rp
                                        ${item.jumlah_bayar_format}
                                    </td>


                                    <td class="text-end">
                                        Rp
                                        ${item.denda_format}
                                    </td>


                                    <td class="text-end">

                                        ${diskonHtml}

                                    </td>


                                    <td class="text-end fw-bold">
                                        Rp
                                        ${item.total_format}
                                    </td>


                                    <td>
                                        ${item.metode}
                                    </td>


                                    <td>
                                        ${item.user ?? '-'}
                                    </td>


                                    <td>

                                        <a
                                            target="_blank"
                                            href="/angsuran/pembayaran/${item.id}/cetak"
                                            class="btn btn-primary btn-sm"
                                            title="Cetak Bukti"
                                        >

                                            <i
                                                class="fa-solid fa-print"
                                            ></i>

                                        </a>

                                    </td>

                                </tr>

                            `);

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | MASUKKAN ROW
                |--------------------------------------------------------------------------
                */

                $('#historyBody')
                    .html(
                        rows.join('')
                    );


                /*
                |--------------------------------------------------------------------------
                | LINK CETAK HISTORY
                |--------------------------------------------------------------------------
                */

                $('#btnCetakHistory')
                    .attr(
                        'href',
                        '/angsuran/angsuran/'
                        +
                        id
                        +
                        '/history/cetak'
                    );


                /*
                |--------------------------------------------------------------------------
                | TAMPILKAN MODAL
                |--------------------------------------------------------------------------
                */

                const modal =
                    new bootstrap.Modal(
                        document.getElementById(
                            'modalHistory'
                        )
                    );


                modal.show();

            },


            error: function (xhr) {

                console.log(
                    xhr
                );

                let message =
                    'Gagal mengambil history pembayaran.';


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }


                $('#historyBody').html(`

                    <tr>

                        <td
                            colspan="9"
                            class="text-center text-danger py-4"
                        >

                            ${message}

                        </td>

                    </tr>

                `);

            }

        });

    }
);
</script>

@endsection