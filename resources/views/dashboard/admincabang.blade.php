@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Welcome {{ Auth::user()->name }}
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <div id="real-date" style="font-size: 1.2rem;">
                  {{ date('l, d F Y') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-deck row-cards">
          <div class="col-12">
            <div class="row row-cards">
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-primary text-white avatar">
                          <i class="fas fa-users" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Draft
                        </div>
                        <div class="text-muted">
                          {{$draft}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-success text-white avatar">
                          <i class="fas fa-users" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Menunggu Pimpinan
                        </div>
                        <div class="text-muted">
                          {{$menungguPimpinan}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-danger text-white avatar">
                          <i class="fa-solid fa-plane" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Revisi
                        </div>
                        <div class="text-muted">
                          {{$revisi}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-warning text-white avatar">
                          <i class="fa-solid fa-hand-holding-dollar" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Disetujui
                        </div>
                        <div class="text-muted">
                          {{$disetujui}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
          <div class="col-12">
            <div class="row row-cards">
              <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        Pengajuan Terbaru
                    </h3>
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-primary btn-sm">
                        Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>No Pengajuan</th>
                                <th>Tanggal</th>
                                <th>Nasabah</th>
                                <th class="text-end">Plafond</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuanTerbaru as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->nomor_pengajuan }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_pengajuan)->format('d/m/Y') }}</td>
                                <td>{{ $item->nasabah?->nama }}</td>
                                <td class="text-end">Rp {{ number_format($item->plafond,0,',','.') }}</td>
                               <td>
                                  <span class="badge {{ $item->status_badge['class'] }}">
                                    {{ $item->status_badge['label'] }}
                                  </span>
                               </td>
                                <td>
                                    @if($item->status == 'draft')
                                        <a href="{{ route('pengajuan.reviewFinal',$item->id) }}"
                                           class="btn btn-primary btn-sm">
                                            Lanjut
                                        </a>
                                    @else
                                        {{-- <a href="pengajuan.show" class="btn btn-info btn-sm">Detail</a> --}}
                                        <a href="{{ route('pengajuan.show', $item) }}"
                                          class="btn btn-info btn-sm">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
            
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data pengajuan.
                                </td>
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