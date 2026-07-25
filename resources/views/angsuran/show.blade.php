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
                                            <th width="180">Nomor Pembiayaan</th>
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
                                            <th width="180">Total Angsuran</th>
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
                        <label class="form-label">Total Terbayar</label>
                        <input type="text" id="m_total_terbayar" class="form-control" readonly>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sisa Tagihan</label>
                        <input type="text" id="m_sisa_tagihan" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nominal Bayar</label>
                        <input type="number" id="jumlah_bayar" class="form-control">
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
    $('.btn-bayar').click(function () {
        let id = $(this).data('id');
        $.get("/angsuran/jadwal/" + id + "/json", function (r) {
            $('#angsuran_id').val(r.id);
            $('#m_nama').val(r.nama);
            $('#m_nomor').val(r.nomor_pembiayaan);
            $('#m_ke').val(r.angsuran_ke);
            $('#m_jatuh_tempo').val(r.tanggal_jatuh_tempo);
            $('#m_status').val(r.status);
            $('#m_pokok').val(r.pokok);
            $('#m_bunga').val(r.bunga);
            $('#m_total').val(r.total);
            $('#m_total_terbayar').val(r.total_terbayar_format);
            $('#m_sisa_tagihan').val(r.sisa_tagihan_format);
            /*
            * Default pembayaran
            * langsung sebesar sisa tagihan
            */
            $('#jumlah_bayar').val(r.sisa_tagihan);
            $('#m_denda').val(r.denda);
            $('#metode').val(r.metode);
            $('#keterangan').val('');

            const modal = new bootstrap.Modal(
                document.getElementById('modalBayar')
            );

            modal.show();

        });

    });

});

$('#btnSimpanBayar').click(function () {
    let id = $('#angsuran_id').val();
    $.ajax({
        url: '/angsuran/jadwal/' + id + '/bayar',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            tanggal_bayar: $('#tanggal_bayar').val(),
            jumlah_bayar: $('#jumlah_bayar').val(),
            metode: $('#metode').val(),
            keterangan: $('#keterangan').val()
        },
        beforeSend: function () {
            $('#btnSimpanBayar').prop('disabled', true).html('Menyimpan...');
        },

        success: function (r) {
            const modal = bootstrap.Modal.getInstance(
                document.getElementById('modalBayar')
            );

            modal.hide();
            $('#status-' + id).removeClass('bg-danger bg-secondary bg-success').addClass(r.row.badge).text(r.row.status);
            if (r.row.status === 'Dibayar') {
                $('#btn-' + id).remove();
            }
            $('#terbayar-' + id).text('Rp ' + r.row.total_terbayar_format);
            $('#sisa-' + id).text('Rp ' + r.row.sisa_tagihan_format);
            $('#card_dibayar').text(r.summary.sudah_dibayar);
            $('#card_sisa').text(r.summary.sisa_angsuran);
            $('#card_outstanding').text('Rp ' + r.summary.outstanding_format);
        },

        error: function (xhr) {
            let msg = 'Terjadi kesalahan.';
            if (xhr.responseJSON) {
                msg = xhr.responseJSON.message;
            }
            alert(msg);
        },

        complete: function () {
            $('#btnSimpanBayar').prop('disabled', false).html('<i class="ti ti-device-floppy"></i> Simpan Pembayaran');
        }
    });
});

$(function () {
    $('.btn-history').click(function () {
        let id = $(this).data('id');
        $.ajax({
            url: "/angsuran/jadwal/" + id + "/history",
            type: "GET",
            success: function (r) {
                $('#h_nama').val(r.nama);
                $('#h_nomor').val(r.nomor_pembiayaan);
                $('#h_ke').val(r.angsuran_ke);
                $('#h_status').val(r.status);
                $('#h_tagihan').val("Rp " + r.total_tagihan);
                $('#h_terbayar').val("Rp " + r.total_terbayar);
                $('#h_sisa').val("Rp " + r.sisa_tagihan);
                let rows = [];
                if (r.history.length === 0) {
                    rows.push(`
                        <tr>
                            <td colspan="8" class="text-center">
                                Belum ada pembayaran.
                            </td>
                        </tr>
                    `);
                } else {
                    $.each(r.history, function (i, item) {
                        rows.push(`
                        <tr>
                            <td>${item.tanggal}</td>
                            <td>${item.nomor}</td>
                            <td class="text-end">Rp ${item.jumlah_bayar_format}</td>
                            <td class="text-end">Rp ${item.denda_format}</td>
                            <td class="text-end">Rp ${item.total_format}</td>
                            <td>${item.metode}</td>
                            <td>${item.user ?? '-'}</td>
                            <td>
                                <a target="_blank" href="/angsuran/pembayaran/${item.id}/cetak" class="btn btn-primary btn-sm" title="Cetak Kwitansi">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        `);
                    });
                }
                $('#historyBody').html(rows.join(''));
                    const modal = new bootstrap.Modal(
                    document.getElementById('modalHistory')
                );
                $('#btnCetakHistory').attr('href','/angsuran/angsuran/'+id+'/history/cetak');
                modal.show();
            }
        });
    });
});
</script>
@endsection