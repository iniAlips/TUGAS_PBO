<?php
declare(strict_types=1);

require_once __DIR__ . '/DataPasien.php';

// Inisialisasi Session PHP (Modul 5 & 6)
session_start();

function aman(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$repo = new DataPasien($_SESSION['pasien_rs'] ?? []);
$error = '';
$pesanSukses = '';

// Mode Edit: Deteksi jika ada parameter GET ?edit=NO_RM
$modeEdit = false;
$editPasien = null;
if (isset($_GET['edit'])) {
    $editPasien = $repo->cari((string)$_GET['edit']);
    if ($editPasien !== null) {
        $modeEdit = true;
    }
}

// Tangani Request POST (Tambah, Ubah, Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string)($_POST['aksi'] ?? '');

    try {
        if ($aksi === 'hapus') {
            $noRmHapus = trim((string)($_POST['no_rm'] ?? ''));
            if (!$repo->hapus($noRmHapus)) {
                throw new InvalidArgumentException('Gagal menghapus data: No RM tidak ditemukan.');
            }
            $_SESSION['pasien_rs'] = $repo->semua();
            header('Location: index.php?status=deleted');
            exit;
        }

        // Ambil Data Identitas Umum (Parent Class)
        $noRm = trim((string)($_POST['no_rm'] ?? ''));
        $nama = trim((string)($_POST['nama'] ?? ''));
        $nik  = trim((string)($_POST['nik'] ?? ''));
        $usia = filter_var($_POST['usia'] ?? null, FILTER_VALIDATE_INT);
        $jk   = (string)($_POST['jenis_kelamin'] ?? '');
        $jenisPelayanan = (string)($_POST['jenis_pelayanan'] ?? '');

        if ($usia === false) {
            throw new InvalidArgumentException('Usia harus berupa angka bulat.');
        }

        // Instansiasi Objek Sesuai Jenis Pelayanan (Child Class)
        if ($jenisPelayanan === 'rawat_inap') {
            $kamar     = (string)($_POST['nomor_kamar'] ?? '');
            $kelas     = (string)($_POST['kelas_kamar'] ?? '');
            $lama      = filter_var($_POST['lama_inap'] ?? null, FILTER_VALIDATE_INT);
            $tarif     = filter_var($_POST['tarif_harian'] ?? null, FILTER_VALIDATE_INT);
            $tindakan  = filter_var($_POST['biaya_tindakan'] ?? null, FILTER_VALIDATE_INT);

            if ($lama === false || $tarif === false || $tindakan === false) {
                throw new InvalidArgumentException('Lama inap, tarif harian, dan biaya tindakan harus bernilai angka.');
            }

            $pasien = new PasienRawatInap($noRm, $nama, $nik, $usia, $jk, $kamar, $kelas, $lama, $tarif, $tindakan);

        } elseif ($jenisPelayanan === 'rawat_jalan') {
            $poli      = (string)($_POST['poli_tujuan'] ?? '');
            $dokter    = (string)($_POST['nama_dokter'] ?? '');
            $periksa   = filter_var($_POST['biaya_pemeriksaan'] ?? null, FILTER_VALIDATE_INT);
            $obat      = filter_var($_POST['biaya_obat'] ?? null, FILTER_VALIDATE_INT);
            $diskon    = filter_var($_POST['diskon_asuransi'] ?? null, FILTER_VALIDATE_INT);

            if ($periksa === false || $obat === false || $diskon === false) {
                throw new InvalidArgumentException('Biaya pemeriksaan, obat, dan diskon asuransi harus bernilai angka.');
            }

            $pasien = new PasienRawatJalan($noRm, $nama, $nik, $usia, $jk, $poli, $dokter, $periksa, $obat, $diskon);

        } else {
            throw new InvalidArgumentException('Jenis pelayanan tidak dikenali.');
        }

        // Eksekusi Simpan atau Ubah pada Repository
        if ($aksi === 'ubah') {
            $noRmLama = (string)($_POST['no_rm_lama'] ?? '');
            $sukses = $repo->ubah($noRmLama, $pasien);
            if (!$sukses) {
                throw new InvalidArgumentException('Gagal mengubah data: No RM lama tidak cocok.');
            }
        } else {
            $sukses = $repo->tambah($pasien);
            if (!$sukses) {
                throw new InvalidArgumentException('Gagal menambah data: No RM duplikat.');
            }
        }

        $_SESSION['pasien_rs'] = $repo->semua();
        header('Location: index.php?status=success');
        exit;

    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pasien Rumah Sakit (PBO PHP)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #2d3748; }
        .container { max-width: 1100px; margin: 0 auto; }
        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 25px; }
        h2, h3 { margin-top: 0; color: #1a202c; }
        .alert-error { background: #fed7d7; color: #9b2c2c; padding: 12px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #e53e3e; }
        .alert-success { background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #38a169; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: bold; margin-bottom: 5px; color: #4a5568; }
        .form-group input, .form-group select { padding: 9px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 0.95rem; }
        .form-group input[readonly] { background-color: #edf2f7; cursor: not-allowed; }
        .section-title { grid-column: 1 / -1; font-weight: bold; margin-top: 10px; padding-bottom: 5px; border-bottom: 2px solid #edf2f7; color: #2b6cb0; }
        .btn { padding: 10px 18px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .btn-primary { background: #3182ce; color: #fff; }
        .btn-secondary { background: #718096; color: #fff; }
        .btn-danger { background: #e53e3e; color: #fff; padding: 6px 10px; font-size: 0.8rem; }
        .btn-warning { background: #dd6b20; color: #fff; padding: 6px 10px; font-size: 0.8rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.9rem; }
        table th, table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        table th { background: #edf2f7; color: #4a5568; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-inap { background: #bee3f8; color: #2c5282; }
        .badge-jalan { background: #feebc8; color: #7b341e; }
        .actions { display: flex; gap: 6px; }
    </style>
    <script>
        function sesuaikanForm() {
            var jenis = document.getElementById("jenis_pelayanan").value;
            var boxInap = document.getElementById("form_rawat_inap");
            var boxJalan = document.getElementById("form_rawat_jalan");

            if (jenis === "rawat_inap") {
                boxInap.style.display = "contents";
                boxJalan.style.display = "none";
            } else {
                boxInap.style.display = "none";
                boxJalan.style.display = "contents";
            }
        }
        window.addEventListener("DOMContentLoaded", sesuaikanForm);
    </script>
</head>
<body>

<div class="container">
    <h2>🏥 Sistem Informasi Layanan Pasien Rumah Sakit</h2>
    <p style="color: #718096;">Implementasi OOP PHP (Inheritance, Enkapsulasi, Repository & Session)</p>

    <?php if ($error !== ''): ?>
        <div class="alert-error"><strong>Peringatan Validasi:</strong> <?= aman($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert-success">Data pasien berhasil diproses dan disimpan ke Session!</div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="alert-success">Data pasien berhasil dihapus!</div>
    <?php endif; ?>

    <!-- Form Tambah / Edit Pasien -->
    <div class="card">
        <h3><?= $modeEdit ? 'Edit Data Pasien (' . aman($editPasien?->getNoRm()) . ')' : 'Pendaftaran Pasien Baru' ?></h3>
        <form method="POST" action="index.php">
            <input type="hidden" name="aksi" value="<?= $modeEdit ? 'ubah' : 'tambah' ?>">
            <?php if ($modeEdit): ?>
                <input type="hidden" name="no_rm_lama" value="<?= aman($editPasien?->getNoRm()) ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="section-title">A. Data Identitas Umum Pasien (Parent Class: Pasien)</div>

                <div class="form-group">
                    <label>No. Rekam Medis (No RM):</label>
                    <input type="text" name="no_rm" value="<?= aman($editPasien?->getNoRm() ?? '') ?>" <?= $modeEdit ? 'readonly' : 'required' ?> placeholder="Contoh: RM-101">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap Pasien:</label>
                    <input type="text" name="nama" value="<?= aman($editPasien?->getNama() ?? '') ?>" required placeholder="Minimal 3 karakter">
                </div>

                <div class="form-group">
                    <label>NIK:</label>
                    <input type="text" name="nik" value="<?= aman($editPasien?->getNik() ?? '') ?>" required placeholder="Minimal 8 digit">
                </div>

                <div class="form-group">
                    <label>Usia (Tahun):</label>
                    <input type="number" name="usia" value="<?= aman($editPasien?->getUsia() ?? '') ?>" required min="0" max="130">
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin:</label>
                    <select name="jenis_kelamin" required>
                        <option value="L" <?= ($editPasien?->getJenisKelamin() === 'L') ? 'selected' : '' ?>>Laki-laki (L)</option>
                        <option value="P" <?= ($editPasien?->getJenisKelamin() === 'P') ? 'selected' : '' ?>>Perempuan (P)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Pelayanan (Child Class):</label>
                    <select name="jenis_pelayanan" id="jenis_pelayanan" onchange="sesuaikanForm()" required>
                        <option value="rawat_inap" <?= ($editPasien instanceof PasienRawatInap) ? 'selected' : '' ?>>Pasien Rawat Inap</option>
                        <option value="rawat_jalan" <?= ($editPasien instanceof PasienRawatJalan) ? 'selected' : '' ?>>Pasien Rawat Jalan</option>
                    </select>
                </div>

                <!-- Bagian Child 1: Rawat Inap -->
                <div id="form_rawat_inap" style="display: contents;">
                    <div class="section-title">B. Data Khusus Rawat Inap (Child Class: PasienRawatInap)</div>

                    <div class="form-group">
                        <label>Nomor Kamar:</label>
                        <input type="text" name="nomor_kamar" value="<?= ($editPasien instanceof PasienRawatInap) ? aman($editPasien->getNomorKamar()) : 'Melati-01' ?>">
                    </div>

                    <div class="form-group">
                        <label>Kelas Kamar:</label>
                        <select name="kelas_kamar">
                            <option value="VIP" <?= ($editPasien instanceof PasienRawatInap && $editPasien->getKelasKamar() === 'VIP') ? 'selected' : '' ?>>VIP</option>
                            <option value="Kelas 1" <?= ($editPasien instanceof PasienRawatInap && $editPasien->getKelasKamar() === 'Kelas 1') ? 'selected' : '' ?>>Kelas 1</option>
                            <option value="Kelas 2" <?= ($editPasien instanceof PasienRawatInap && $editPasien->getKelasKamar() === 'Kelas 2') ? 'selected' : '' ?>>Kelas 2</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Lama Inap (Hari):</label>
                        <input type="number" name="lama_inap" value="<?= ($editPasien instanceof PasienRawatInap) ? aman($editPasien->getLamaInap()) : '1' ?>" min="1">
                    </div>

                    <div class="form-group">
                        <label>Tarif Kamar / Malam (Rp):</label>
                        <input type="number" name="tarif_harian" value="<?= ($editPasien instanceof PasienRawatInap) ? aman($editPasien->getTarifHarian()) : '300000' ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Biaya Tindakan Medis (Rp):</label>
                        <input type="number" name="biaya_tindakan" value="<?= ($editPasien instanceof PasienRawatInap) ? aman($editPasien->getBiayaTindakan()) : '150000' ?>" min="0">
                    </div>
                </div>

                <!-- Bagian Child 2: Rawat Jalan -->
                <div id="form_rawat_jalan" style="display: none;">
                    <div class="section-title">B. Data Khusus Rawat Jalan (Child Class: PasienRawatJalan)</div>

                    <div class="form-group">
                        <label>Poli Tujuan:</label>
                        <input type="text" name="poli_tujuan" value="<?= ($editPasien instanceof PasienRawatJalan) ? aman($editPasien->getPoliTujuan()) : 'Poli Gigi' ?>">
                    </div>

                    <div class="form-group">
                        <label>Dokter Penanggung Jawab:</label>
                        <input type="text" name="nama_dokter" value="<?= ($editPasien instanceof PasienRawatJalan) ? aman($editPasien->getNamaDokter()) : 'drg. Rahmat' ?>">
                    </div>

                    <div class="form-group">
                        <label>Biaya Pemeriksaan (Rp):</label>
                        <input type="number" name="biaya_pemeriksaan" value="<?= ($editPasien instanceof PasienRawatJalan) ? aman($editPasien->getBiayaPemeriksaan()) : '75000' ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Biaya Resep Obat (Rp):</label>
                        <input type="number" name="biaya_obat" value="<?= ($editPasien instanceof PasienRawatJalan) ? aman($editPasien->getBiayaObat()) : '50000' ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Cover Asuransi (% Potongan):</label>
                        <input type="number" name="diskon_asuransi" value="<?= ($editPasien instanceof PasienRawatJalan) ? aman($editPasien->getDiskonAsuransi()) : '20' ?>" min="0" max="100">
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary"><?= $modeEdit ? 'Simpan Perubahan' : 'Daftarkan Pasien' ?></button>
                <?php if ($modeEdit): ?>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel Data Pasien -->
    <div class="card">
        <h3>Daftar Pasien Terdaftar (Polymorphism & Session Storage)</h3>
        <table>
            <thead>
                <tr>
                    <th>No. RM</th>
                    <th>Nama & Usia</th>
                    <th>NIK / Gender</th>
                    <th>Pelayanan</th>
                    <th>Rincian Khusus Objek</th>
                    <th>Total Billing</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($repo->semua())): ?>
                    <tr><td colspan="7" style="text-align: center; color: #a0aec0;">Belum ada data pasien. Silakan tambahkan pasien baru di atas.</td></tr>
                <?php else: ?>
                    <?php foreach ($repo->semua() as $p): ?>
                        <tr>
                            <td><strong><?= aman($p->getNoRm()) ?></strong></td>
                            <td><?= aman($p->getNama()) ?> (<?= aman($p->getUsia()) ?> th)</td>
                            <td><?= aman($p->getNik()) ?> / <?= aman($p->getJenisKelamin()) ?></td>
                            <td>
                                <span class="badge <?= ($p instanceof PasienRawatInap) ? 'badge-inap' : 'badge-jalan' ?>">
                                    <?= aman($p->getJenisPelayanan()) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($p instanceof PasienRawatInap): ?>
                                    Kamar: <?= aman($p->getNomorKamar()) ?> (<?= aman($p->getKelasKamar()) ?>) | Inap: <?= aman($p->getLamaInap()) ?> hari
                                <?php elseif ($p instanceof PasienRawatJalan): ?>
                                    Poli: <?= aman($p->getPoliTujuan()) ?> | Dokter: <?= aman($p->getNamaDokter()) ?> (Diskon: <?= aman($p->getDiskonAsuransi()) ?>%)
                                <?php endif; ?>
                            </td>
                            <td><strong>Rp <?= aman(number_format($p->hitungTotalBiaya(), 0, ',', '.')) ?></strong></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?edit=<?= urlencode($p->getNoRm()) ?>" class="btn btn-warning">Edit</a>
                                    <form method="POST" action="index.php" onsubmit="return confirm('Yakin ingin menghapus pasien ini?');" style="display:inline;">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="no_rm" value="<?= aman($p->getNoRm()) ?>">
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>