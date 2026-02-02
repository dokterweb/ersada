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
                            <h3 class="card-title">Rekap Absensi</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('absensiustadzs.rekapabsenustadz') }}" method="GET" class="row g-2 mb-3">
                                
                                <div class="col-md-5">
                                    <input type="date" name="start_date" class="form-control" value="{{ $filters['startDate'] ?? '' }}">
                                </div>
                        
                                <div class="col-md-5">
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
                                            <th>Nama Santri</th>
                                            <th>Kelompok</th>
                                            <th>Hadir</th>
                                            <th>Ghoib</th>
                                            <th>Izin</th>
                                            <th>Sakit</th>
                                            <th>Pulang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($rekap as $item)
                                        <tr>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->kelompok->nama_kelompok ?? '-' }}</td>
                                            <td>{{ $item->hadir }}</td>
                                            <td>{{ $item->ghoib }}</td>
                                            <td>{{ $item->izin }}</td>
                                            <td>{{ $item->sakit }}</td>
                                            <td>{{ $item->pulang }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data</td>
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