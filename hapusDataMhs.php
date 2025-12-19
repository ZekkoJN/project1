<?php
    include "koneksi.php";
    
    // Ambil ID dari URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        die("ID tidak valid!");
    }
    
    // Hapus data mahasiswa berdasarkan ID
    $sql = "DELETE FROM mhs WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Hapus Berhasil</title>
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
        <div class='success-icon'>🗑️</div>
        <h2>Data Berhasil Dihapus!</h2>
        <p>Data mahasiswa telah berhasil dihapus dari database.</p>
        <a href='tampilDataMhs.php' class='btn'>Kembali ke Data Mahasiswa</a>
    </div>
</body>
</html>";
    } else {
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Hapus Gagal</title>
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
        <h2>Hapus Gagal!</h2>
        <p>Error: " . mysqli_error($koneksi) . "</p>
        <a href='tampilDataMhs.php' class='btn'>Kembali</a>
    </div>
</body>
</html>";
    }
    
    $stmt->close();
    $koneksi->close();
?>
