@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-cyan-lt">
                            <h3 class="card-title">Rekap Murojaah</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('murojaahs.rekap') }}" method="GET" class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <input type="text" name="nama_santri" class="form-control"
                                           placeholder="Cari siswa"
                                           value="{{ request('nama_santri') }}">
                                </div>
                        
                                <div class="col-md-3">
                                    <select name="kelompok_id" class="form-control">
                                        <option value="">Semua Kelompok</option>
                                        @foreach($kelompoks as $k)
                                            <option value="{{ $k->id }}" {{ request('kelompok_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelompok }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="start_date" class="form-control" value="{{ $filters['startDate'] ?? '' }}">
                                </div>
                        
                                <div class="col-md-2">
                                    <input type="date" name="end_date" class="form-control" value="{{ $filters['endDate'] ?? '' }}">
                                </div>
                        
                                <div class="col-md-2 d-flex gap-2">
                                    <button class="btn btn-success flex-fill">Filter</button>
                                </div>
                            </form>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Santri</th>
                                            <th>Kelas</th>
                                            <th>Surat</th>
                                            <th>Dari Ayat</th>
                                            <th>Sampai Ayat</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->murojaah)->format('d-m-Y') }}</td>
                                            <td>{{ $item->santri->user->name?? '-' }}</td>
                                            <td>{{ $item->santri->kelasnya->nama_kelas ?? '-' }}</td>
                                            <td>{{ $item->surat->sura_name ?? '-' }}</td>
                                            <td>{{ $item->dariayat }}</td>
                                            <td>{{ $item->sampaiayat }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
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


@endsection

@section('scripts')

<!-- SweetAlert2 Script -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500
    });
</script>
@elseif (session('error'))
   <script>
       Swal.fire({
           icon: 'error',
           title: 'Oops...',
           text: "{{ session('error') }}",
           position: 'top-end',
           showConfirmButton: false,
           timer: 1500
       });
   </script>
@endif

<script>
       $(document).ready(function() {
        // Inisialisasi DataTables
        $('#mytable').DataTable({
            "processing": true,   // Menampilkan loading saat memproses data
            "serverSide": false,  // Tentukan apakah menggunakan server-side processing
            "paging": true,       // Menampilkan pagination
            "lengthChange": false // Menonaktifkan pengaturan jumlah baris per halaman
        });
    });
</script>
@endsection