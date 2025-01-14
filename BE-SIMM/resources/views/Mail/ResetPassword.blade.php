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
                                Password Berhasil Diganti
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <!-- Tampilkan data Peserta -->
                            @if(isset($nomor_induk))
                                <h4><strong>Halo, {{$nama_peserta}}</strong></h4>
                            @endif

                            <!-- Tampilkan data Kepala Bagian -->
                            @if(isset($nip_kabag))
                                <h4><strong>Halo, {{$nama_kabag}}</strong></h4>
                            @endif

                            <!-- Tampilkan data Admin -->
                            @if(isset($nama_admin))
                                <h4><strong>Halo, {{$nama_admin}}</strong></h4>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Anda berhasil mengganti password di Sistem Informasi Manajemen Magang.</p>
                            <p>Jika Anda tidak pernah melakukan perubahan ini, segera hubungi admin dari staf Unit Kepegawaian di Kantor Kementerian Agama Kota Surabaya.</p>
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
