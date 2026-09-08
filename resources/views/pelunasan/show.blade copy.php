@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Detail Pelunasan
                </h2>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
            <div class="card">
                <div class="card-header bg-blue-lt">
                    <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">{{ $pelunasan->nomor_pelunasan }}</h3>
                            <h5 class="text-muted">
                                {{ $pelunasan->pembiayaan->pengajuan->nasabah->nama }}
                            </h5>
        
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success fs-6">LUNAS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <strong>Data Nasabah</strong>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="35%">Nama</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->nik }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->alamat }}</td>
                        </tr>
                        <tr>
                            <th>Marketing</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->marketing->name }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->cabang->nama_cabang }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <strong>Data Pembiayaan</strong>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">No Pembiayaan</th>
                            <td>{{ $pelunasan->pembiayaan->nomor_pembiayaan }}</td>
                        </tr>
                        <tr>
                            <th>Plafond</th>
                            <td>Rp {{ number_format($pelunasan->pembiayaan->plafond,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Tenor</th>
                            <td>{{ $pelunasan->pembiayaan->tenor }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Tanggal Akad</th>
                            <td>{{ optional($pelunasan->pembiayaan->akad)->tanggal_akad?->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pencairan</th>
                            <td>{{ optional(optional($pelunasan->pembiayaan->akad)->pencairan)->tanggal_pencairan?->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= PELUNASAN ================= --}}
        <div class="card mt-3">

            <div class="card-header">
                <strong>Informasi Pelunasan</strong>
            </div>

            <table class="table table-bordered mb-0">

                <tr>
                    <th width="25%">Nomor Pelunasan</th>
                    <td>{{ $pelunasan->nomor_pelunasan }}</td>
                </tr>

                <tr>
                    <th>Tanggal Pelunasan</th>
                    <td>
                        {{ $pelunasan->tanggal_pelunasan->format('d-m-Y') }}
                    </td>
                </tr>

                <tr>
                    <th>Jenis Pelunasan</th>
                    <td>

                        @if($pelunasan->jenis_pelunasan === 'normal')

                            <span class="badge bg-success">
                                Normal
                            </span>

                        @else

                            <span class="badge bg-warning text-dark">
                                Dengan Diskon
                            </span>

                        @endif

                    </td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>

                        @switch($pelunasan->status)

                            @case('menunggu_persetujuan')
                                <span class="badge bg-warning text-dark">
                                    Menunggu Persetujuan
                                </span>
                                @break

                            @case('disetujui')
                                <span class="badge bg-info">
                                    Disetujui
                                </span>
                                @break

                            @case('siap_dibayar')
                                <span class="badge bg-primary">
                                    Siap Dibayar
                                </span>
                                @break

                            @case('dibayar')
                                <span class="badge bg-success">
                                    Dibayar
                                </span>
                                @break

                            @case('ditolak')
                                <span class="badge bg-danger">
                                    Ditolak
                                </span>
                                @break

                            @default
                                <span class="badge bg-secondary">
                                    {{ ucfirst($pelunasan->status) }}
                                </span>

                        @endswitch

                    </td>
                </tr>

                <tr>
                    <th>Sisa Pokok</th>
                    <td>
                        Rp {{ number_format($pelunasan->sisa_pokok,0,',','.') }}
                    </td>
                </tr>

                <tr>
                    <th>Sisa Bunga</th>
                    <td>
                        Rp {{ number_format($pelunasan->sisa_bunga,0,',','.') }}
                    </td>
                </tr>

                <tr>
                    <th>Denda</th>
                    <td>
                        Rp {{ number_format($pelunasan->denda,0,',','.') }}
                    </td>
                </tr>

                <tr>
                    <th>Total Sebelum Diskon</th>
                    <td>
                        Rp {{ number_format($pelunasan->total_sebelum_diskon,0,',','.') }}
                    </td>
                </tr>

                <tr>
                    <th>Diskon Diajukan</th>
                    <td>
                        Rp {{ number_format($pelunasan->diskon,0,',','.') }}
                    </td>
                </tr>

                @if($pelunasan->diskon_disetujui !== null)

                    <tr>
                        <th>Diskon Disetujui</th>
                        <td>
                            Rp {{ number_format($pelunasan->diskon_disetujui,0,',','.') }}
                        </td>
                    </tr>

                @endif

                <tr class="table-success">

                    <th>Total Pelunasan</th>

                    <th>
                        Rp {{ number_format($pelunasan->total_pelunasan,0,',','.') }}
                    </th>

                </tr>

                @if($pelunasan->alasan_diskon)

                    <tr>
                        <th>Alasan Pengajuan Diskon</th>

                        <td>
                            {{ $pelunasan->alasan_diskon }}
                        </td>
                    </tr>

                @endif

                <tr>
                    <th>Keterangan</th>

                    <td>
                        {{ $pelunasan->keterangan ?: '-' }}
                    </td>
                </tr>

                <tr>
                    <th>Petugas</th>

                    <td>
                        {{ $pelunasan->creator->name }}
                    </td>
                </tr>

                @if($pelunasan->approved_by)

                    <tr>
                        <th>Diproses Oleh</th>

                        <td>
                            {{ $pelunasan->approver->name ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Waktu Approval</th>

                        <td>
                            {{ $pelunasan->approved_at?->format('d-m-Y H:i') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Catatan Approval</th>

                        <td>
                            {{ $pelunasan->catatan_approval ?: '-' }}
                        </td>
                    </tr>

                @endif

            </table>

        </div>

        @if(
                auth()->user()->hasRole('komisaris|direktur|kacab') &&
                $pelunasan->status === 'menunggu_persetujuan'
            )
            <div class="card mt-3 border-warning">

                <div class="card-header bg-warning-lt">

                    <strong>
                        Persetujuan Pimpinan
                    </strong>

                </div>

                <form
                    method="POST"
                    action="{{ route('pelunasan.approve', $pelunasan) }}"
                >

                    @csrf

                    <div class="card-body">

                        <div class="alert alert-warning">

                            Pengajuan pelunasan ini membutuhkan persetujuan pimpinan.

                            <br>

                            Diskon yang diajukan:

                            <strong>
                                Rp {{ number_format($pelunasan->diskon,0,',','.') }}
                            </strong>

                        </div>


                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Total Sebelum Diskon
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control text-end"
                                        value="Rp {{ number_format($pelunasan->total_sebelum_diskon,0,',','.') }}"
                                        readonly
                                    >

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Diskon Diajukan
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control text-end"
                                        value="Rp {{ number_format($pelunasan->diskon,0,',','.') }}"
                                        readonly
                                    >

                                </div>

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Diskon Disetujui
                                    </label>

                                    <input
                                        type="number"
                                        name="diskon_disetujui"
                                        id="diskon_disetujui"
                                        class="form-control text-end"
                                        value="{{ $pelunasan->diskon }}"
                                        min="0"
                                        max="{{ $pelunasan->total_sebelum_diskon }}"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Total Akhir
                                    </label>

                                    <input
                                        type="text"
                                        id="total_akhir"
                                        class="form-control text-end fw-bold"
                                        readonly
                                    >

                                </div>

                            </div>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Catatan Approval
                            </label>

                            <textarea
                                name="catatan_approval"
                                class="form-control"
                                rows="4"
                                placeholder="Catatan persetujuan pimpinan..."
                            ></textarea>

                        </div>

                    </div>


                    <div class="card-footer">

                        <button
                            type="submit"
                            class="btn btn-success"
                        >

                            <i class="fas fa-check me-1"></i>

                            Setujui Pelunasan

                        </button>


                        <button
                            type="button"
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalReject"
                        >

                            <i class="fas fa-times me-1"></i>

                            Tolak Pengajuan

                        </button>

                    </div>

                </form>

            </div>
        @endif


        @if(
            auth()->user()->hasRole('admincabang') &&
            $pelunasan->status === 'siap_dibayar'
        )

        <div class="card mt-3 border-success">

            <div class="card-header bg-success-lt">

                <strong>
                    Pembayaran Pelunasan
                </strong>

            </div>

            <div class="card-body">

                <div class="alert alert-success">

                    Pelunasan telah disetujui dan
                    <strong>siap dibayar</strong>.

                </div>

                <div class="row">

                    <div class="col-md-6">

                        <strong>Total yang harus dibayar</strong>

                        <div class="fs-2 fw-bold text-success">
                            Rp{{ number_format($pelunasan->total_pelunasan,0,',','.') }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <strong>Diskon Disetujui</strong>

                        <div class="fs-3">
                            Rp{{ number_format($pelunasan->diskon_disetujui ?? 0,0,',','.') }}

                        </div>

                    </div>

                </div>

            </div>

            @if($pelunasan->status === 'siap_dibayar')
                <div class="card-footer">
                    <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalBayar">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Bayar Pelunasan
                    </button>
                </div>
            @endif
        </div>

        @endif
        <div class="mt-3">
            <a href="{{ route('pelunasan.cetak',$pelunasan) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i>
                Cetak PDF
            </a>
            <a href="{{ route('operasional.show',$pelunasan->pembiayaan) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
      </div>
    </div>
    
</div>

<div class="modal fade" id="modalReject" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('pelunasan.reject', $pelunasan) }}"
            >

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Tolak Pengajuan Pelunasan
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="alert alert-danger">

                        Apakah Anda yakin ingin menolak
                        pengajuan pelunasan ini?

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Alasan Penolakan
                        </label>

                        <textarea
                            name="catatan_approval"
                            class="form-control"
                            rows="5"
                            required
                            placeholder="Masukkan alasan penolakan..."
                        ></textarea>

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
                        class="btn btn-danger"
                    >

                        <i class="fas fa-times me-1"></i>

                        Tolak Pengajuan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- ================= MODAL BAYAR PELUNASAN ================= --}}
<div class="modal fade" id="modalBayar" tabindex="-1"
    aria-labelledby="modalBayarLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('pelunasan.bayar', $pelunasan) }}"
                method="POST">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalBayarLabel">
                        Pembayaran Pelunasan
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Tanggal Pembayaran
                        </label>

                        <input type="date"
                            name="tanggal_bayar"
                            class="form-control"
                            value="{{ now()->format('Y-m-d') }}"
                            required>
                    </div>

                    {{-- Total --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Total Pelunasan
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                Rp
                            </span>

                            <input type="text"
                                class="form-control fw-bold"
                                value="{{ number_format($pelunasan->total_pelunasan, 0, ',', '.') }}"
                                readonly>
                        </div>

                    </div>

                    {{-- Jumlah Bayar --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Jumlah Dibayar
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                Rp
                            </span>

                            <input type="number"
                                name="jumlah_bayar"
                                class="form-control fw-bold"
                                value="{{ $pelunasan->total_pelunasan }}"
                                min="{{ $pelunasan->total_pelunasan }}"
                                max="{{ $pelunasan->total_pelunasan }}"
                                required>
                        </div>

                        <small class="text-muted">
                            Pembayaran pelunasan harus sesuai dengan
                            total yang telah ditetapkan.
                        </small>

                    </div>

                    {{-- Informasi Diskon --}}
                    @if($pelunasan->diskon_disetujui > 0)

                        <div class="alert alert-success">

                            <div>
                                <strong>Diskon Disetujui</strong>
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
                        pembiayaan akan otomatis berstatus
                        <strong>LUNAS</strong>.

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                        class="btn btn-success">

                        <i class="fas fa-check me-1"></i>

                        Konfirmasi Pembayaran

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

    const totalSebelumDiskon =
        {{ $pelunasan->total_sebelum_diskon }};

    function hitungTotalApproval()
    {

        let diskon =
            parseInt(
                $('#diskon_disetujui').val()
            ) || 0;


        if (diskon < 0) {

            diskon = 0;

            $('#diskon_disetujui').val(0);

        }


        if (diskon > totalSebelumDiskon) {

            diskon = totalSebelumDiskon;

            $('#diskon_disetujui')
                .val(totalSebelumDiskon);

        }


        let total =
            totalSebelumDiskon - diskon;


        $('#total_akhir').val(
            'Rp ' +
            new Intl.NumberFormat('id-ID')
                .format(total)
        );

    }


    $('#diskon_disetujui').on(
        'keyup change',
        hitungTotalApproval
    );


    hitungTotalApproval();

</script>

@endsection