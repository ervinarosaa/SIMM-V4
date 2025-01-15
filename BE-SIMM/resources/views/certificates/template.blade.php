<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $peserta->nama_peserta }}</title>
    <style>
        html, body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f9f9f9;
        }
        .certificate {
            position: relative;
            display: flex;
            justify-content: center;
            align-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            text-align: center;
            background-image: url({{ public_path('template/bg-sertifikat.png') }}); 
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .header {
            font-size: 14px;
            line-height: 1.4;
        }
        .logo img {
            width: 70px; 
            height: auto;
            margin-top: 50px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 38px;
            font-weight: bold;
            margin-top: 10px;
        }
        .subtitle {
            font-size: 16px;
            margin-bottom: 3px;
        }
        .recipient {
            font-size: 35px;
            font-weight: bold;
            color: goldenrod;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .recipient.small-font {
            font-size: 28px; 
        }
        .details {
            font-size: 16px;
            margin: 20px 0;
            line-height: 1.6;
        }.details-peserta {
            font-size: 14px;
            margin: 5px 0;
            line-height: 1.6;
        }
        .footer {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-top: 40px;
        }
        .footer-table {
            width: 50%;
            margin: 0 auto;
            /* border: 2px solid black; */
        }
        .footer-table td {
            vertical-align: top;
            padding: 10px;
        }
        .footer-table img {
            width: 120px;
            height: 160px;
            object-fit: cover;
            margin-left: 30px;
        }
        .signature {
            text-align: left;
            font-size: 16px;
            margin-left: 50px;
        }
        .signature-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="logo">
                <img src="{{ public_path('template/logo.png') }}" alt="Logo Kemenag">
            </div>
            KEMENTERIAN AGAMA REPUBLIK INDONESIA<br>
            KANTOR KEMENTERIAN AGAMA KOTA SURABAYA
        </div>

        <div class="title">SERTIFIKAT MAGANG</div>
        <div class="subtitle">{{ $sertifikat->nomor_sertifikat ?? 'Nomor sertifikat tidak tersedia' }}</div>

        <div class="details">Diberikan kepada:</div>
        <div class="recipient {{ strlen($peserta->nama_peserta) > 31 ? 'small-font' : '' }}">
            {{ $peserta->nama_peserta }}
        </div>
        <hr style="width: 60%; margin: 0 auto;">
        <div class="details-peserta">
            @if($peserta->institusi->tingkat_pendidikan === "Perguruan Tinggi")
                NIS : {{ $peserta->nomor_induk }},
                Fakultas : {{ $peserta->fakultas }},
            @else
                NIM : {{ $peserta->nomor_induk }},
            @endif

            Jurusan : {{ $peserta->jurusan }}<br>
            {{ $peserta->institusi->nama_institusi }}<br>
        </div>

        <div class="details">
            Telah menyelesaikan program magang di Kantor Kementerian Agama Kota Surabaya terhitung mulai<br>
            dari {{ \Carbon\Carbon::parse($peserta->tanggal_mulai)->locale('id')->isoFormat('D MMMM YYYY') }} sampai dengan {{ \Carbon\Carbon::parse($peserta->tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY') }}
            dengan predikat "{{ $peserta->nilai->predikat_nilai }}"
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td style="text-align: center;">
                        <img src="{{ $fotoUrl }}" alt="Foto Peserta">
                    </td>
                    <td style="text-align: left; padding-left: 70px;">
                        <div class="signature">
                            Surabaya, {{ \Carbon\Carbon::parse($sertifikat->tanggal_penandatangan)->locale('id')->isoFormat('D MMMM YYYY') }}<br>
                            {{ $sertifikat->jabatan_penandatangan }},<br><br><br><br><br>
                            <div class="signature-name">{{ $sertifikat->nama_penandatangan }}</div>
                            <div>NIP. {{ $sertifikat->nip_penandatangan }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
