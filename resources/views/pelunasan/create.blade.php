@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">

                <div class="col">
                    <h2 class="page-title">
                        Pelunasan Pembiayaan
                    </h2>
                </div>

            </div>
        </div>
    </div>


    <!-- Page Body -->
    <div class="page-body">

        <div class="container-xl">

            <div class="row row-cards">

                <div class="card">

                    <div class="card-header bg-blue-lt">

                        <h3 class="card-title">
                            Pengajuan Pelunasan
                        </h3>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('pelunasan.store', $pembiayaan) }}"
                    >

                        @csrf


                        <div class="card-body">

                            <div class="row">

                                <!-- INFORMASI PELUNASAN -->
                                <div class="col-md-7">

                                    <table class="table table-bordered">

                                        <tr>
                                            <th width="40%">
                                                No Pembiayaan
                                            </th>

                                            <td>
                                                {{ $pembiayaan->nomor_pembiayaan }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Nama Nasabah
                                            </th>

                                            <td>
                                                {{ $pembiayaan->pengajuan->nasabah->nama }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Jenis Tenor
                                            </th>

                                            <td>
                                                {{ ucfirst($perhitungan['jenis_tenor']) }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Sisa Angsuran
                                            </th>

                                            <td>
                                                {{ $perhitungan['sisa_angsuran'] }} kali
                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Tanggal Pelunasan
                                            </th>

                                            <td>

                                                <input
                                                    type="date"
                                                    name="tanggal_pelunasan"
                                                    id="tanggal_pelunasan"
                                                    class="form-control"
                                                    value="{{ old('tanggal_pelunasan', date('Y-m-d')) }}"
                                                    required
                                                >

                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Sisa Pokok
                                            </th>

                                            <td>

                                                <input
                                                    readonly
                                                    type="text"
                                                    class="form-control text-end"
                                                    value="{{ number_format($perhitungan['sisa_pokok'],0,',','.') }}"
                                                >

                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Sisa Bunga
                                            </th>

                                            <td>

                                                <input
                                                    readonly
                                                    type="text"
                                                    class="form-control text-end"
                                                    value="{{ number_format($perhitungan['sisa_bunga'],0,',','.') }}"
                                                >

                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Denda
                                            </th>

                                            <td>

                                                <input
                                                    readonly
                                                    type="text"
                                                    class="form-control text-end"
                                                    value="{{ number_format($perhitungan['denda'],0,',','.') }}"
                                                >

                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Total Sebelum Diskon
                                            </th>

                                            <td>

                                                <input
                                                    readonly
                                                    type="text"
                                                    id="total_sebelum_diskon_view"
                                                    class="form-control fw-bold text-end"
                                                    value="{{ number_format($perhitungan['total_sebelum_diskon'],0,',','.') }}"
                                                >

                                            </td>
                                        </tr>


                                        <tr>
                                            <th>
                                                Diskon
                                            </th>

                                            <td>

                                                <input
                                                    type="number"
                                                    name="diskon"
                                                    id="diskon"
                                                    class="form-control text-end"
                                                    value="{{ old('diskon', 0) }}"
                                                    min="0"
                                                    max="{{ $perhitungan['total_sebelum_diskon'] }}"
                                                >

                                                <small class="text-muted">
                                                    Kosongkan atau isi 0 jika tidak ada diskon.
                                                </small>

                                            </td>
                                        </tr>


                                        <tr id="alasan_diskon_row" style="display:none;">

                                            <th>
                                                Alasan Diskon
                                            </th>

                                            <td>

                                                <textarea
                                                    name="alasan_diskon"
                                                    id="alasan_diskon"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Masukkan alasan pengajuan diskon..."
                                                >{{ old('alasan_diskon') }}</textarea>

                                                <small class="text-muted">
                                                    Pengajuan diskon akan membutuhkan persetujuan pimpinan.
                                                </small>

                                            </td>

                                        </tr>


                                        <tr class="table-success">

                                            <th>
                                                Total Pelunasan
                                            </th>

                                            <td>

                                                <input
                                                    readonly
                                                    type="text"
                                                    id="total_view"
                                                    class="form-control fw-bold text-end fs-3"
                                                    value="{{ number_format($perhitungan['total_sebelum_diskon'],0,',','.') }}"
                                                >

                                            </td>

                                        </tr>

                                    </table>

                                </div>


                                <!-- KETERANGAN -->
                                <div class="col-md-5">

                                    <div class="mb-3">

                                        <label class="form-label">
                                            Keterangan
                                        </label>

                                        <textarea
                                            name="keterangan"
                                            class="form-control"
                                            rows="8"
                                            placeholder="Keterangan pelunasan..."
                                        >{{ old('keterangan') }}</textarea>

                                    </div>


                                    <div
                                        id="info_normal"
                                        class="alert alert-success"
                                    >

                                        <strong>
                                            Pelunasan Normal
                                        </strong>

                                        <br>

                                        Tidak ada diskon.
                                        Pengajuan akan langsung berstatus
                                        <strong>Siap Dibayar</strong>
                                        tanpa persetujuan pimpinan.

                                    </div>


                                    <div
                                        id="info_diskon"
                                        class="alert alert-warning"
                                        style="display:none;"
                                    >

                                        <strong>
                                            Pelunasan Dengan Diskon
                                        </strong>

                                        <br>

                                        Pengajuan akan masuk ke status
                                        <strong>Menunggu Persetujuan</strong>
                                        dan harus disetujui pimpinan sebelum dapat dibayar.

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="card-footer">

                            <button
                                type="submit"
                                class="btn btn-danger"
                            >

                                <i class="fas fa-save me-1"></i>

                                Ajukan Pelunasan

                            </button>


                            <a
                                href="{{ route('operasional.show', $pembiayaan) }}"
                                class="btn btn-secondary"
                            >

                                Kembali

                            </a>

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

    const totalNormal =
        {{ $perhitungan['total_sebelum_diskon'] }};


    function formatRupiah(angka)
    {
        return new Intl.NumberFormat('id-ID')
            .format(angka);
    }


    function hitungPelunasan()
    {

        let diskon =
            parseInt(
                $("#diskon").val()
            ) || 0;


        /*
        |--------------------------------------------------------------------------
        | Batasi diskon
        |--------------------------------------------------------------------------
        */

        if (diskon < 0) {

            diskon = 0;

            $("#diskon").val(0);

        }


        if (diskon > totalNormal) {

            diskon = totalNormal;

            $("#diskon").val(totalNormal);

        }


        /*
        |--------------------------------------------------------------------------
        | Total pelunasan
        |--------------------------------------------------------------------------
        */

        let total =
            totalNormal - diskon;


        $("#total_view").val(
            formatRupiah(total)
        );


        /*
        |--------------------------------------------------------------------------
        | Jika ada diskon
        |--------------------------------------------------------------------------
        */

        if (diskon > 0) {

            $("#alasan_diskon_row")
                .show();

            $("#info_normal")
                .hide();

            $("#info_diskon")
                .show();

        } else {

            $("#alasan_diskon_row")
                .hide();

            $("#info_normal")
                .show();

            $("#info_diskon")
                .hide();

        }

    }


    $("#diskon").on(
        "keyup change",
        hitungPelunasan
    );


    hitungPelunasan();

</script>

@endsection