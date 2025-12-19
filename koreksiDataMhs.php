<?php
    include "koneksi.php";
    
    // Ambil ID dari URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        die("ID tidak valid!");
    }
    
    // Ambil data mahasiswa berdasarkan ID
    $sql = "SELECT * FROM mhs WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        die("Data tidak ditemukan!");
    }
    
    $row = $result->fetch_assoc();
    
    // Pisahkan hobi menjadi array
    $hobiArray = !empty($row['hobi']) ? explode(", ", $row['hobi']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
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
            width: 100%;
            max-width: 600px;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
        }

        .header h2 {
            color: #333;
            font-size: 28px;
        }

        .header p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group.full-width label,
        .form-group:first-child label {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .radio-group label,
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
            font-weight: 400;
        }

        input[type="radio"],
        input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .form-group:nth-child(1),
        .form-group:nth-child(2),
        .form-group:nth-child(3) {
            grid-template-columns: 1fr 1fr;
        }

        .radio-group-container,
        .checkbox-group-container {
            grid-column: 1 / -1;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }

        button,
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
            max-width: 200px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button[type="reset"] {
            background: #e0e0e0;
            color: #333;
            flex: 1;
            max-width: 200px;
        }

        button[type="reset"]:hover {
            background: #d0d0d0;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .password-note {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            .form-group {
                grid-template-columns: 1fr;
            }

            .radio-group,
            .checkbox-group {
                flex-direction: column;
                gap: 15px;
            }

            .button-group {
                flex-direction: column;
            }

            button,
            .btn {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✏️ Edit Data Mahasiswa</h2>
            <p>Perbarui data mahasiswa dengan benar</p>
        </div>

        <form action="simpanKoreksiDataMhs.php" method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            
            <!-- Row 1: NIM & Nama -->
            <div class="form-group">
                <div>
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" value="<?= htmlspecialchars($row['nim']) ?>" required>
                </div>
                <div>
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                </div>
            </div>

            <!-- Row 2: Tempat & Tanggal Lahir -->
            <div class="form-group">
                <div>
                    <label for="tempatLahir">Tempat Lahir</label>
                    <input type="text" id="tempatLahir" name="tempatLahir" value="<?= htmlspecialchars($row['tempatLahir']) ?>" required>
                </div>
                <div>
                    <label for="tanggalLahir">Tanggal Lahir</label>
                    <input type="date" id="tanggalLahir" name="tanggalLahir" value="<?= $row['tanggalLahir'] ?>" required>
                </div>
            </div>

            <!-- Row 3: Jumlah Saudara & Kota -->
            <div class="form-group">
                <div>
                    <label for="jmlSaudara">Jumlah Saudara</label>
                    <input type="number" id="jmlSaudara" name="jmlSaudara" value="<?= $row['jmlSaudara'] ?>" required>
                </div>
                <div>
                    <label for="kota">Kota</label>
                    <select id="kota" name="kota" required>
                        <option value="">-- Pilih Kota --</option>
                        <option value="Semarang" <?= $row['kota'] == 'Semarang' ? 'selected' : '' ?>>Semarang</option>
                        <option value="Solo" <?= $row['kota'] == 'Solo' ? 'selected' : '' ?>>Solo</option>
                        <option value="Brebes" <?= $row['kota'] == 'Brebes' ? 'selected' : '' ?>>Brebes</option>
                        <option value="Kudus" <?= $row['kota'] == 'Kudus' ? 'selected' : '' ?>>Kudus</option>
                        <option value="Demak" <?= $row['kota'] == 'Demak' ? 'selected' : '' ?>>Demak</option>
                        <option value="Salatiga" <?= $row['kota'] == 'Salatiga' ? 'selected' : '' ?>>Salatiga</option>
                    </select>
                </div>
            </div>

            <!-- Alamat (Full Width) -->
            <div class="form-group full-width">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" required><?= htmlspecialchars($row['alamat']) ?></textarea>
            </div>

            <!-- Jenis Kelamin (Full Width) -->
            <div class="form-group full-width radio-group-container">
                <label>Jenis Kelamin</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="jenisKelamin" value="L" <?= $row['jenisKelamin'] == 'L' ? 'checked' : '' ?> required> Laki-laki
                    </label>
                    <label>
                        <input type="radio" name="jenisKelamin" value="P" <?= $row['jenisKelamin'] == 'P' ? 'checked' : '' ?> required> Perempuan
                    </label>
                </div>
            </div>

            <!-- Status Keluarga (Full Width) -->
            <div class="form-group full-width radio-group-container">
                <label>Status Keluarga</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="statusKeluarga" value="K" <?= $row['statusKeluarga'] == 'K' ? 'checked' : '' ?> required> Kawin
                    </label>
                    <label>
                        <input type="radio" name="statusKeluarga" value="B" <?= $row['statusKeluarga'] == 'B' ? 'checked' : '' ?> required> Belum Kawin
                    </label>
                </div>
            </div>

            <!-- Hobi (Full Width) -->
            <div class="form-group full-width checkbox-group-container">
                <label>Hobi (Boleh lebih dari satu)</label>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="hobi[]" value="Membaca" <?= in_array('Membaca', $hobiArray) ? 'checked' : '' ?>> Membaca
                    </label>
                    <label>
                        <input type="checkbox" name="hobi[]" value="Olahraga" <?= in_array('Olahraga', $hobiArray) ? 'checked' : '' ?>> Olahraga
                    </label>
                    <label>
                        <input type="checkbox" name="hobi[]" value="Musik" <?= in_array('Musik', $hobiArray) ? 'checked' : '' ?>> Musik
                    </label>
                    <label>
                        <input type="checkbox" name="hobi[]" value="Traveling" <?= in_array('Traveling', $hobiArray) ? 'checked' : '' ?>> Traveling
                    </label>
                </div>
            </div>

            <!-- Email & Password -->
            <div class="form-group">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
                </div>
                <div>
                    <label for="pass">Password Baru (Opsional)</label>
                    <input type="password" id="pass" name="pass" placeholder="Kosongkan jika tidak ingin mengubah">
                    <p class="password-note">Minimal 10 karakter jika ingin mengubah password</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="submit">💾 Update</button>
                <button type="reset">🔄 Reset</button>
            </div>

            <div class="back-link">
                <a href="tampilDataMhs.php">← Kembali ke Data Mahasiswa</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php
    $stmt->close();
    $koneksi->close();
?>
