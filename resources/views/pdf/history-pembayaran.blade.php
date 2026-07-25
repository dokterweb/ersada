<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">

    <style>
    @page{
        margin:20px;
    }

    body{
        font-family: DejaVu Sans;
        font-size:11px;
        color:#000;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    td{
        padding:4px;
        vertical-align:top;
    }

    th{
        padding:6px;
    }

    .center{
        text-align:center;
    }

    .right{
        text-align:right;
    }

    .bold{
        font-weight:bold;
    }

    .border{
        border:1px solid #000;
    }

    .logo{
        width:110px;
    }

    .title{
        font-size:18px;
        font-weight:bold;
    }

    .small{
        font-size:11px;
    }

    .table-history{
        margin-top:10px;
    }

    .table-history th{
        border:1px solid #000;
        background:#efefef;
    }

    .table-history td{
        border:1px solid #000;
    }

    .ttd{
        height:80px;
    }
    </style>

</head>

<body>

    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}

    <table>
        <tr>
            <td width="18%" class="center">
                <img src="{{ public_path('storage/avatars/logo.jpg') }}" class="logo">
            </td>
            <td width="82%" class="center">
                <div class="title">PT. ERSADA MAKMUR JAYA</div>
                <div class="small">{{ $angsuran->pembiayaan->pengajuan->cabang->alamat }}</div>
            </td>
        </tr>
    </table>
    <hr>
    <h3 class="center">HISTORY PEMBAYARAN ANGSURAN</h3>
    <table>
        <tr>
            <td width="22%">Nama Debitur</td>
            <td width="3%">:</td>
            <td width="35%">{{ $angsuran->pembiayaan->pengajuan->nasabah->nama }}</td>
            <td width="20%">No Pembiayaan</td>
            <td width="3%">:</td>
            <td>{{ $angsuran->pembiayaan->nomor_pembiayaan }}</td>
        </tr>
        <tr>
            <td>Angsuran Ke</td>
            <td>:</td>
            <td>{{ $angsuran->angsuran_ke }}</td>
            <td>Status</td>
            <td>:</td>
            <td>{{ strtoupper($angsuran->status) }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td>{{ now()->translatedFormat('d F Y H:i') }}</td>
            <td>Kasir</td>
            <td>:</td>
            <td>{{ auth()->user()->name }}</td>
        </tr>
    </table>

    {{-- ===================================================== --}}
    {{-- HISTORY --}}
    {{-- ===================================================== --}}

    <table class="table-history">
        <thead>
            <tr>
                <th width="6%">No</th>
                <th width="15%">Tanggal</th>
                <th width="23%">No Bukti</th>
                <th width="15%">Bayar</th>
                <th width="12%">Denda</th>
                <th width="12%">Diskon</th>
                <th width="17%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($angsuran->pembayaranAngsurans as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d-m-Y') }}</td>
                    <td>{{ $item->nomor_pembayaran }}</td>
                    <td class="right">{{ number_format($item->jumlah_bayar,0,',','.') }}</td>
                    <td class="right">{{ number_format($item->denda,0,',','.') }}</td>
                    <td class="right">{{ number_format($item->diskon,0,',','.') }}</td>
                    <td class="right">{{ number_format($item->total_dibayar,0,',','.') }}</td>
                </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Belum ada pembayaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <br>
    {{-- ===================================================== --}}
    {{-- SUMMARY --}}
    {{-- ===================================================== --}}
    <table class="border">
        <tr>
            <td width="70%" class="right bold">Total Tagihan</td>
            <td class="right">Rp {{ number_format($angsuran->total_angsuran,0,',','.') }}</td>
        </tr>
        <tr>
            <td class="right bold">Total Terbayar</td>
            <td class="right">Rp {{ number_format($angsuran->total_terbayar,0,',','.') }}</td>
        </tr>
        <tr>
            <td class="right bold">Sisa Tagihan</td>
            <td class="right">Rp {{ number_format($angsuran->sisa_tagihan,0,',','.') }}</td>
        </tr>
    </table>
    <br><br><br>
    {{-- ===================================================== --}}
    {{-- TTD --}}
    {{-- ===================================================== --}}
    <table>
        <tr>
            <td class="center">Mengetahui</td>
            <td class="center">Dicetak Oleh</td>
        </tr>
        <tr>
            <td class="ttd"></td>
            <td class="ttd"></td>
        </tr>
        <tr>
            <td class="center">(_______________________)</td>
            <td class="center">({{ strtoupper(auth()->user()->name) }})</td>
        </tr>
    </table>
</body>

</html>