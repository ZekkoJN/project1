<?php
    include "koneksi.php";
    
    // ambil data dan sanitasi
    function bersih($data){
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    // Ambil ID
    $id              = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        die("ID tidak valid!");
    }
    
    // Ambil data dari form
    $nim             = bersih($_POST['nim'] ?? '');
    $nama            = bersih($_POST['nama'] ?? '');
    $tempatLahir     = bersih($_POST['tempatLahir'] ?? '');
    $xTanggalLahir   = bersih($_POST['tanggalLahir'] ?? '');
    $tanggalLahir    = !empty($xTanggalLahir) ? date("Y-m-d", strtotime($xTanggalLahir)) : '';
    $jmlSaudara      = bersih($_POST['jmlSaudara'] ?? '');
    $alamat          = bersih($_POST['alamat'] ?? '');
    $kota            = bersih($_POST['kota'] ?? '');
    $jenisKelamin    = bersih($_POST['jenisKelamin'] ?? '');  // L / P
    $statusKeluarga  = bersih($_POST['statusKeluarga'] ?? '');  // K / B
    $hobi            = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";
    $email           = bersih($_POST['email'] ?? '');
    $xRawPassword    = bersih($_POST['pass'] ?? '');
    
    // Cek apakah password diubah
    if (!empty($xRawPassword)) {
        // Validasi password baru
        if (strlen($xRawPassword) < 10) {
            die("Password minimal 10 karakter.");
        }
        
        // Hashing password baru
        $hashedPassword = password_hash($xRawPassword, PASSWORD_BCRYPT);
        
        // Update dengan password baru
        $sql = "UPDATE mhs SET 
                nim = ?, 
                nama = ?, 
                tempatLahir = ?, 
                tanggalLahir = ?, 
                jmlSaudara = ?, 
                alamat = ?, 
                kota = ?, 
                jenisKelamin = ?, 
                statusKeluarga = ?, 
                hobi = ?, 
                email = ?, 
                pass = ? 
                WHERE id = ?";
        
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssssssssssssi", 
            $nim, 
            $nama, 
            $tempatLahir, 
            $tanggalLahir, 
            $jmlSaudara, 
            $alamat, 
            $kota, 
            $jenisKelamin, 
            $statusKeluarga, 
            $hobi, 
            $email, 
            $hashedPassword, 
            $id
        );
    } else {
        // Update tanpa mengubah password
        $sql = "UPDATE mhs SET 
                nim = ?, 
                nama = ?, 
                tempatLahir = ?, 
                tanggalLahir = ?, 
                jmlSaudara = ?, 
                alamat = ?, 
                kota = ?, 
                jenisKelamin = ?, 
                statusKeluarga = ?, 
                hobi = ?, 
                email = ? 
                WHERE id = ?";
        
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssssssssssi", 
            $nim, 
            $nama, 
            $tempatLahir, 
            $tanggalLahir, 
            $jmlSaudara, 
            $alamat, 
            $kota, 
            $jenisKelamin, 
            $statusKeluarga, 
            $hobi, 
            $email, 
            $id
        );
    }
    
    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Update Berhasil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            text-align: center;
            max-width: 500px;
        }
        .success-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success-icon'>✅</div>
        <h2>Data Berhasil Diperbarui!</h2>
        <p>Data mahasiswa telah berhasil diupdate dalam database.</p>
        <a href='tampilDataMhs.php' class='btn'>Lihat Data Mahasiswa</a>
    </div>
</body>
</html>";
    } else {
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Update Gagal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            text-align: center;
            max-width: 500px;
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h2 {
            color: #d32f2f;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='error-icon'>❌</div>
        <h2>Update Gagal!</h2>
        <p>Error: " . mysqli_error($koneksi) . "</p>
        <a href='tampilDataMhs.php' class='btn'>Kembali</a>
    </div>
</body>
</html>";
    }
    
    $stmt->close();
    $koneksi->close();
?>
