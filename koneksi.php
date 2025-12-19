<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "universitas"; // ganti sesuai nama database Anda

$koneksi = mysqli_connect($host, $user, $pass, $db);

// cek koneksi
if (!$koneksi) {
    die("Koneksi gagal : " . mysqli_connect_error());
}
?>
