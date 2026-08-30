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
                                                @if($item->status!='dibayar')
                                                   {{--  <a href="{{ route('angsuran.create',$item) }}" class="btn btn-success btn-sm">
                                                        Bayar
                                                    </a> --}}
                                                    <button id="btn-{{ $item->id }}"  class="btn btn-success btn-sm btn-bayar"
                                                        data-id="{{ $item->id }}">Bayar
                                                    </button>
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
                        <label class="form-label">Denda</label>
                        <input type="text" id="m_denda" class="form-control" readonly>
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
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Debitur</label>
                        <input id="h_nama" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>No Pembiayaan</label>
                        <input id="h_nomor" class="form-control" readonly>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Angsuran Ke</label>
                        <input id="h_ke" class="form-control" readonly>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Status</label>
                        <input id="h_status" class="form-control" readonly>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No Bukti</th>
                                <th class="text-end">Bayar</th>
                                <th class="text-end">Denda</th>
                                <th class="text-end">Total</th>
                                <th>Metode</th>
                                <th>Kasir</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody">
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <label>Total Tagihan</label>
                        <input id="h_tagihan" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Total Terbayar</label>
                        <input id="h_terbayar" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Sisa Tagihan</label>
                        <input id="h_sisa" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
                <a id="btnCetakHistory" target="_blank" class="btn btn-danger">
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
    | BUKA MODAL PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-bayar',
        function () {

            let id =
                $(this).data('id');


            $.get(
                "/angsuran/jadwal/"
                + id
                + "/json",

                function (r) {

                    /*
                    |--------------------------------------------------------------------------
                    | ID
                    |--------------------------------------------------------------------------
                    */

                    $('#angsuran_id')
                        .val(r.id);


                    /*
                    |--------------------------------------------------------------------------
                    | DATA DEBITUR
                    |--------------------------------------------------------------------------
                    */

                    $('#m_nama')
                        .val(r.nama);

                    $('#m_nomor')
                        .val(
                            r.nomor_pembiayaan
                        );

                    $('#m_ke')
                        .val(
                            r.angsuran_ke
                        );

                    $('#m_jatuh_tempo')
                        .val(
                            r.tanggal_jatuh_tempo
                        );

                    $('#m_status')
                        .val(
                            r.status
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | DATA ANGSURAN
                    |--------------------------------------------------------------------------
                    */

                    $('#m_pokok')
                        .val(r.pokok);

                    $('#m_bunga')
                        .val(r.bunga);

                    $('#m_total')
                        .val(r.total);


                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL TERBAYAR
                    |--------------------------------------------------------------------------
                    */

                    $('#m_total_terbayar')
                        .val(
                            r.total_terbayar_format
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SISA TAGIHAN
                    |--------------------------------------------------------------------------
                    */

                    $('#m_sisa_tagihan')
                        .val(
                            r.sisa_tagihan_format
                        )
                        .attr(
                            'data-value',
                            r.sisa_tagihan
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | TANGGAL BAYAR
                    |--------------------------------------------------------------------------
                    */

                    $('#tanggal_bayar')
                        .val(
                            "{{ now()->format('Y-m-d') }}"
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | DENDA
                    |--------------------------------------------------------------------------
                    */

                    $('#m_denda')
                        .val(
                            r.denda_format
                        )
                        .attr(
                            'data-value',
                            r.denda
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | HARI TERLAMBAT
                    |--------------------------------------------------------------------------
                    */

                    $('#m_hari_terlambat')
                        .val(
                            r.hari_terlambat
                            + ' hari'
                        )
                        .attr(
                            'data-value',
                            r.hari_terlambat
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | ADMIN
                    |--------------------------------------------------------------------------
                    */

                    $('#m_admin_keterlambatan')
                        .val(
                            r.admin_keterlambatan_format
                        )
                        .attr(
                            'data-value',
                            r.admin_keterlambatan
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL TAMBAHAN
                    |--------------------------------------------------------------------------
                    */

                    $('#m_total_tambahan')
                        .val(
                            r.total_tambahan_format
                        )
                        .attr(
                            'data-value',
                            r.total_tambahan
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | NOMINAL BAYAR DEFAULT
                    |--------------------------------------------------------------------------
                    |
                    | Sisa tagihan + total tambahan
                    |
                    | Contoh:
                    |
                    | Sisa      720.000
                    | Denda     432.000
                    | Admin       0
                    |
                    | Default = 1.152.000
                    |
                    | Tetapi user tetap boleh mengubahnya
                    | menjadi lebih kecil.
                    |
                    */

                    let defaultBayar =
                        Number(
                            r.sisa_tagihan
                        )
                        +
                        Number(
                            r.total_tambahan
                        );


                    $('#jumlah_bayar')
                        .val(
                            defaultBayar
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN DATA ASLI
                    |--------------------------------------------------------------------------
                    */

                    $('#jumlah_bayar')
                        .attr(
                            'data-sisa-tagihan',
                            r.sisa_tagihan
                        )
                        .attr(
                            'data-total-tambahan',
                            r.total_tambahan
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | METODE
                    |--------------------------------------------------------------------------
                    */

                    $('#metode')
                        .val(
                            r.metode || 'tunai'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | KETERANGAN
                    |--------------------------------------------------------------------------
                    */

                    $('#keterangan')
                        .val('');


                    /*
                    |--------------------------------------------------------------------------
                    | MODAL
                    |--------------------------------------------------------------------------
                    */

                    const modal =
                        new bootstrap.Modal(
                            document.getElementById(
                                'modalBayar'
                            )
                        );


                    modal.show();

                }

            ).fail(function (xhr) {

                console.log(xhr);

                alert(
                    xhr.responseJSON?.message
                    ||
                    'Gagal mengambil data angsuran.'
                );

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TANGGAL BAYAR BERUBAH
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '#tanggal_bayar',
        function () {

            hitungPreviewDenda();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PREVIEW DENDA
    |--------------------------------------------------------------------------
    */

    function hitungPreviewDenda()
    {

        let angsuranId =
            $('#angsuran_id').val();


        let tanggalBayar =
            $('#tanggal_bayar').val();


        if (
            !angsuranId
            ||
            !tanggalBayar
        ) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        let sisaTagihan =
            Number(
                $('#m_sisa_tagihan')
                    .attr(
                        'data-value'
                    )
            )
            || 0;


        /*
        |--------------------------------------------------------------------------
        | LOADING
        |--------------------------------------------------------------------------
        */

        $('#m_denda')
            .val(
                'Menghitung...'
            );

        $('#m_admin_keterlambatan')
            .val(
                'Menghitung...'
            );

        $('#m_total_tambahan')
            .val(
                'Menghitung...'
            );


        /*
        |--------------------------------------------------------------------------
        | URL PREVIEW
        |--------------------------------------------------------------------------
        */

        let url =
            "{{ route(
                'angsuran.previewDenda',
                ':angsuran'
            ) }}";


        url =
            url.replace(
                ':angsuran',
                angsuranId
            );


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        $.get(

            url,

            {
                tanggal_bayar:
                    tanggalBayar
            },

            function (r) {


                /*
                |--------------------------------------------------------------------------
                | DENDA
                |--------------------------------------------------------------------------
                */

                $('#m_denda')
                    .val(
                        r.denda_format
                    )
                    .attr(
                        'data-value',
                        r.denda
                    );


                /*
                |--------------------------------------------------------------------------
                | HARI
                |--------------------------------------------------------------------------
                */

                $('#m_hari_terlambat')
                    .val(
                        r.hari_terlambat
                        + ' hari'
                    )
                    .attr(
                        'data-value',
                        r.hari_terlambat
                    );


                /*
                |--------------------------------------------------------------------------
                | ADMIN
                |--------------------------------------------------------------------------
                */

                $('#m_admin_keterlambatan')
                    .val(
                        r.admin_keterlambatan_format
                    )
                    .attr(
                        'data-value',
                        r.admin_keterlambatan
                    );


                /*
                |--------------------------------------------------------------------------
                | TOTAL TAMBAHAN
                |--------------------------------------------------------------------------
                */

                $('#m_total_tambahan')
                    .val(
                        r.total_tambahan_format
                    )
                    .attr(
                        'data-value',
                        r.total_tambahan
                    );


                /*
                |--------------------------------------------------------------------------
                | DEFAULT NOMINAL BAYAR
                |--------------------------------------------------------------------------
                */

                let totalTambahan =
                    Number(
                        r.total_tambahan
                    )
                    || 0;


                let defaultBayar =
                    sisaTagihan
                    +
                    totalTambahan;


                $('#jumlah_bayar')
                    .val(
                        defaultBayar
                    )
                    .attr(
                        'data-total-tambahan',
                        totalTambahan
                    );

            }

        ).fail(function (xhr) {

            console.log(xhr);

            $('#m_denda')
                .val('0');

            $('#m_admin_keterlambatan')
                .val('0');

            $('#m_total_tambahan')
                .val('0');

            $('#m_hari_terlambat')
                .val('0 hari');

            $('#jumlah_bayar')
                .val(
                    sisaTagihan
                );

            alert(
                xhr.responseJSON?.message
                ||
                'Gagal menghitung denda.'
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '#btnSimpanBayar',
        function () {

            let button =
                $(this);


            let angsuranId =
                $('#angsuran_id')
                    .val();


            let tanggalBayar =
                $('#tanggal_bayar')
                    .val();


            let jumlahBayar =
                Number(
                    $('#jumlah_bayar')
                        .val()
                )
                || 0;


            let metode =
                $('#metode')
                    .val();


            let keterangan =
                $('#keterangan')
                    .val();


            /*
            |--------------------------------------------------------------------------
            | VALIDASI
            |--------------------------------------------------------------------------
            */

            if (!angsuranId) {

                alert(
                    'Angsuran tidak ditemukan.'
                );

                return;
            }


            if (!tanggalBayar) {

                alert(
                    'Tanggal pembayaran wajib diisi.'
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
            | URL STORE
            |--------------------------------------------------------------------------
            */

            let url =
                "{{ route(
                    'angsuran.store',
                    ':angsuran'
                ) }}";


            url =
                url.replace(
                    ':angsuran',
                    angsuranId
                );


            /*
            |--------------------------------------------------------------------------
            | DISABLE BUTTON
            |--------------------------------------------------------------------------
            */

            button
                .prop(
                    'disabled',
                    true
                )
                .html(
                    '<i class="ti ti-loader-2"></i> Menyimpan...'
                );


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: url,

                type: "POST",

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


                success: function (res) {

                    if (
                        !res.success
                    ) {

                        alert(
                            res.message
                            ||
                            'Pembayaran gagal.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE BARIS
                    |--------------------------------------------------------------------------
                    */

                    let id =
                        res.row.id;


                    $('#terbayar-' + id)
                        .text(
                            'Rp ' +
                            res.row
                                .total_terbayar_format
                        );


                    $('#sisa-' + id)
                        .text(
                            'Rp ' +
                            res.row
                                .sisa_tagihan_format
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    $('#status-' + id)

                        .removeClass(
                            'bg-success bg-danger bg-secondary'
                        )

                        .addClass(
                            res.row.badge
                        )

                        .text(
                            res.row.status
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA LUNAS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        res.row.status
                        ===
                        'Dibayar'
                    ) {

                        $('#btn-' + id)
                            .remove();
                    }


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
                        bootstrap.Modal
                            .getInstance(
                                modalElement
                            );


                    if (modal) {

                        modal.hide();

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NOTIFIKASI
                    |--------------------------------------------------------------------------
                    */

                    alert(
                        res.message
                        ||
                        'Pembayaran berhasil.'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | REFRESH
                    |--------------------------------------------------------------------------
                    */

                    location.reload();

                },


                error: function (xhr) {

                    console.log(
                        xhr
                    );


                    let response =
                        xhr.responseJSON;


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDATION ERROR
                    |--------------------------------------------------------------------------
                    */

                    if (
                        response
                        &&
                        response.errors
                    ) {

                        let pesan = '';


                        $.each(
                            response.errors,
                            function (
                                field,
                                messages
                            ) {

                                pesan +=
                                    messages.join(
                                        '\n'
                                    )
                                    +
                                    '\n';

                            }
                        );


                        alert(
                            pesan
                        );

                    }

                    else {

                        alert(
                            response?.message
                            ||
                            'Pembayaran gagal.'
                        );
                    }

                },


                complete: function () {

                    button
                        .prop(
                            'disabled',
                            false
                        )
                        .html(
                            '<i class="ti ti-device-floppy"></i> Simpan Pembayaran'
                        );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-history',
        function () {

            let id =
                $(this).data('id');


            $.ajax({

                url:
                    "/angsuran/jadwal/"
                    +
                    id
                    +
                    "/history",

                type:
                    "GET",


                success: function (r) {

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


                    let rows = [];


                    if (
                        !r.history
                        ||
                        r.history.length === 0
                    ) {

                        rows.push(`

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center"
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

                                rows.push(`

                                    <tr>

                                        <td>
                                            ${item.tanggal}
                                        </td>

                                        <td>
                                            ${item.nomor}
                                        </td>

                                        <td class="text-end">
                                            Rp ${item.jumlah_bayar_format}
                                        </td>

                                        <td class="text-end">
                                            Rp ${item.denda_format}
                                        </td>

                                        <td class="text-end">
                                            Rp ${item.total_format}
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


                    $('#historyBody')
                        .html(
                            rows.join('')
                        );


                    $('#btnCetakHistory')
                        .attr(
                            'href',
                            '/angsuran/angsuran/'
                            +
                            id
                            +
                            '/history/cetak'
                        );


                    const modal =
                        new bootstrap.Modal(
                            document.getElementById(
                                'modalHistory'
                            )
                        );


                    modal.show();

                },


                error: function (xhr) {

                    console.log(xhr);

                    alert(
                        xhr.responseJSON?.message
                        ||
                        'Gagal mengambil history pembayaran.'
                    );

                }

            });

        }
    );

});

</script>

@endsection