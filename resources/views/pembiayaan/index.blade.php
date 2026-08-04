@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
   
            <div class="card">
                
                <div class="card-header bg-success">
                    <h3 class="card-title">Data Pembiayaan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-vcenter table-mobile-md card-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>No Pengajuan</th>
                                <th>Nama Debitur</th>
                                <th>Marketing</th>
                                <th>Cabang</th>
                                <th>Status</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuans as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_pengajuan }}</td>
                                <td>{{ optional($item->nasabah)->nama }}</td>
                                <td>{{ optional($item->marketing)->user->name }}</td>
                                <td>{{ optional($item->cabang)->nama_cabang }}</td>
                            
                                {{-- STATUS PEMBIAYAAN --}}
                                <td>
                                    @if(!$item->pembiayaan)
                                        <span class="badge bg-secondary">Belum Dibuat</span>
                            
                                    @elseif($item->pembiayaan->status=='draft')
                                        <span class="badge bg-warning text-dark">Draft</span>
                            
                                    @elseif($item->pembiayaan->status=='jadwal_generated')
                                        <span class="badge bg-info">Jadwal Generated</span>
                            
                                    @elseif($item->pembiayaan->status=='akad')
                                        <span class="badge bg-primary">Akad Dibuat</span>
                            
                                    @elseif($item->pembiayaan->status=='signed')
                                        <span class="badge bg-success">Akad Ditandatangani</span>
                            
                                    @elseif($item->pembiayaan->status=='dicairkan')
                                        <span class="badge bg-success">Dicairkan</span>
                            
                                    @elseif($item->pembiayaan->status=='lunas')
                                        <span class="badge bg-dark">Lunas</span>
                            
                                    @else
                                        <span class="badge bg-danger">
                                            {{ ucfirst($item->pembiayaan->status) }}
                                        </span>
                                    @endif
                                </td>
                            
                                {{-- ACTION --}}
                                <td width="220">
                                    @if(!$item->pembiayaan)
                                        <a href="{{ route('pembiayaan.create',$item) }}" class="btn btn-primary btn-sm">
                                            <i class="ti ti-plus"></i>
                                            Buat Pembiayaan
                                        </a>
                                    @else
                                        @php
                                            $status = $item->pembiayaan->status;
                                        @endphp
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="ti ti-settings"></i>
                                                Action
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                {{-- DRAFT --}}
                                                @if($status=='draft')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('pembiayaan.edit',$item->pembiayaan) }}">
                                                            <i class="ti ti-edit me-2"></i>
                                                            Edit Pembiayaan
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('pembiayaan.review',$item->pembiayaan) }}">
                                                            <i class="ti ti-eye me-2"></i>
                                                            Review Pembiayaan
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('pembiayaan.generateJadwal',$item->pembiayaan) }}">
                                                            <i class="ti ti-calendar me-2"></i>
                                                            Generate Jadwal
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- JADWAL GENERATED --}}
                                                @if($status=='jadwal_generated')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('pembiayaan.jadwal',$item->pembiayaan) }}">
                                                            <i class="ti ti-calendar-event me-2"></i>
                                                            Lihat Jadwal
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('akad.create',$item->pembiayaan) }}">
                                                            <i class="ti ti-file-text me-2"></i>
                                                            Proses Akad
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- AKAD / SIGNED / DICAIRKAN / LUNAS --}}
                                                @if(in_array($status,['akad','signed','dicairkan','lunas']))
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('akad.show',$item->pembiayaan->akad) }}">
                                                            <i class="ti ti-file-description me-2"></i>
                                                            Detail Akad
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- SIGNED --}}
                                                @if($status=='signed')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('pencairan.create',$item->pembiayaan) }}">
                                                            <i class="ti ti-cash me-2"></i>
                                                            Pencairan
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- DICAIRKAN --}}
                                                @if($status=='dicairkan')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('angsuran.index',$item->pembiayaan) }}">
                                                            <i class="ti ti-report-money me-2"></i>
                                                            Lihat Angsuran
                                                        </a>
                                                    </li>
                                                @endif
                                                {{-- LUNAS --}}
                                                @if($status=='lunas')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('pelunasan.show',$item->pembiayaan->pelunasan) }}">
                                                            <i class="ti ti-check me-2"></i>
                                                            Detail Pelunasan
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Tidak ada data.
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
@endsection