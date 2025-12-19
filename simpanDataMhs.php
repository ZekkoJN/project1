<?php
    include "koneksi.php";
    
    // ambil data dan sanitasi
    function bersih($data){
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    //ambil password asli dari input form
    $nim             = bersih($_POST['nim']?? '');
    $nama            = bersih($_POST['nama']?? '');
    $tempatLahir     = bersih($_POST['tempatLahir']?? '');
    $xTanggalLahir   = bersih($_POST['tanggalLahir']?? '');
    $tanggalLahir    = !empty($xTanggalLahir) ? date("Y-m-d", strtotime($xTanggalLahir)) : '';
    $jmlSaudara      = bersih($_POST['jmlSaudara']?? '');
    $alamat          = bersih($_POST['alamat']?? '');
    $kota            = bersih($_POST['kota']?? '');
    $jenisKelamin    = bersih($_POST['jenisKelamin']?? '');  // L / P
    $statusKeluarga  = bersih($_POST['statusKeluarga']?? '');  // K / B
    $hobi            = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";
    $email           = bersih($_POST['email']?? '');
    $xRawPassword    = bersih($_POST['pass']?? ''); // 
    
    //Lakukan validasi pada password asli (panjang, kompleksitas, dll.)
    if (empty($xRawPassword) || strlen($xRawPassword) < 10 ) {
        die("Password minimal 10 karakter.");
    }
    
    // Hashing password menggunakan password_hash()
    $hashedPassword = password_hash($xRawPassword, PASSWORD_BCRYPT);
    $sql1 = "INSERT INTO mhs (nim,nama,tempatLahir,tanggalLahir,jmlSaudara,alamat,kota,jenisKelamin,statusKeluarga,hobi,email,pass) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    
    $stmt=$koneksi->prepare($sql1);
    $stmt->bind_param("ssssssssssss", $nim, $nama, $tempatLahir, $tanggalLahir, $jmlSaudara, $alamat, $kota, $jenisKelamin, $statusKeluarga, $hobi, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "Data berhasil disimpan! <br>";
        echo "<a href='tampilDataMhs.php'>Lihat Data</a>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
    
    $koneksi->close();
    $stmt->close();
?>
