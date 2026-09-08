@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="page-header d-print-none">

        <div class="container-xl">

            <div class="row g-2 align-items-center">

                <div class="col">

                    <h2 class="page-title">
                        Detail Pelunasan
                    </h2>

                    <div class="text-muted mt-1">
                        Transaksi penyelesaian pembiayaan
                    </div>

                </div>

                <div class="col-auto">

                    @php
                        $statusClass = match($pelunasan->status) {
                            'dibayar' => 'bg-success',
                            'siap_dibayar' => 'bg-warning text-dark',
                            'menunggu_persetujuan' => 'bg-info',
                            'ditolak' => 'bg-danger',
                            'dibatalkan' => 'bg-secondary',
                            default => 'bg-primary',
                        };
                    @endphp

                    <span class="badge {{ $statusClass }} fs-6">

                        {{ ucfirst(str_replace('_', ' ', $pelunasan->status)) }}

                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- BODY --}}
    {{-- ========================================================= --}}

    <div class="page-body">

        <div class="container-xl">


            {{-- ================================================= --}}
            {{-- ALERT --}}
            {{-- ================================================= --}}

            @if(session('success'))

                <div class="alert alert-success alert-dismissible">

                    <i class="fas fa-check-circle me-1"></i>

                    {{ session('success') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            @if(session('error'))

                <div class="alert alert-danger alert-dismissible">

                    <i class="fas fa-exclamation-circle me-1"></i>

                    {{ session('error') }}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- IDENTITAS TRANSAKSI --}}
            {{-- ================================================= --}}

            <div class="card mb-3">

                <div class="card-header">

                    <h3 class="card-title">
                        Informasi Transaksi
                    </h3>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <table class="table table-bordered">

                                <tr>
                                    <th width="40%">
                                        Nomor Pelunasan
                                    </th>

                                    <td>
                                        <strong>
                                            {{ $pelunasan->nomor_pelunasan }}
                                        </strong>
                                    </td>
                                </tr>

                                <tr>

                                    <th>
                                        Nomor Pembiayaan
                                    </th>

                                    <td>

                                        <a href="{{ route(
                                            'operasional.show',
                                            $pelunasan->pembiayaan
                                        ) }}">

                                            {{ $pelunasan->pembiayaan->nomor_pembiayaan }}

                                        </a>

                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Debitur
                                    </th>

                                    <td>
                                        {{ optional(
                                            $pelunasan->pembiayaan->pengajuan->nasabah
                                        )->nama ?? '-' }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Jenis Pelunasan
                                    </th>

                                    <td>

                                        @if($pelunasan->jenis_pelunasan === 'dengan_diskon')

                                            <span class="badge bg-warning text-dark">
                                                Dengan Diskon
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                Normal
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Tanggal Pelunasan
                                    </th>

                                    <td>
                                        {{ $pelunasan->tanggal_pelunasan?->format('d-m-Y') ?? '-' }}
                                    </td>

                                </tr>

                            </table>

                        </div>


                        <div class="col-md-6">

                            <table class="table table-bordered">

                                <tr>

                                    <th width="40%">
                                        Petugas Pengajuan
                                    </th>

                                    <td>
                                        {{ optional($pelunasan->creator)->name ?? '-' }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Dibuat
                                    </th>

                                    <td>
                                        {{ $pelunasan->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Status
                                    </th>

                                    <td>

                                        <span class="badge {{ $statusClass }}">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $pelunasan->status
                                                )
                                            ) }}

                                        </span>

                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Pembayaran
                                    </th>

                                    <td>

                                        @if($pelunasan->status === 'dibayar')

                                            <span class="badge bg-success">

                                                <i class="fas fa-check me-1"></i>

                                                Sudah Dibayar

                                            </span>

                                        @else

                                            <span class="text-muted">
                                                Belum Dibayar
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- PERHITUNGAN --}}
            {{-- ================================================= --}}

            <div class="card mb-3">

                <div class="card-header">

                    <h3 class="card-title">
                        Perhitungan Pelunasan
                    </h3>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered mb-0">

                        <tr>

                            <th width="40%">
                                Sisa Pokok
                            </th>

                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->sisa_pokok,
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

                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->sisa_bunga,
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

                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->denda,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>


                        <tr class="table-light">

                            <th>
                                Total Sebelum Diskon
                            </th>

                            <td class="text-end fw-bold">

                                Rp
                                {{ number_format(
                                    $pelunasan->total_sebelum_diskon,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>


                        @if($pelunasan->jenis_pelunasan === 'dengan_diskon')

                            <tr>

                                <th>
                                    Diskon Diajukan
                                </th>

                                <td class="text-end text-warning">

                                    Rp
                                    {{ number_format(
                                        $pelunasan->diskon,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </td>

                            </tr>


                            <tr>

                                <th>
                                    Diskon Disetujui
                                </th>

                                <td class="text-end text-success fw-bold">

                                    @if(!is_null($pelunasan->diskon_disetujui))

                                        Rp
                                        {{ number_format(
                                            $pelunasan->diskon_disetujui,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    @else

                                        <span class="text-muted">
                                            Belum ditentukan
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endif


                        <tr class="table-success">

                            <th class="fs-5">

                                Total Pelunasan

                            </th>

                            <td class="text-end fw-bold fs-5">

                                Rp
                                {{ number_format(
                                    $pelunasan->total_pelunasan,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- ALASAN & KETERANGAN --}}
            {{-- ================================================= --}}

            @if(
                $pelunasan->alasan_diskon ||
                $pelunasan->keterangan
            )

                <div class="card mb-3">

                    <div class="card-header">

                        <h3 class="card-title">
                            Keterangan Pengajuan
                        </h3>

                    </div>

                    <div class="card-body">

                        @if($pelunasan->alasan_diskon)

                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Alasan Diskon
                                </label>

                                <div class="border rounded p-3 bg-light">

                                    {{ $pelunasan->alasan_diskon }}

                                </div>

                            </div>

                        @endif


                        @if($pelunasan->keterangan)

                            <div>

                                <label class="form-label fw-bold">
                                    Keterangan
                                </label>

                                <div class="border rounded p-3 bg-light">

                                    {{ $pelunasan->keterangan }}

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- APPROVAL --}}
            {{-- ================================================= --}}

            @if(
                $pelunasan->jenis_pelunasan === 'dengan_diskon'
            )

                <div class="card mb-3">

                    <div class="card-header">

                        <h3 class="card-title">
                            Persetujuan Diskon
                        </h3>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table class="table table-bordered">

                                    <tr>

                                        <th width="45%">
                                            Status
                                        </th>

                                        <td>

                                            @if(
                                                $pelunasan->status === 'menunggu_persetujuan'
                                            )

                                                <span class="badge bg-info">
                                                    Menunggu Persetujuan
                                                </span>

                                            @elseif(
                                                in_array(
                                                    $pelunasan->status,
                                                    ['siap_dibayar','dibayar']
                                                )
                                            )

                                                <span class="badge bg-success">
                                                    Disetujui
                                                </span>

                                            @elseif(
                                                $pelunasan->status === 'ditolak'
                                            )

                                                <span class="badge bg-danger">
                                                    Ditolak
                                                </span>

                                            @else

                                                <span class="badge bg-secondary">
                                                    -
                                                </span>

                                            @endif

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Diskon Diajukan
                                        </th>

                                        <td>

                                            Rp
                                            {{ number_format(
                                                $pelunasan->diskon,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Diskon Disetujui
                                        </th>

                                        <td class="fw-bold text-success">

                                            @if(!is_null($pelunasan->diskon_disetujui))

                                                Rp
                                                {{ number_format(
                                                    $pelunasan->diskon_disetujui,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                            @else

                                                -

                                            @endif

                                        </td>

                                    </tr>

                                </table>

                            </div>


                            <div class="col-md-6">

                                <table class="table table-bordered">

                                    <tr>

                                        <th width="45%">
                                            Disetujui Oleh
                                        </th>

                                        <td>
                                            {{ optional(
                                                $pelunasan->approver
                                            )->name ?? '-' }}
                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Waktu Approval
                                        </th>

                                        <td>

                                            {{ $pelunasan->approved_at?->format(
                                                'd-m-Y H:i:s'
                                            ) ?? '-' }}

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Catatan Approval
                                        </th>

                                        <td>

                                            {{ $pelunasan->catatan_approval ?: '-' }}

                                        </td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- PEMBAYARAN --}}
            {{-- ================================================= --}}

            <div class="card mb-3">

                <div class="card-header">

                    <h3 class="card-title">
                        Informasi Pembayaran
                    </h3>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <table class="table table-bordered">

                                <tr>

                                    <th width="45%">
                                        Status Pembayaran
                                    </th>

                                    <td>

                                        @if($pelunasan->status === 'dibayar')

                                            <span class="badge bg-success">
                                                Lunas / Dibayar
                                            </span>

                                        @else

                                            <span class="badge bg-warning text-dark">
                                                Belum Dibayar
                                            </span>

                                        @endif

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Total Dibayar
                                    </th>

                                    <td class="fw-bold">

                                        @if($pelunasan->status === 'dibayar')

                                            Rp
                                            {{ number_format(
                                                $pelunasan->total_pelunasan,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        @else

                                            Rp 0

                                        @endif

                                    </td>

                                </tr>

                            </table>

                        </div>


                        <div class="col-md-6">

                            <table class="table table-bordered">

                                <tr>

                                    <th width="45%">
                                        Dibayar Oleh
                                    </th>

                                    <td>

                                        {{ optional(
                                            $pelunasan->payer
                                        )->name ?? '-' }}

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Waktu Pembayaran
                                    </th>

                                    <td>

                                        {{ $pelunasan->paid_at?->format(
                                            'd-m-Y H:i:s'
                                        ) ?? '-' }}

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- ANGSURAN YANG DITUTUP --}}
            {{-- ================================================= --}}

            <div class="card mb-3">

                <div class="card-header">

                    <h3 class="card-title">
                        Angsuran yang Ditutup
                    </h3>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead>

                            <tr>

                                <th width="80">
                                    Ke
                                </th>

                                <th>
                                    Jatuh Tempo
                                </th>

                                <th class="text-end">
                                    Pokok
                                </th>

                                <th class="text-end">
                                    Bunga
                                </th>

                                <th class="text-end">
                                    Total Angsuran
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse(
                                $pelunasan->angsurans
                                as $angsuran
                            )

                                <tr>

                                    <td>
                                        {{ $angsuran->angsuran_ke }}
                                    </td>

                                    <td>

                                        {{ $angsuran->tanggal_jatuh_tempo?->format(
                                            'd-m-Y'
                                        ) ?? '-' }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $angsuran->pokok_angsuran,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $angsuran->bunga_angsuran,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>

                                    <td class="text-end fw-bold">

                                        Rp
                                        {{ number_format(
                                            $angsuran->total_angsuran,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>

                                    <td>

                                        <span class="badge bg-success">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $angsuran->status
                                                )
                                            ) }}

                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center text-muted py-4"
                                    >

                                        Belum ada angsuran yang
                                        terhubung dengan pelunasan.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- FOOTER ACTION --}}
            {{-- ================================================= --}}

            <div class="d-flex justify-content-between">

                <a
                    href="{{ route(
                        'operasional.show',
                        $pelunasan->pembiayaan
                    ) }}"
                    class="btn btn-secondary"
                >

                    <i class="fas fa-arrow-left me-1"></i>

                    Kembali

                </a>


                <div>

                    <a
                        href="{{ route(
                            'pelunasan.cetak',
                            $pelunasan
                        ) }}"
                        target="_blank"
                        class="btn btn-danger"
                    >

                        <i class="fas fa-file-pdf me-1"></i>

                        Cetak Bukti Pelunasan

                    </a>


                    @if($pelunasan->status === 'siap_dibayar')

                        <button
                            type="button"
                            class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#modalBayar"
                        >

                            <i class="fas fa-money-bill-wave me-1"></i>

                            Bayar Pelunasan

                        </button>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- MODAL BAYAR --}}
{{-- ============================================================= --}}

@if($pelunasan->status === 'siap_dibayar')

<div class="modal fade"
    id="modalBayar"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                action="{{ route(
                    'pelunasan.bayar',
                    $pelunasan
                ) }}"
                method="POST"
            >

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Pembayaran Pelunasan
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Nomor Pelunasan
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $pelunasan->nomor_pelunasan }}"
                            readonly
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Total Pelunasan
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="text"
                                class="form-control fw-bold"
                                value="{{ number_format(
                                    $pelunasan->total_pelunasan,
                                    0,
                                    ',',
                                    '.'
                                ) }}"
                                readonly
                            >

                        </div>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Tanggal Pembayaran
                        </label>

                        <input
                            type="date"
                            name="tanggal_bayar"
                            class="form-control"
                            value="{{ now()->format('Y-m-d') }}"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Jumlah Pembayaran
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="jumlah_bayar"
                                class="form-control fw-bold"
                                value="{{ $pelunasan->total_pelunasan }}"
                                min="{{ $pelunasan->total_pelunasan }}"
                                max="{{ $pelunasan->total_pelunasan }}"
                                required
                            >

                        </div>

                        <small class="text-muted">

                            Jumlah pembayaran harus sama dengan
                            total pelunasan.

                        </small>

                    </div>


                    @if(
                        $pelunasan->diskon_disetujui !== null
                    )

                        <div class="alert alert-success">

                            <div class="fw-bold">
                                Diskon Disetujui
                            </div>

                            <div>

                                Rp
                                {{ number_format(
                                    $pelunasan->diskon_disetujui,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </div>

                        </div>

                    @endif


                    <div class="alert alert-warning mb-0">

                        <i class="fas fa-exclamation-triangle me-1"></i>

                        Setelah pembayaran berhasil,
                        pembiayaan akan berstatus
                        <strong>LUNAS</strong>.

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="fas fa-check me-1"></i>

                        Konfirmasi Pembayaran

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif

@endsection