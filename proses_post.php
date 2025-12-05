<!DOCTYPE html>
<html>
<head>
    <title>Hasil Input POST</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
    </style>
</head>
<body>

<h2>Data yang Dikirim dengan Metode POST</h2>

<?php
if (isset($_POST['nim'])) {
    echo "NIM : " . $_POST['nim'] . "<br>";
    echo "Nama : " . $_POST['nama'] . "<br>";
    echo "Tempat Lahir : " . $_POST['tempat_lahir'] . "<br>";
    echo "Tanggal Lahir : " . $_POST['tanggal_lahir'] . "<br>";
    echo "Alamat : " . $_POST['alamat'] . "<br>";
    
    echo "Kota : " . $_POST['kota'] . "<br>";
    
    $jk = isset($_POST['jk']) ? $_POST['jk'] : '-';
    echo "Jenis Kelamin : " . $jk . "<br>";
    
    echo "Email : " . $_POST['email'] . "<br>";

    // New Fields Processing
    echo "No HP : " . $_POST['no_hp'] . "<br>";
    echo "Umur : " . $_POST['umur'] . "<br>";
    
    $status = isset($_POST['status']) ? $_POST['status'] : '-';
    echo "Status : " . $status . "<br>";

    echo "Hobi : ";
    if (isset($_POST['hobi'])) {
        $hobi = $_POST['hobi'];
        // Check if it's an array (checkboxes with same name[])
        if (is_array($hobi)) {
            echo implode(", ", $hobi);
        } else {
            echo $hobi;
        }
    } else {
        echo "-";
    }
    echo "<br>";
} else {
    echo "Tidak ada data yang dikirim.";
}
?>

</body>
</html>
