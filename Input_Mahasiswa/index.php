<?php
declare(strict_types=1);
require_once __DIR__ . '/DataMahasiswa.php'; // class dimuat sebelum session_start
session_start();

function aman(mixed $teks): string
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

/** Membuat object Mahasiswa dari input form. Semester divalidasi dengan filter_var. */
function dariForm(string $nim): Mahasiswa
{
    $semester = filter_var($_POST['semester'] ?? '', FILTER_VALIDATE_INT);
    if ($semester === false) {
        throw new InvalidArgumentException("Semester harus berupa angka bulat");
    }
    return new Mahasiswa(
        $nim,
        (string)($_POST['nama'] ?? ''),
        (string)($_POST['fakultas'] ?? ''),
        (string)($_POST['prodi'] ?? ''),
        (string)($_POST['asalSekolah'] ?? ''),
        $semester,
        (string)($_POST['hobi'] ?? ''),
        (string)($_POST['agama'] ?? '')
    );
}

$repo  = new DataMahasiswa();
$error = null;
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);
$edit  = null;
$old   = $_POST; // untuk mengisi ulang form tambah saat input tidak valid

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    try {
        if ($aksi === 'tambah') {
            $repo->tambah(dariForm((string)($_POST['nim'] ?? '')));
            $_SESSION['pesan'] = "Data berhasil ditambahkan";
            header('Location: index.php'); exit;
        } elseif ($aksi === 'ubah') {
            $nim = (string)($_POST['nim'] ?? '');
            if ($repo->cari($nim) === null) throw new InvalidArgumentException("Data tidak ditemukan");
            $repo->ubah(dariForm($nim)); // NIM diambil dari data lama, bukan dari input yang bisa diubah
            $_SESSION['pesan'] = "Data berhasil diubah";
            header('Location: index.php'); exit;
        } elseif ($aksi === 'hapus') {
            $repo->hapus((string)($_POST['nim'] ?? ''));
            $_SESSION['pesan'] = "Data berhasil dihapus";
            header('Location: index.php'); exit;
        }
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
        if ($aksi === 'ubah') $edit = $repo->cari((string)($_POST['nim'] ?? ''));
    }
} elseif (isset($_GET['edit'])) {
    $edit = $repo->cari((string)$_GET['edit']);
    $old = [];
}

$modeEdit = $edit !== null;
$val = fn(string $k, string $default = '') => aman($old[$k] ?? $default);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CRUD Mahasiswa - Enkapsulasi</title>
<style>
 body{font-family:Arial,sans-serif;max-width:1000px;margin:24px auto;padding:0 12px;color:#222}
 table{border-collapse:collapse;width:100%;margin-top:12px}
 th,td{border:1px solid #bbb;padding:6px 8px;text-align:left;font-size:14px}
 th{background:#e8eef6}
 form.f label{display:block;margin:8px 0 2px;font-size:14px}
 form.f input,form.f select{width:100%;max-width:420px;padding:6px;box-sizing:border-box}
 .err{background:#fde8e8;border:1px solid #e99;padding:8px;margin:10px 0}
 .ok{background:#e6f6e6;border:1px solid #9c9;padding:8px;margin:10px 0}
 button{padding:6px 12px;cursor:pointer}
 input[readonly]{background:#eee}
 .inline{display:inline}
</style>
</head>
<body>
<h1>CRUD Data Mahasiswa</h1>
<p>Pertemuan 05 – Enkapsulasi (data disimpan di PHP Session)</p>

<?php if ($error): ?><div class="err"><?= aman($error) ?></div><?php endif; ?>
<?php if ($pesan): ?><div class="ok"><?= aman($pesan) ?></div><?php endif; ?>

<h2><?= $modeEdit ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' ?></h2>
<form class="f" method="post" action="index.php">
 <input type="hidden" name="aksi" value="<?= $modeEdit ? 'ubah' : 'tambah' ?>">

 <label>NIM</label>
 <?php if ($modeEdit): ?>
   <input name="nim" value="<?= aman($edit->getNim()) ?>" readonly>
 <?php else: ?>
   <input name="nim" value="<?= $val('nim') ?>">
 <?php endif; ?>

 <label>Nama</label>
 <input name="nama" value="<?= $modeEdit ? aman($edit->getNama()) : $val('nama') ?>">

 <label>Fakultas</label>
 <input name="fakultas" value="<?= $modeEdit ? aman($edit->getFakultas()) : $val('fakultas') ?>">

 <label>Prodi</label>
 <input name="prodi" value="<?= $modeEdit ? aman($edit->getProdi()) : $val('prodi') ?>">

 <label>Asal Sekolah</label>
 <input name="asalSekolah" value="<?= $modeEdit ? aman($edit->getAsalSekolah()) : $val('asalSekolah') ?>">

 <label>Semester (1–14)</label>
 <input name="semester" value="<?= $modeEdit ? aman($edit->getSemester()) : $val('semester') ?>">

 <label>Hobi</label>
 <input name="hobi" value="<?= $modeEdit ? aman($edit->getHobi()) : $val('hobi') ?>">

 <label>Agama</label>
 <?php $agamaDipilih = $modeEdit ? $edit->getAgama() : ($old['agama'] ?? ''); ?>
 <select name="agama">
  <option value="">-- pilih --</option>
  <?php foreach (Mahasiswa::AGAMA as $a): ?>
   <option value="<?= aman($a) ?>" <?= $a === $agamaDipilih ? 'selected' : '' ?>><?= aman($a) ?></option>
  <?php endforeach; ?>
 </select>

 <p>
  <button type="submit"><?= $modeEdit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($modeEdit): ?><a href="index.php">Batal</a><?php endif; ?>
 </p>
</form>

<h2>Daftar Mahasiswa</h2>
<table>
 <tr>
  <th>NIM</th><th>Nama</th><th>Fakultas</th><th>Prodi</th><th>Asal Sekolah</th>
  <th>Smt</th><th>Hobi</th><th>Agama</th><th>Aksi</th>
 </tr>
 <?php foreach ($repo->semua() as $m): ?>
 <tr>
  <td><?= aman($m->getNim()) ?></td>
  <td><?= aman($m->getNama()) ?></td>
  <td><?= aman($m->getFakultas()) ?></td>
  <td><?= aman($m->getProdi()) ?></td>
  <td><?= aman($m->getAsalSekolah()) ?></td>
  <td><?= aman($m->getSemester()) ?></td>
  <td><?= aman($m->getHobi()) ?></td>
  <td><?= aman($m->getAgama()) ?></td>
  <td>
   <a href="index.php?edit=<?= urlencode($m->getNim()) ?>">Edit</a>
   <form class="inline" method="post" action="index.php" onsubmit="return confirm('Hapus data ini?')">
    <input type="hidden" name="aksi" value="hapus">
    <input type="hidden" name="nim" value="<?= aman($m->getNim()) ?>">
    <button type="submit">Hapus</button>
   </form>
  </td>
 </tr>
 <?php endforeach; ?>
 <?php if (count($repo->semua()) === 0): ?>
 <tr><td colspan="9">Belum ada data.</td></tr>
 <?php endif; ?>
</table>
</body>
</html>
