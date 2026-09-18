@extends('layouts.app')

@section('content')

<div class="container-xl">

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">

            <div class="col">

                <div class="page-pretitle">
                    Pelunasan
                </div>

                <h2 class="page-title">
                    Pengajuan Diskon Pelunasan
                </h2>

            </div>

        </div>
    </div>


    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Riwayat Pengajuan Diskon
            </h3>

        </div>


        <div class="table-responsive">

            <table class="table table-vcenter card-table">

                <thead>
                    <tr>

                        <th>#</th>

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
                            Total
                        </th>

                        <th class="text-end">
                            Diskon
                        </th>

                        <th class="text-end">
                            Total Akhir
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
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

                            </td>


                            <td>

                                <div class="fw-bold">
                                    {{ $nasabah?->nama ?? '-' }}
                                </div>

                                <div class="text-secondary">
                                    {{ $nasabah?->nik ?? '-' }}
                                </div>

                            </td>


                            <td>
                                {{ $pembiayaan?->nomor_pembiayaan ?? '-' }}
                            </td>


                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->total_sebelum_diskon,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $pelunasan->diskon,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td class="text-end fw-bold">

                                Rp
                                {{ number_format(
                                    $pelunasan->total_pelunasan,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($pelunasan->status === 'menunggu_persetujuan')

                                    <span class="badge bg-warning text-warning-fg">
                                        Menunggu Persetujuan
                                    </span>

                                @elseif($pelunasan->status === 'siap_dibayar')

                                    <span class="badge bg-success text-success-fg">
                                        Disetujui
                                    </span>

                                @elseif($pelunasan->status === 'dibayar')

                                    <span class="badge bg-primary text-primary-fg">
                                        Sudah Dibayar
                                    </span>

                                @elseif($pelunasan->status === 'ditolak')

                                    <span class="badge bg-danger text-danger-fg">
                                        Ditolak
                                    </span>

                                @elseif($pelunasan->status === 'dibatalkan')

                                    <span class="badge bg-secondary">
                                        Dibatalkan
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $pelunasan->status)) }}
                                    </span>

                                @endif

                            </td>


                            <td>

                                {{ optional($pelunasan->tanggal_pelunasan)
                                    ->format('d-m-Y') }}

                            </td>


                            <td>

                                <a href="{{ route('pelunasan.show', $pelunasan) }}"
                                   class="btn btn-sm btn-primary">

                                    <i class="fa-solid fa-eye me-1"></i>

                                    Detail

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10"
                                class="text-center py-5">

                                <div class="empty">

                                    <div class="empty-icon">
                                        <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                                    </div>

                                    <p class="empty-title">
                                        Belum ada pengajuan diskon
                                    </p>

                                    <p class="empty-subtitle text-secondary">
                                        Pengajuan diskon pelunasan yang Anda buat akan muncul di sini.
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