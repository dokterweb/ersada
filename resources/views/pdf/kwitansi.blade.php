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

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td {
        vertical-align: middle;
        padding: 2px;
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

    .logo {
        width: 100px;
        height: auto;
    }

    .header-title {
        font-size: 14px;
        font-weight: bold;
        line-height: 1.4;
        }
    .header-title2 {
        font-size: 12px;
        font-weight: bold;
        line-height: 1.4;
        }

    .small {
        font-size: 8px;
        line-height: 1.3;
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
<table class="header-table">
       @php
                $cabang = $pembayaran->angsuran
                    ->pembiayaan
                    ->pengajuan
                    ->cabang;

                $isKisaran = $cabang
                    && (int) $cabang->id === 2;
            @endphp
    <tr>
        <td width="28%" class="center">
            @if ($cabang && (int) $cabang->id === 2)
                <img
                    src="{{ public_path('storage/img/koperasi_ersada.png') }}"
                    class="logo"
                >
            @else
                <img
                    src="{{ public_path('storage/avatars/logo.jpg') }}"
                    class="logo"
                >
            @endif
        </td>

        <td width="72%" class="center">
         

            @if ($isKisaran)
                <div class="header-title">
                    KOPERASI SERBA USAHA<br>
                    "MAKMUR JAYA" UNIT SIMPAN PINJAM
                </div>

                <div class="header-title2">
                    BADAN HUKUM: NO.97/KOP/BH/V/2010, TGL 26 MEI 2010<br>
                    {{ $cabang->alamat ?? '' }}
                </div>
            @else
                <div class="header-title">
                    PT. ERSADA MAKMUR JAYA
                </div>

                <div class="header-title2">
                    {{ $cabang->alamat ?? '' }}
                </div>
            @endif
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
                    @if($pembayaran->diskon_denda > 0)
                        <tr>
                            <td>Diskon Denda</td>
                            <td>:</td>
                            <td>
                                Rp {{ number_format($pembayaran->diskon_denda, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
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
                        <td>Metode</td>
                        <td>:</td>
                        <td>{{ ucfirst($pembayaran->metode) }}</td>
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