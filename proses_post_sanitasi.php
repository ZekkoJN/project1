<!DOCTYPE html>
<html>
<head>
    <title>Hasil Input POST (Sanitasi)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Data yang Dikirim dengan Metode POST (Sanitasi)</h2>

    <?php
    if (isset($_POST['nim'])) {
        // Sanitasi data menggunakan htmlspecialchars dan filter_var
        $nim = htmlspecialchars($_POST['nim'], ENT_QUOTES, 'UTF-8');
        $nama = htmlspecialchars($_POST['nama'], ENT_QUOTES, 'UTF-8');
        $tempat_lahir = htmlspecialchars($_POST['tempat_lahir'], ENT_QUOTES, 'UTF-8');
        $tanggal_lahir = htmlspecialchars($_POST['tanggal_lahir'], ENT_QUOTES, 'UTF-8');
        $alamat = htmlspecialchars($_POST['alamat'], ENT_QUOTES, 'UTF-8');
        $kota = htmlspecialchars($_POST['kota'], ENT_QUOTES, 'UTF-8');
        
        // Sanitasi jenis kelamin
        $jk = isset($_POST['jk']) ? htmlspecialchars($_POST['jk'], ENT_QUOTES, 'UTF-8') : '-';
        
        // Sanitasi dan validasi email
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $email_valid = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        // Sanitasi no HP (hanya angka dan karakter tertentu)
        $no_hp = preg_replace('/[^0-9+\-]/', '', $_POST['no_hp']);
        
        // Sanitasi umur (hanya angka)
        $umur = filter_var($_POST['umur'], FILTER_SANITIZE_NUMBER_INT);
        
        // Sanitasi status
        $status = isset($_POST['status']) ? htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8') : '-';
        
        // Sanitasi hobi (array)
        $hobi_display = "-";
        if (isset($_POST['hobi']) && is_array($_POST['hobi'])) {
            $hobi_sanitized = array_map(function($item) {
                return htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
            }, $_POST['hobi']);
            $hobi_display = implode(", ", $hobi_sanitized);
        }
        
        // Tampilkan data yang sudah disanitasi
        echo "<div class='result-box'>";
        echo "<div class='result-item'><strong>NIM:</strong> " . $nim . "</div>";
        echo "<div class='result-item'><strong>Nama:</strong> " . $nama . "</div>";
        echo "<div class='result-item'><strong>Tempat Lahir:</strong> " . $tempat_lahir . "</div>";
        echo "<div class='result-item'><strong>Tanggal Lahir:</strong> " . $tanggal_lahir . "</div>";
        echo "<div class='result-item'><strong>Alamat:</strong> " . $alamat . "</div>";
        echo "<div class='result-item'><strong>Kota:</strong> " . $kota . "</div>";
        echo "<div class='result-item'><strong>Jenis Kelamin:</strong> " . $jk . "</div>";
        echo "<div class='result-item'><strong>Email:</strong> " . $email;
        if (!$email_valid) {
            echo " <span class='error'>(Email tidak valid)</span>";
        }
        echo "</div>";
        echo "<div class='result-item'><strong>No HP:</strong> " . $no_hp . "</div>";
        echo "<div class='result-item'><strong>Umur:</strong> " . $umur . "</div>";
        echo "<div class='result-item'><strong>Status:</strong> " . $status . "</div>";
        echo "<div class='result-item'><strong>Hobi:</strong> " . $hobi_display . "</div>";
        echo "</div>";
        
        echo "<div class='back-link'>";
        echo "<a href='F_POST.php'>← Kembali ke Form</a>";
        echo "</div>";
    } else {
        echo "<div class='error-box'>Tidak ada data yang dikirim.</div>";
        echo "<div class='back-link'>";
        echo "<a href='F_POST.php'>← Kembali ke Form</a>";
        echo "</div>";
    }
    ?>

</div>

</body>
</html>
