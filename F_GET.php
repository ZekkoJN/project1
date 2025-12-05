<!DOCTYPE html>
<html>
<head>
    <title>Form Data (GET)</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 120px; vertical-align: top; }
    </style>
</head>
<body>

<h2>Form Input Data Mahasiswa - GET</h2>
<form action="proses_get.php" method="GET">
    <div class="form-group">
        <label>NIM :</label> 
        <input type="text" name="nim">
    </div>
    <div class="form-group">
        <label>Nama :</label> 
        <input type="text" name="nama">
    </div>
    <div class="form-group">
        <label>Tempat Lahir :</label> 
        <input type="text" name="tempat_lahir">
    </div>
    <div class="form-group">
        <label>Tanggal Lahir :</label> 
        <input type="date" name="tanggal_lahir">
    </div>
    <div class="form-group">
        <label>Alamat :</label> 
        <textarea name="alamat" rows="4" cols="30"></textarea>
    </div>
    <div class="form-group">
        <label>Kota :</label>
        <select name="kota">
            <option>Semarang</option>
            <option>Solo</option>
            <option>Salatiga</option>
            <option>Kudus</option>
            <option>Pekalongan</option>
        </select>
    </div>
    <div class="form-group">
        <label>Jenis Kelamin :</label>
        <input type="radio" name="jk" value="Laki-laki"> Laki-laki
        <input type="radio" name="jk" value="Perempuan"> Perempuan
    </div>
    <div class="form-group">
        <label>Email :</label> 
        <input type="email" name="email">
    </div>
    
    <!-- New Fields -->
    <div class="form-group">
        <label>No HP :</label> 
        <input type="text" name="no_hp">
    </div>
    <div class="form-group">
        <label>Umur :</label> 
        <input type="number" name="umur">
    </div>
    <div class="form-group">
        <label>Status :</label>
        <input type="radio" name="status" value="Kawin"> Kawin
        <input type="radio" name="status" value="Belum Kawin"> Belum Kawin
    </div>
    <div class="form-group">
        <label>Hobi :</label>
        <input type="checkbox" name="hobi[]" value="Membaca"> Membaca
        <input type="checkbox" name="hobi[]" value="Olah Raga"> Olah Raga
        <input type="checkbox" name="hobi[]" value="Musik"> Musik
        <input type="checkbox" name="hobi[]" value="Traveling"> Traveling
    </div>

    <input type="submit" value="Kirim">
</form>

</body>
</html>
