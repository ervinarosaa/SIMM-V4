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
                                Informasi Akun
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Selamat,</p>
                            <p>Anda sudah terdaftar sebagai kepala bagian di Kementerian Agama Kota Surabaya.</p>
                            <p>
                                Silahkan login ke Sistem Informasi Manajemen Magang pada URL:
                                <a href="{{ env('FRONTEND_URL') }}">{{ env('FRONTEND_URL') }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Berikut informasi akun Anda:</p>
                            <ul>
                                <li>Nama Lengkap: <strong>{{ $nama_kabag }}</strong></li>
                                <li>NIP: <strong>{{ $nip_kabag }}</strong></li>
                                <li>Lokasi: <strong>{{ $nama_lokasi }}</strong></li>
                                <li>Email: <strong>{{ $email }}</strong></li>
                                <li>Password: <strong>{{ $password }}</strong></li>
                            </ul>
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
