@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <form action="{{ route('murojaahs.update', $murojaah->id) }}" method="POST" class="card">
                @csrf
                @method('PUT')
                <div class="card-header bg-red-lt">
                    <h3 class="card-title">Input Data</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tgl_murojaah" id="tgl_murojaah" class="form-control" value="{{ $murojaah->tgl_murojaah }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kelompok</label>
                            <select class="form-select" name="kelompok_id" id="kelompok_id">
                                <option value="">Pilih kelompok</option>
                                @foreach($kelompoks as $kelompok)
                                    <option value="{{ $kelompok->id }}" {{ $kelompok->id == $murojaah->santri->kelompok_id ? 'selected' : '' }}>{{ $kelompok->nama_kelompok }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama santri</label>
                            <select name="santri_id" id="santri_id" class="form-select" required readonly>
                                <option value="{{ $murojaah->santri_id }}">{{ $murojaah->santri->user->name }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Pilih Surat</label>
                            <select class="form-select" name="surat_no" id="surat_no">
                                <option value="">Pilih Surat</option>
                                @foreach($surat as $s)
                                    <option value="{{ $s->sura_no }}" {{ $s->sura_no == $murojaah->surat_no ? 'selected' : '' }}>{{ $s->sura_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">No. Surat</label>
                            <input type="text" id="no_surat" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Juz</label>
                            <input type="text" id="jozz" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Mulai Hal</label>
                            <input type="text" id="start_page" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Akhir Hal</label>
                            <input type="text" id="end_page" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Dari Ayat</label>
                            <input type="number" id="dariayat" name="dariayat" class="form-control" value="{{ $murojaah->dariayat }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sampai Ayat</label>
                            <input type="number" id="sampaiayat" name="sampaiayat" class="form-control" value="{{ $murojaah->sampaiayat }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nilai</label>
                            <select class="form-select" name="keterangan" id="keterangan">
                                <option value="Jayyid Jiddan" {{ $murojaah->keterangan == 'Jayyid Jiddan' ? 'selected' : '' }}>Jayyid Jiddan</option>
                                <option value="Jayyid" {{ $murojaah->keterangan == 'Jayyid' ? 'selected' : '' }}>Jayyid</option>
                                <option value="Maqbul" {{ $murojaah->keterangan == 'Maqbul' ? 'selected' : '' }}>Maqbul</option>
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

$(document).ready(function() {
    // Ketika surat dipilih
    $('#surat_no').on('change', function() {
        const suratNo = $(this).val();
        
        if (suratNo) {
            // Ambil data surat dari API
            $.ajax({
                url: `/api/madina/${suratNo}`,  // Endpoint API untuk mengambil detail surat
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update form dengan data surat
                        $('#no_surat').val(response.data.no_surat);
                        $('#jozz').val(response.data.jozz);
                        $('#start_page').val(response.data.start_page);
                        $('#end_page').val(response.data.end_page);
                        $('#suratDetails').show(); // Tampilkan detail surat
                    }
                },
                error: function() {
                    // Reset input jika terjadi error
                    $('#suratDetails').hide();
                    alert('Gagal mengambil data surat.');
                }
            });
        } else {
            // Reset jika tidak ada surat yang dipilih
            $('#suratDetails').hide();
        }
    });
});


$(document).ready(function () {
    const suratSelect = $('#surat_no');

    function loadSuratDetails(sura_no) {
        if (!sura_no) {
            $('#no_surat').val('');
            $('#jozz').val('');
            $('#start_page').val('');
            $('#end_page').val('');
            return;
        }

        $.ajax({
            url: `/api/madina/${sura_no}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const d = response.data;
                    $('#no_surat').val(d.no_surat);
                    $('#jozz').val(d.jozz);
                    $('#start_page').val(d.start_page);
                    $('#end_page').val(d.end_page);
                }
            },
            error: function () {
                console.error('Gagal mengambil data surat');
            }
        });
    }

    // ⬅️ PENTING: saat halaman edit pertama kali dibuka
    const initialSuraNo = suratSelect.val();
    if (initialSuraNo) {
        loadSuratDetails(initialSuraNo);
    }

    // Saat user ganti surat secara manual
    suratSelect.on('change', function () {
        const sura_no = $(this).val();
        loadSuratDetails(sura_no);
    });
});

$(document).ready(function() {
    // Ketika surat dipilih
    $('#surat_no').on('change', function() {
        const suratNo = $(this).val();
        
        if (suratNo) {
            // Ambil data surat dari API
            $.ajax({
                url: `/api/madina/${suratNo}`,  // Endpoint API untuk mengambil detail surat
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update form dengan data surat
                        $('#no_surat').val(response.data.no_surat);
                        $('#jozz').val(response.data.jozz);
                        $('#start_page').val(response.data.start_page);
                        $('#end_page').val(response.data.end_page);
                        $('#suratDetails').show(); // Tampilkan detail surat
                    }
                },
                error: function() {
                    // Reset input jika terjadi error
                    $('#suratDetails').hide();
                    alert('Gagal mengambil data surat.');
                }
            });
        } else {
            // Reset jika tidak ada surat yang dipilih
            $('#suratDetails').hide();
        }
    });
});


$('#editForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize(); // Ambil data form

        $.ajax({
            url: `/murojaahs/${$('#edit_id').val()}/update`, // Ambil ID dari hidden input
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire('Success!', 'Data berhasil diperbarui!', 'success');
                    location.reload();  // Refresh halaman
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function (err) {
                Swal.fire('Error!', 'Gagal mengupdate data.', 'error');
            }
        });
    });


</script>

@endsection