@extends('layouts.app')

@section('content')

<div class="container-xl">

    {{-- Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">

            <div class="col">
                <div class="page-pretitle">
                    Pelunasan
                </div>

                <h2 class="page-title">
                    Persetujuan Diskon Pelunasan
                </h2>

            </div>

            <div class="col-auto ms-auto">
                <span class="badge bg-warning text-warning-fg">
                    {{ $pelunasans->total() }} Menunggu Persetujuan
                </span>
            </div>

        </div>
    </div>


    {{-- Card --}}
    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Pengajuan Diskon
            </h3>

        </div>


        <div class="table-responsive">

            <table class="table table-vcenter card-table">

                <thead>
                    <tr>

                        <th width="50">
                            #
                        </th>

                        <th>
                            Nomor Pelunasan
                        </th>

                        <th>
                            Nasabah
                        </th>

                        <th>
                            Pembiayaan
                        </th>

                        <th class="text-end">
                            Total Pelunasan
                        </th>

                        <th class="text-end">
                            Diskon Diajukan
                        </th>

                        <th class="text-end">
                            Setelah Diskon
                        </th>

                        <th>
                            Pengaju
                        </th>

                        <th width="100">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody>

                    @forelse($pelunasans as $pelunasan)

                        @php
                            $pembiayaan = $pelunasan->pembiayaan;
                            $pengajuan = $pembiayaan?->pengajuan;
                            $nasabah = $pengajuan?->nasabah;
                        @endphp

                        <tr>

                            <td>
                                {{ $pelunasans->firstItem() + $loop->index }}
                            </td>


                            <td>

                                <div class="fw-bold">
                                    {{ $pelunasan->nomor_pelunasan }}
                                </div>

                                <div class="text-secondary">
                                    {{ optional($pelunasan->tanggal_pelunasan)->format('d-m-Y') }}
                                </div>

                            </td>


                            <td>

                                <div class="fw-bold">
                                    {{ $nasabah?->nama ?? '-' }}
                                </div>

                                <div class="text-secondary">
                                    NIK: {{ $nasabah?->nik ?? '-' }}
                                </div>

                            </td>


                            <td>

                                <div class="fw-bold">
                                    {{ $pembiayaan?->nomor_pembiayaan ?? '-' }}
                                </div>

                                <div class="text-secondary">
                                    Plafond:
                                    Rp {{ number_format($pembiayaan?->plafond ?? 0, 0, ',', '.') }}
                                </div>

                            </td>


                            {{-- Total sebelum diskon --}}
                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->total_sebelum_diskon,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- Diskon --}}
                            <td class="text-end">

                                <span class="text-danger fw-bold">

                                    Rp
                                    {{ number_format(
                                        $pelunasan->diskon,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </span>

                            </td>


                            {{-- Perkiraan total --}}
                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->total_sebelum_diskon - $pelunasan->diskon,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td>

                                {{ $pelunasan->creator?->name ?? '-' }}

                            </td>


                            <td>

                                <a href="#"
                                   class="btn btn-primary btn-sm"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalApproval{{ $pelunasan->id }}">

                                    <i class="fa-solid fa-check me-1"></i>

                                    Proses

                                </a>

                            </td>

                        </tr>


                        {{-- Modal Approval --}}
                        <div class="modal modal-blur fade"
                             id="modalApproval{{ $pelunasan->id }}"
                             tabindex="-1"
                             aria-hidden="true">

                            <div class="modal-dialog modal-lg modal-dialog-centered">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">

                                            Persetujuan Diskon Pelunasan

                                        </h5>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                        </button>

                                    </div>


                                    <div class="modal-body">

                                        <div class="row g-3">

                                            {{-- Nasabah --}}
                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Nasabah
                                                </label>

                                                <input type="text"
                                                       class="form-control"
                                                       value="{{ $nasabah?->nama ?? '-' }}"
                                                       readonly>

                                            </div>


                                            {{-- Nomor pembiayaan --}}
                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Nomor Pembiayaan
                                                </label>

                                                <input type="text"
                                                       class="form-control"
                                                       value="{{ $pembiayaan?->nomor_pembiayaan ?? '-' }}"
                                                       readonly>

                                            </div>


                                            {{-- Total --}}
                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Total Pelunasan
                                                </label>

                                                <input type="text"
                                                       class="form-control"
                                                       value="Rp {{ number_format($pelunasan->total_sebelum_diskon, 0, ',', '.') }}"
                                                       readonly>

                                            </div>


                                            {{-- Diskon diajukan --}}
                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Diskon Diajukan
                                                </label>

                                                <input type="text"
                                                       class="form-control"
                                                       value="Rp {{ number_format($pelunasan->diskon, 0, ',', '.') }}"
                                                       readonly>

                                            </div>


                                            {{-- Alasan --}}
                                            <div class="col-12">

                                                <label class="form-label">
                                                    Alasan Diskon
                                                </label>

                                                <textarea class="form-control"
                                                          rows="3"
                                                          readonly>{{ $pelunasan->alasan_diskon }}</textarea>

                                            </div>


                                            {{-- Diskon disetujui --}}
                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Diskon Disetujui
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="number"
                                                       name="diskon_disetujui"
                                                       form="formApproval{{ $pelunasan->id }}"
                                                       class="form-control"
                                                       value="{{ $pelunasan->diskon }}"
                                                       min="0"
                                                       max="{{ $pelunasan->total_sebelum_diskon }}"
                                                       required>

                                                <small class="text-secondary">
                                                    Maksimal:
                                                    Rp {{ number_format($pelunasan->total_sebelum_diskon, 0, ',', '.') }}
                                                </small>

                                            </div>


                                            {{-- Catatan --}}
                                            <div class="col-12">

                                                <label class="form-label">
                                                    Catatan Approval
                                                </label>

                                                <textarea name="catatan_approval"
                                                          form="formApproval{{ $pelunasan->id }}"
                                                          class="form-control"
                                                          rows="3"
                                                          placeholder="Catatan persetujuan (opsional)"></textarea>

                                            </div>

                                        </div>

                                    </div>


                                   <div class="modal-footer">

                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">

                                            Batal

                                        </button>


                                        {{-- Reject --}}
                                        <button type="button"
                                                class="btn btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalReject{{ $pelunasan->id }}"
                                                data-bs-dismiss="modal">

                                            <i class="fa-solid fa-xmark me-1"></i>

                                            Tolak

                                        </button>


                                        {{-- Approve --}}
                                        <form id="formApproval{{ $pelunasan->id }}"
                                            action="{{ route('pelunasan.approve', $pelunasan) }}"
                                            method="POST">

                                            @csrf

                                            <button type="submit"
                                                    class="btn btn-success">

                                                <i class="fa-solid fa-check me-1"></i>

                                                Setujui Pelunasan

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- Modal Reject --}}
<div class="modal modal-blur fade"
     id="modalReject{{ $pelunasan->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title text-danger">

                    <i class="fa-solid fa-triangle-exclamation me-2"></i>

                    Tolak Pengajuan Pelunasan

                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>


            <form action="{{ route('pelunasan.reject', $pelunasan) }}"
                  method="POST">

                @csrf

                <div class="modal-body">

                    <div class="alert alert-warning">

                        <div class="fw-bold mb-1">
                            Anda akan menolak pengajuan pelunasan ini.
                        </div>

                        <div>
                            Pastikan alasan penolakan sudah sesuai.
                        </div>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Nomor Pelunasan
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $pelunasan->nomor_pelunasan }}"
                               readonly>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Nasabah
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $nasabah?->nama ?? '-' }}"
                               readonly>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Alasan / Catatan Penolakan
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="catatan_approval"
                            class="form-control"
                            rows="4"
                            required
                            placeholder="Masukkan alasan penolakan..."></textarea>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Batal

                    </button>


                    <button type="submit"
                            class="btn btn-danger">

                        <i class="fa-solid fa-xmark me-1"></i>

                        Ya, Tolak Pengajuan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
                    @empty

                        <tr>

                            <td colspan="9"
                                class="text-center py-5">

                                <div class="empty">

                                    <div class="empty-img">
                                        <i class="fa-solid fa-circle-check fa-3x text-success"></i>
                                    </div>

                                    <p class="empty-title">
                                        Tidak ada pengajuan diskon
                                    </p>

                                    <p class="empty-subtitle text-secondary">
                                        Saat ini tidak ada pelunasan yang menunggu persetujuan.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($pelunasans->hasPages())

            <div class="card-footer">

                {{ $pelunasans->links() }}

            </div>

        @endif

    </div>

</div>

@endsection