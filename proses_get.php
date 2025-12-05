<!DOCTYPE html>
<html>
<head>
    <title>Hasil Input GET</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
    </style>
</head>
<body>

<h2>Data yang Dikirim dengan Metode GET</h2>

<?php
if (isset($_GET['nim'])) {
    echo "NIM : " . $_GET['nim'] . "<br>";
    echo "Nama : " . $_GET['nama'] . "<br>";
    echo "Tempat Lahir : " . $_GET['tempat_lahir'] . "<br>";
    echo "Tanggal Lahir : " . $_GET['tanggal_lahir'] . "<br>";
    echo "Alamat : " . $_GET['alamat'] . "<br>";
    
    echo "Kota : " . $_GET['kota'] . "<br>";
    
    $jk = isset($_GET['jk']) ? $_GET['jk'] : '-';
    echo "Jenis Kelamin : " . $jk . "<br>";
    
    echo "Email : " . $_GET['email'] . "<br>";

    // New Fields Processing
    echo "No HP : " . $_GET['no_hp'] . "<br>";
    echo "Umur : " . $_GET['umur'] . "<br>";
    
    $status = isset($_GET['status']) ? $_GET['status'] : '-';
    echo "Status : " . $status . "<br>";

    echo "Hobi : ";
    if (isset($_GET['hobi'])) {
        $hobi = $_GET['hobi'];
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
