<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            Filter Laporan
        </h3>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="row">
                {{-- Tanggal Awal --}}
                <div class="col-md-2">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>

                {{-- Tanggal Akhir --}}
                <div class="col-md-2">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>

                {{-- Cabang --}}
                @if($showCabang)
                <div class="col-md-2">
                    <label class="form-label">Cabang</label>
                    <select name="cabang" class="form-select">
                        <option value="">Semua Cabang</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" @selected(request('cabang')==$cabang->id)>
                                {{ $cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @endif

                {{-- Marketing --}}
                @if($showMarketing)
                <div class="col-md-2">
                    <label class="form-label">Marketing</label>
                    <select name="marketing" class="form-select">
                        <option value="">Semua Marketing</option>
                        @foreach($marketings as $marketing)
                            <option value="{{ $marketing->id }}" @selected(request('marketing')==$marketing->id)>
                                {{ $marketing->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Status --}}
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="draft">Draft</option>
                        <option value="review">Review</option>
                        <option value="akad">Akad</option>
                        <option value="dicairkan">Aktif</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>

                {{-- Keyword --}}

                <div class="col-md-2">
                    <label class="form-label">Keyword</label>
                    <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"placeholder="Nama / No Pembiayaan">
                </div>

            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Cari</button>
                <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route($exportExcelRoute, request()->query()) }}"
                    class="btn btn-success">
                    Export Excel
                </a>
                <a href="{{ route($exportPdfRoute, request()->query()) }}"
                    class="btn btn-danger">
                    Export PDF
                </a>
            </div>
        </form>
    </div>
</div>