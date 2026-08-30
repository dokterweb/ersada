@extends('layouts.app')

@section('content')

<div class="container">

    <div class="page-header mb-4">

        <div>

            <h2 class="page-title">
                Pengajuan Diskon Denda
            </h2>

            <div class="text-muted">
                Pengajuan diskon denda untuk angsuran
            </div>

        </div>

    </div>


    {{-- ERROR --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR MESSAGE --}}

    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- PENGAJUAN PENDING --}}

    @if($pengajuanPending)

        <div class="alert alert-warning">

            <strong>
                Pengajuan diskon sedang menunggu persetujuan pimpinan.
            </strong>

            <br>

            Diskon yang diajukan:

            <strong>
                Rp {{ number_format($pengajuanPending->diskon_denda,0,',','.') }}
            </strong>

        </div>

    @endif


    <div class="row">

        {{-- ========================================================= --}}
        {{-- DATA ANGSURAN --}}
        {{-- ========================================================= --}}

        <div class="col-md-8">

            <div class="card mb-4">

                <div class="card-header">

                    <h3 class="card-title">
                        Data Angsuran
                    </h3>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Nama Debitur
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $angsuran->pembiayaan->pengajuan->nasabah->nama }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Nomor Pembiayaan
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $angsuran->pembiayaan->nomor_pembiayaan }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Angsuran Ke
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $angsuran->angsuran_ke }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Jatuh Tempo
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $angsuran->tanggal_jatuh_tempo->format('d-m-Y') }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ ucfirst(str_replace('_',' ',$angsuran->status)) }}"
                                readonly
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PERHITUNGAN DENDA --}}
            {{-- ========================================================= --}}

            <div class="card mb-4">

                <div class="card-header bg-danger-lt">

                    <h3 class="card-title">

                        <i class="ti ti-alert-triangle"></i>

                        Perhitungan Denda

                    </h3>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Hari Terlambat
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $preview['hari_terlambat'] }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Denda
                            </label>

                            <input
                                type="text"
                                class="form-control fw-bold text-danger"
                                value="Rp {{ $preview['denda_tersisa_format'] }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Admin Keterlambatan
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Rp {{ $preview['admin_tersisa_format'] }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Total Tambahan
                            </label>

                            <input
                                type="text"
                                class="form-control fw-bold"
                                value="Rp {{ $preview['total_tambahan_format'] }}"
                                readonly
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Sisa Tagihan
                            </label>

                            <input
                                type="text"
                                class="form-control fw-bold"
                                value="Rp {{ number_format($angsuran->sisa_tagihan,0,',','.') }}"
                                readonly
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FORM PENGAJUAN --}}
            {{-- ========================================================= --}}

            <div class="card">

                <div class="card-header bg-warning-lt">

                    <h3 class="card-title">

                        <i class="ti ti-discount"></i>

                        Pengajuan Diskon

                    </h3>

                </div>


                <form
                    action="{{ route('diskon-denda.store',$angsuran) }}"
                    method="POST"
                >

                    @csrf


                    <div class="card-body">

                        <div class="alert alert-info">

                            <strong>
                                Catatan:
                            </strong>

                            Diskon denda harus mendapatkan persetujuan pimpinan
                            sebelum dapat digunakan dalam pembayaran.

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Denda Saat Ini
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Rp {{ $preview['denda_tersisa_format'] }}"
                                readonly
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Diskon Denda
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="number"
                                name="diskon_denda"
                                id="diskon_denda"
                                class="form-control"
                                min="1"
                                max="{{ $preview['denda_tersisa'] }}"
                                value="{{ old('diskon_denda') }}"
                                required
                            >

                            <div class="form-text">

                                Maksimal diskon:

                                <strong>
                                    Rp {{ $preview['denda_tersisa_format'] }}
                                </strong>

                            </div>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Denda Setelah Diskon
                            </label>

                            <input
                                type="text"
                                id="denda_setelah_diskon"
                                class="form-control fw-bold"
                                value="Rp {{ $preview['denda_tersisa_format'] }}"
                                readonly
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Alasan Pengajuan
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="alasan"
                                rows="4"
                                class="form-control"
                                required
                            >{{ old('alasan') }}</textarea>

                        </div>

                    </div>


                    <div class="card-footer d-flex justify-content-between">

                        <a
                            href="{{ route('angsuran.show',$angsuran->pembiayaan_id) }}"
                            class="btn btn-secondary"
                        >
                            Kembali
                        </a>


                        @if(!$pengajuanPending)

                            <button
                                type="submit"
                                class="btn btn-warning"
                            >

                                <i class="ti ti-send"></i>

                                Ajukan Diskon

                            </button>

                        @else

                            <button
                                type="button"
                                class="btn btn-secondary"
                                disabled
                            >

                                Menunggu Persetujuan

                            </button>

                        @endif

                    </div>

                </form>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- RINGKASAN --}}
        {{-- ========================================================= --}}

        <div class="col-md-4">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Ringkasan
                    </h3>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <div class="text-muted">
                            Sisa Tagihan
                        </div>

                        <div class="h3">
                            Rp {{ number_format($angsuran->sisa_tagihan,0,',','.') }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <div class="text-muted">
                            Total Tambahan
                        </div>

                        <div class="h3 text-danger">
                            Rp {{ $preview['total_tambahan_format'] }}
                        </div>

                    </div>


                    <hr>


                    <div>

                        <div class="text-muted">
                            Total Kewajiban
                        </div>

                        <div
                            class="h2 text-primary"
                            id="total_kewajiban"
                        >
                            Rp {{ number_format(
                                $angsuran->sisa_tagihan +
                                $preview['total_tambahan'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@section('scripts')

<script>

$(function () {

    const denda = {{ (int) $preview['denda_tersisa'] }};


    $('#diskon_denda').on('input', function () {

        let diskon = parseInt($(this).val()) || 0;


        if (diskon > denda) {

            diskon = denda;

            $(this).val(denda);

        }


        if (diskon < 0) {

            diskon = 0;

            $(this).val(0);

        }


        let sisa = denda - diskon;


        $('#denda_setelah_diskon').val(
            'Rp ' + formatRupiah(sisa)
        );

    });


    function formatRupiah(angka)
    {
        return new Intl.NumberFormat(
            'id-ID'
        ).format(angka);
    }

});

</script>

@endsection