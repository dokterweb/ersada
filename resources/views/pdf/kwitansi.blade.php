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
        font-size:12px;
        color:#000;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    .border{
        border:1px solid #000;
    }

    td{
        padding:5px;
        vertical-align:top;
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

    .logo{
        width:120px;
    }

    .header-title{
        font-size:20px;
        font-weight:bold;
    }

    .small{
        font-size:11px;
    }

    .spacer{
        height:12px;
    }

    .ttd{
        height:90px;
    }

    </style>
</head>

<body>
    {{-- ================= HEADER ================= --}}
    <table>
        <tr>
            <td width="22%" class="center">
                <img src="{{ public_path('storage/avatars/logo.jpg') }}" class="logo">
            </td>
            <td width="78%" class="center">
                <div class="header-title">PT. ERSADA MAKMUR JAYA</div>
                <div class="small">{{ $pembayaran->angsuran->pembiayaan->pengajuan->cabang->alamat }}</div>
            </td>
        </tr>
    </table>
    <div class="spacer"></div>
    {{-- ================= REGISTER ================= --}}
    <table class="border">
        <tr>
            <td width="50%">No. Register :<b>{{ $pembayaran->nomor_pembayaran }}</b></td>
            <td>
                Jangka Waktu :
                <b>{{ $pembayaran->angsuran->pembiayaan->tenor }}Bulan</b>
            </td>
        </tr>
    </table>
    <div class="spacer"></div>
    <div class="center bold" style="font-size:16px;">
        TANDA TERIMA ANGSURAN PINJAMAN
    </div>
    <div class="spacer"></div>
    {{-- ================= ISI ================= --}}
    <table class="border">
        <tr>
            <td width="55%">
                <table>
                    <tr>
                        <td width="45%">Nama Peminjam</td>
                        <td width="5%">:</td>
                        <td>{{ $pembayaran->angsuran->pembiayaan->pengajuan->nasabah->nama }}</td>
                    </tr>
                    <tr>
                        <td>Besar Angsuran</td>
                        <td>:</td>
                        <td>Rp {{ number_format($pembayaran->angsuran->total_angsuran,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Denda</td>
                        <td>:</td>
                        <td>Rp {{ number_format($pembayaran->denda,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Bayar</td>
                        <td>:</td>
                        <td><b>Rp {{ number_format($pembayaran->total_dibayar,0,',','.') }}</b></td>
                    </tr>
                    <tr>
                        <td>Angsuran Ke</td>
                        <td>:</td>
                        <td>{{ $pembayaran->angsuran->angsuran_ke }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Bayar</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Metode</td>
                        <td>:</td>
                        <td>{{ ucfirst($pembayaran->metode) }}</td>
                    </tr>
                </table>
            </td>
            <td width="45%">
                <table>
                    <tr>
                        <td width="45%">Besar Pinjaman</td>
                        <td width="5%">:</td>
                        <td>Rp {{ number_format($pembayaran->angsuran->pembiayaan->plafond,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pinjaman</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($pembayaran->angsuran->pembiayaan->tanggal_akad)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>No Pembiayaan</td>
                        <td>:</td>
                        <td>{{ $pembayaran->angsuran->pembiayaan->nomor_pembiayaan }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ strtoupper($pembayaran->angsuran->status) }}</td>
                    </tr>
                    <tr>
                        <td>Kasir</td>
                        <td>:</td>
                        <td>{{ $pembayaran->creator->name }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="height:40px;"></div>

    {{-- ================= TTD ================= --}}
    <table>
        <tr>
            <td class="center">Peminjam</td>
            <td class="center">PT. ERSADA MAKMUR JAYA</td>
        </tr>
        <tr>
            <td class="ttd"></td>
            <td class="ttd"></td>
        </tr>
        <tr>
            <td class="center">( _______________________ )</td>
            <td class="center">( {{ strtoupper($pembayaran->creator->name) }} )</td>
        </tr>
    </table>

</body>

</html>