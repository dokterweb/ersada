@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <form action="{{ route('murojaahs.store') }}" method="POST" class="card">
                @csrf
                <div class="card-header bg-red-lt">
                    <h3 class="card-title">Input Data</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tgl" id="tgl" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kelompok</label>
                            <select class="form-select" name="kelompok_id" id="kelompok_id">
                                <option value="">Pilih kelompok</option>
                                @foreach ($kelompoks as $p)
                                <option value="{{ $p->id }}" {{ old('kelompok_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_kelompok }}</option>
                                @endforeach
                            </select>
                            @error('kelompok_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama santri</label>
                            <select name="santri_id" id="santri_id" class="form-select" required disabled>
                                <option value="">Pilih Kelompok dulu</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Pilih Surat</label>
                            <select class="form-select" name="surat_no" id="surat_no">
                                <option value="">Pilih Surat</option>
                                    @foreach($surat as $s)
                                    <option value="{{ $s->sura_no }}">{{ $s->sura_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">No. Surat</label>
                            <input type="text" id="no_surat_view" class="form-control" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Juz</label>
                            <input type="text" id="juz_view" class="form-control" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Mulai Hal</label>
                            <input type="text" id="start_page_view" class="form-control" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Akhir Hal</label>
                            <input type="text" id="end_page_view" class="form-control" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Dari Ayat</label>
                            <input type="number" name="dariayat" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sampai Ayat</label>
                            <input type="number" name="sampaiayat" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nilai</label>
                            <select class="form-select" name="keterangan" id="keterangan">
                                <option value="Jayyid Jiddan" {{ old('keterangan') == 'Jayyid Jiddan' ? 'selected' : '' }}>Jayyid Jiddan</option>
                                <option value="Jayyid" {{ old('keterangan') == 'Jayyid' ? 'selected' : '' }}>Jayyid</option>
                                <option value="Maqbul" {{ old('keterangan') == 'Maqbul' ? 'selected' : '' }}>Maqbul</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-end">
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        
        
            <div class="col-lg-12 mt-4" id="historyWrapper" style="display:none;">
                <div class="card">
                    <div class="card-header bg-cyan-lt">
                        <h3 class="card-title">Data Murojaah</h3>
                    </div>
                    <div class="table-responsive p-3">
                        <table id="tableHistory" class="table table-vcenter card-table" style="width:100%;">
                        <thead>
                            <tr>
                            <th>Tanggal</th>
                            <th>Nama Surat</th>
                            <th>Dari Ayat</th>
                            <th>Sampai Ayat</th>
                            <th>Keterangan</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
    
</div>


@endsection

@section('scripts')

{{-- Day.js untuk format tanggal --}}
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

<script>
   $(document).ready(function() {
        // pilih kelompok → load santri
        const $kelompok = $('#kelompok_id');
        const $santri = $('#santri_id');

        $kelompok.on('change', function() {
        $santri.html('<option value="">Memuat…</option>');
        $santri.prop('disabled', true);

        const val = $(this).val();
        if (!val) {
            $santri.html('<option value="">Pilih Kelompok dulu</option>');
            return;
        }

        $.ajax({
            url: `/api/kelompoks/${val}/santris`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $santri.html('<option value="">Pilih santri</option>');
                data.forEach(function(s) {
                    $santri.append(new Option(s.name, s.id));
                });
                $santri.prop('disabled', false);
            },
            error: function() {
                $santri.html('<option value="">Gagal memuat santri</option>');
            }
        });
    });
});

$('#surat_no').on('change', function() {
    const no = $(this).val();
    if (!no) return;

    $.ajax({
        url: `/api/madina/${no}`,
        type: 'GET',
        dataType: 'json',
        success: function(json) {
            if (json.status === 'success') {
                $('#no_surat_view').val(json.data.no_surat || '');
                $('#juz_view').val(json.data.jozz || '');
                $('#start_page_view').val(json.data.start_page || '');
                $('#end_page_view').val(json.data.end_page || '');
            }
        },
        error: function() {
            // Handle error if necessary
        }
    });
});

$(document).ready(function() {
    const santriSelect = $('#santri_id');
    const historyWrapper = $('#historyWrapper');
    let table;  // DataTables instance

    // Ketika santri dipilih, ambil histori murojaah
    santriSelect.on('change', function() {
        const santriId = $(this).val();
        if (!santriId) {
            historyWrapper.hide();
            return;
        }

        $.ajax({
            url: `/api/santris/${santriId}/murojaah`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Format tanggal dan keterangan
                data.forEach(row => {
                    row.tgl_murojaah = dayjs(row.tgl_murojaah).format('DD-MM-YYYY');
                    row.keterangan = row.keterangan ?? '-';
                });

                // Jika DataTables belum diinisialisasi, inisialisasi sekarang
                if (!$.fn.dataTable.isDataTable('#tableHistory')) {
                    table = $('#tableHistory').DataTable({
                        data: data,  // Data langsung dimasukkan
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: false,
                        pageLength: 5,
                        language: {
                            emptyTable: "Belum ada histori tadarus.",
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ baris",
                            paginate: { next: "›", previous: "‹" }
                        },
                        columns: [
                            { data: 'tgl_murojaah' },
                            { data: 'sura_name' },
                            { data: 'dariayat' },
                            { data: 'sampaiayat' },
                            { data: 'keterangan' },
                            {
                                data: 'id', 
                                render: function(data, type, row) {
                                    // Tombol Edit dan Delete
                                    return `
                                        <a href="#" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm btn-delete" data-id="${data}">Delete</button>
                                    `;
                                }
                            }
                        ]
                    });
                } else {
                    // Kalau sudah ada instance DataTables, update data-nya
                    table.clear().rows.add(data).draw();
                }

                historyWrapper.show();
            },
            error: function() {
                table.clear().draw();  // Jika ada error, kosongkan tabel
                historyWrapper.show();
                alert('Gagal memuat histori tadarus.');
            }
        });
    });

});
</script>

@endsection