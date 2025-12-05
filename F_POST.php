<!DOCTYPE html>
<html>
<head>
    <title>Form Data (POST)</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            // Validasi Nama
            var nama = document.forms["formMahasiswa"]["nama"].value.trim();
            if (nama === "") {
                alert("Isian Nama tidak boleh kosong!");
                document.forms["formMahasiswa"]["nama"].focus();
                return false;
            }
            
            // Cek apakah nama mengandung angka
            if (/\d/.test(nama)) {
                alert("Isian tidak boleh mengandung angka");
                document.forms["formMahasiswa"]["nama"].focus();
                return false;
            }
            
            // Validasi Umur
            var umur = document.forms["formMahasiswa"]["umur"].value.trim();
            if (umur === "") {
                alert("Isian Umur tidak boleh kosong!");
                document.forms["formMahasiswa"]["umur"].focus();
                return false;
            }
            
            // Cek apakah umur mengandung huruf
            if (/[a-zA-Z]/.test(umur)) {
                alert("Isian tidak boleh mengandung huruf");
                document.forms["formMahasiswa"]["umur"].focus();
                return false;
            }
            
            return true;
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Form Input Data Mahasiswa - POST</h2>
    <form name="formMahasiswa" action="proses_post_sanitasi.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label>NIM :</label> 
            <input type="text" name="nim" class="form-control">
        </div>
        <div class="form-group">
            <label>Nama :</label> 
            <input type="text" name="nama" class="form-control" required>
            <small class="form-hint">Tidak boleh kosong dan tidak boleh mengandung angka</small>
        </div>
        <div class="form-group">
            <label>Tempat Lahir :</label> 
            <input type="text" name="tempat_lahir" class="form-control">
        </div>
        <div class="form-group">
            <label>Tanggal Lahir :</label> 
            <input type="date" name="tanggal_lahir" class="form-control">
        </div>
        <div class="form-group">
            <label>Alamat :</label> 
            <textarea name="alamat" rows="4" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Kota :</label>
            <select name="kota" class="form-control">
                <option>Semarang</option>
                <option>Solo</option>
                <option>Salatiga</option>
                <option>Kudus</option>
                <option>Pekalongan</option>
            </select>
        </div>
        <div class="form-group">
            <label>Jenis Kelamin :</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="jk" value="Laki-laki"> Laki-laki
                </label>
                <label class="radio-label">
                    <input type="radio" name="jk" value="Perempuan"> Perempuan
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Email :</label> 
            <input type="email" name="email" class="form-control">
        </div>
        
        <!-- New Fields -->
        <div class="form-group">
            <label>No HP :</label> 
            <input type="text" name="no_hp" class="form-control">
        </div>
        <div class="form-group">
            <label>Umur :</label> 
            <input type="text" name="umur" class="form-control" required>
            <small class="form-hint">Tidak boleh kosong dan tidak boleh mengandung huruf</small>
        </div>
        <div class="form-group">
            <label>Status :</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="status" value="Kawin"> Kawin
                </label>
                <label class="radio-label">
                    <input type="radio" name="status" value="Belum Kawin"> Belum Kawin
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Hobi :</label>
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="hobi[]" value="Membaca"> Membaca
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="hobi[]" value="Olah Raga"> Olah Raga
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="hobi[]" value="Musik"> Musik
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="hobi[]" value="Traveling"> Traveling
                </label>
            </div>
        </div>

        <div class="form-actions">
            <input type="submit" value="Kirim" class="btn btn-primary">
            <input type="reset" value="Reset" class="btn btn-secondary">
        </div>
    </form>
</div>

</body>
</html>
