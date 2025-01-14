<div>
    <head>
        <meta http-equiv=3D"Content-Type" content=3D"text/html; charset=3DUTF-8">
    </head>

    <body style="font-family: Arial, sans-serif;">
        <div style="display: block; margin: auto; max-width:600px;" class="main">
            <table style="border: 0; width: 100%;">
                <thead>
                    <tr>
                        <td style="text-align: center;">
                            <img src="cid:logo.png" alt="Logo Kemenag" style="max-height: 100px;">
                            <h1 style="margin: 5px 0;">Kementerian Agama Kota Surabaya</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <hr style="border: 0; border-top: 3px solid teal;">
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <h3 style="font-size: 18px; font-weight: bold; margin-top: 20px; text-align: center;">
                                Kode Verifikasi Reset Password
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <!-- Tampilkan data Peserta -->
                            @if(isset($nomor_induk))
                                <p><strong>{{$nama_peserta}}</strong></p>
                                <p><strong>{{$nama_institusi}}</strong></p>
                                <p><strong>{{$jurusan}}</strong></p>
                            @endif

                            <!-- Tampilkan data Kepala Bagian -->
                            @if(isset($nip_kabag))
                                <p><strong>{{$nama_kabag}}</strong></p>
                                <p><strong>{{ $nip_kabag }}</strong></p>
                                <p><strong>{{ $nama_lokasi }}</strong></p>
                            @endif

                            <!-- Tampilkan data Admin -->
                            @if(isset($nama_admin))
                                <p><strong>{{$nama_admin}}</strong></p>
                                <p><strong>{{ $role }}</strong></p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Halo,</p>
                            <p>Pakai kode OTP di bawah ini untuk akunmu.</p>
                            <h4 style="color: blue"><strong>{{$otp_code}}</strong></h4>
                            <p>Kodenya berlaku selama 5 menit.</p>
                            <p>Demi keamanan, jangan berikan kodenya ke siapa pun!</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <style>
            .main {
                background-color: white;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </body>
</div>
