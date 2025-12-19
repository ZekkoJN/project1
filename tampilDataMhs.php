<?php
    include "koneksi.php";
    $data = mysqli_query($koneksi, "SELECT * FROM mhs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
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
            padding: 30px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h2 {
            color: #333;
            font-size: 28px;
            flex: 1;
            min-width: 300px;
        }

        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: sticky;
            top: 0;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }

        tbody tr {
            transition: background-color 0.3s;
        }

        tbody tr:hover {
            background-color: #f5f7ff;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(even):hover {
            background-color: #f5f7ff;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-kawin {
            background-color: #d4edda;
            color: #155724;
        }

        .status-belum {
            background-color: #fff3cd;
            color: #856404;
        }

        .gender-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .gender-L {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .gender-P {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-action {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 3px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
        }

        .btn-edit:hover {
            background-color: #ffc107;
            transform: scale(1.1);
        }

        .btn-delete {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
        }

        .btn-delete:hover {
            background-color: #dc3545;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
            }

            .header h2 {
                min-width: auto;
            }

            .btn-add {
                width: 100%;
                text-align: center;
            }

            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }

            .table-wrapper {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📊 Daftar Data Mahasiswa</h2>
            <a href="tambahDataMhs.php" class="btn-add">➕ Tambah Data Baru</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Jml Sdr</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>JK</th>
                        <th>Status</th>
                        <th>Hobi</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><strong><?= $row['id'] ?? '' ?></strong></td>
                            <td><?= $row['nim'] ?? '' ?></td>
                            <td><?= $row['nama'] ?? '' ?></td>
                            <td><?= $row['tempatLahir'] ?? '' ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggalLahir'] ?? '')) ?></td>
                            <td><?= $row['jmlSaudara'] ?? '' ?></td>
                            <td><?= $row['alamat'] ?? '' ?></td>
                            <td><?= $row['kota'] ?? '' ?></td>
                            <td>
                                <span class="gender-badge gender-<?= $row['jenisKelamin'] ?? '' ?>">
                                    <?= $row['jenisKelamin'] == 'L' ? '♂️ Laki-laki' : '♀️ Perempuan' ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= ($row['statusKeluarga'] ?? '') == 'K' ? 'status-kawin' : 'status-belum' ?>">
                                    <?= $row['statusKeluarga'] == 'K' ? '✓ Kawin' : '✕ Belum Kawin' ?>
                                </span>
                            </td>
                            <td><?= $row['hobi'] ?? '-' ?></td>
                            <td><?= $row['email'] ?? '' ?></td>
                            <td><code><?= substr($row['pass'] ?? '', 0, 10) ?>...</code></td>
                            <td>
                                <a href="koreksiDataMhs.php?id=<?= $row['id'] ?>" class="btn-action btn-edit" title="Edit">✏️</a>
                                <a href="hapusDataMhs.php?id=<?= $row['id'] ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini?')">🗑️</a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="14" class="no-data">Tidak ada data mahasiswa</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Total Data: <strong><?= mysqli_num_rows($data) ?></strong> Mahasiswa</p>
        </div>
    </div>
</body>
</html>

<?php
    mysqli_close($koneksi);
?>
