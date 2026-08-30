@extends('layouts.app')

@section('content')

<div class="container">

    <div class="page-header mb-4">

        <h2 class="page-title">
            Approval Diskon Denda
        </h2>

        <div class="text-muted">
            Daftar pengajuan diskon denda yang menunggu persetujuan
        </div>

    </div>


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR --}}

    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Pengajuan Menunggu Approval
            </h3>

        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Debitur</th>
                        <th>No Pembiayaan</th>
                        <th>Angsuran</th>
                        <th>Denda</th>
                        <th>Diskon</th>
                        <th>Denda Setelah Diskon</th>
                        <th>Diajukan Oleh</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pengajuans as $item)
                    @php
                        $angsuran = $item->angsuran;
                        $nasabah = $angsuran ->pembiayaan ->pengajuan ->nasabah;
                    @endphp
                    <tr>
                        <td>{{ $pengajuans->firstItem() + $loop->index }}</td>
                        <td>{{ $nasabah->nama }}</td>
                        <td>{{ $angsuran->pembiayaan->nomor_pembiayaan }}</td>
                        <td>Ke-{{ $angsuran->angsuran_ke }}</td>
                        <td>Rp{{ number_format($item->denda,0,',','.') }}</td>
                        <td>
                            <strong class="text-warning">
                                Rp{{ number_format($item->diskon_denda,0,',','.') }}
                            </strong>
                        </td>
                        <td>Rp{{ number_format($item->denda_setelah_diskon,0,',','.') }}</td>
                        <td>{{ $item->diajukanOleh->name }}</td>
                        <td style="min-width:250px">{{ $item->alasan }}</td>
                        <td style="min-width:180px">
                            {{-- SETUJUI --}}
                            <form action="{{ route('diskon-denda.approve',$item) }}"method="POST"class="d-inline"
                                onsubmit="return confirm('Apakah pengajuan diskon ini akan disetujui?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="ti ti-check"></i>
                                    Setujui
                                </button>
                            </form>
                            {{-- TOLAK --}}
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalTolak" data-id="{{ $item->id }}">
                                <i class="ti ti-x"></i>
                                Tolak
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            Tidak ada pengajuan diskon denda.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($pengajuans->hasPages())
            <div class="card-footer">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL TOLAK --}}
{{-- ========================================================= --}}

<div
    class="modal fade"
    id="modalTolak"
    tabindex="-1"
>

    <div class="modal-dialog">

        <form
            method="POST"
            id="formTolak"
        >

            @csrf


            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Tolak Pengajuan Diskon
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <label class="form-label">

                        Alasan Penolakan
                        <span class="text-danger">*</span>

                    </label>

                    <textarea
                        name="catatan_approval"
                        class="form-control"
                        rows="4"
                        required
                    ></textarea>

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

                        Tolak Pengajuan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection


@section('scripts')

<script>

$(function () {

    $('#modalTolak').on('show.bs.modal', function (event) {

        const button = $(event.relatedTarget);
        const id = button.data('id');

        $('#formTolak').attr(
            'action',
            "{{ url('/diskon-denda') }}/" + id + "/reject"
        );

        $('#formTolak textarea[name="catatan_approval"]').val('');
    });

});

</script>

@endsection