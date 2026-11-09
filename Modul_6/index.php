<?php
declare(strict_types=1);
require_once __DIR__ . '/DataMahasiswa.php'; 
session_start();

function aman(mixed $teks): string
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}


function intForm(string $key, string $label): int
{
    $v = filter_var($_POST[$key] ?? '', FILTER_VALIDATE_INT);
    if ($v === false) {
        throw new InvalidArgumentException("$label harus berupa angka bulat");
    }
    return $v;
}


function dariForm(string $nim): Mahasiswa
{
    $semester = intForm('semester', 'Semester');
    $ukt      = intForm('ukt', 'UKT');
    $nama     = (string)($_POST['nama'] ?? '');
    $fakultas = (string)($_POST['fakultas'] ?? '');
    $prodi    = (string)($_POST['prodi'] ?? '');
    $sekolah  = (string)($_POST['asalSekolah'] ?? '');
    $hobi     = (string)($_POST['hobi'] ?? '');
    $agama    = (string)($_POST['agama'] ?? '');
    $jenis    = (string)($_POST['jenis'] ?? '');

    if ($jenis === 'reguler') {
        return new MahasiswaReguler($nim, $nama, $fakultas, $prodi, $sekolah, $semester, $hobi, $agama, $ukt);
    }
    if ($jenis === 'beasiswa') {
        $potongan = intForm('potongan', 'Potongan');
        return new MahasiswaBeasiswa($nim, $nama, $fakultas, $prodi, $sekolah, $semester, $hobi, $agama, $ukt, $potongan);
    }
    throw new InvalidArgumentException("Jenis mahasiswa tidak dikenal");
}

$repo  = new DataMahasiswa();
$error = null;
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);


$f = [
    'nim' => '', 'nama' => '', 'fakultas' => '', 'prodi' => '', 'asalSekolah' => '',
    'semester' => '', 'hobi' => '', 'agama' => '', 'jenis' => 'reguler',
    'ukt' => '', 'potongan' => '0',
];
$modeEdit = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string)($_POST['aksi'] ?? '');
    foreach ($f as $k => $_) { // isi ulang form dari input (jika ada error)
        if (isset($_POST[$k]) && is_string($_POST[$k])) $f[$k] = $_POST[$k];
    }
    $modeEdit = ($aksi === 'ubah');
    try {
        if ($aksi === 'tambah') {
            $repo->tambah(dariForm((string)($_POST['nim'] ?? '')));
            $_SESSION['pesan'] = "Data berhasil ditambahkan";
            header('Location: index.php'); exit;
        } elseif ($aksi === 'ubah') {
            $nim = (string)($_POST['nim'] ?? '');
            if ($repo->cari($nim) === null) throw new InvalidArgumentException("Data tidak ditemukan");
            // object child baru pada NIM yang sama; jenis boleh berubah, NIM tidak
            $repo->ubah(dariForm($nim));
            $_SESSION['pesan'] = "Data berhasil diubah";
            header('Location: index.php'); exit;
        } elseif ($aksi === 'hapus') {
            $nim = trim((string)($_POST['nim'] ?? ''));
            if ($repo->cari($nim) === null) throw new InvalidArgumentException("Data tidak ditemukan");
            $repo->hapus($nim);
            $_SESSION['pesan'] = "Data berhasil dihapus";
            header('Location: index.php'); exit;
        }
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    }
} elseif (isset($_GET['edit'])) {
    $m = $repo->cari((string)$_GET['edit']);
    if ($m !== null) {
        $modeEdit = true;
        $beasiswa = $m instanceof MahasiswaBeasiswa;
        $f = [
            'nim' => $m->getNim(), 'nama' => $m->getNama(), 'fakultas' => $m->getFakultas(),
            'prodi' => $m->getProdi(), 'asalSekolah' => $m->getAsalSekolah(),
            'semester' => (string)$m->getSemester(), 'hobi' => $m->getHobi(), 'agama' => $m->getAgama(),
            'jenis' => $beasiswa ? 'beasiswa' : 'reguler',
            'ukt' => (string)($m instanceof MahasiswaBeasiswa || $m instanceof MahasiswaReguler ? $m->getUkt() : 0),
            'potongan' => $beasiswa ? (string)$m->getPotonganPersen() : '0',
        ];
    }
}


$q = trim((string)($_GET['q'] ?? ''));
$tampil = array_filter($repo->semua(), function (Mahasiswa $m) use ($q): bool {
    return $q === ''
        || stripos($m->getNim(), $q) !== false
        || stripos($m->getNama(), $q) !== false;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CRUD Mahasiswa - Inheritance</title>
<style>
 body{font-family:Arial,sans-serif;max-width:1100px;margin:24px auto;padding:0 12px;color:#222}
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
<p>Pertemuan 06 – Inheritance (Reguler &amp; Beasiswa, data disimpan di PHP Session)</p>

<?php if ($error): ?><div class="err"><?= aman($error) ?></div><?php endif; ?>
<?php if ($pesan): ?><div class="ok"><?= aman($pesan) ?></div><?php endif; ?>

<h2><?= $modeEdit ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' ?></h2>
<form class="f" method="post" action="index.php">
 <input type="hidden" name="aksi" value="<?= $modeEdit ? 'ubah' : 'tambah' ?>">

 <label>NIM</label>
 <input name="nim" value="<?= aman($f['nim']) ?>" <?= $modeEdit ? 'readonly' : '' ?>>

 <label>Nama</label>
 <input name="nama" value="<?= aman($f['nama']) ?>">

 <label>Fakultas</label>
 <input name="fakultas" value="<?= aman($f['fakultas']) ?>">

 <label>Prodi</label>
 <input name="prodi" value="<?= aman($f['prodi']) ?>">

 <label>Asal Sekolah</label>
 <input name="asalSekolah" value="<?= aman($f['asalSekolah']) ?>">

 <label>Semester (1–14)</label>
 <input name="semester" value="<?= aman($f['semester']) ?>">

 <label>Hobi</label>
 <input name="hobi" value="<?= aman($f['hobi']) ?>">

 <label>Agama</label>
 <select name="agama">
  <option value="">-- pilih --</option>
  <?php foreach (Mahasiswa::AGAMA as $a): ?>
   <option value="<?= aman($a) ?>" <?= $a === $f['agama'] ? 'selected' : '' ?>><?= aman($a) ?></option>
  <?php endforeach; ?>
 </select>

 <label>Jenis Mahasiswa</label>
 <select name="jenis" id="jenis">
  <option value="reguler" <?= $f['jenis'] === 'reguler' ? 'selected' : '' ?>>Reguler</option>
  <option value="beasiswa" <?= $f['jenis'] === 'beasiswa' ? 'selected' : '' ?>>Beasiswa</option>
 </select>

 <label>UKT (Rp)</label>
 <input name="ukt" value="<?= aman($f['ukt']) ?>">

 <div id="boxPotongan">
  <label>Potongan Beasiswa (0–100 %)</label>
  <input name="potongan" value="<?= aman($f['potongan']) ?>">
 </div>

 <p>
  <button type="submit"><?= $modeEdit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($modeEdit): ?><a href="index.php">Batal</a><?php endif; ?>
 </p>
</form>

<h2>Daftar Mahasiswa</h2>
<form method="get" action="index.php">
 <input name="q" placeholder="Cari NIM atau nama" value="<?= aman($q) ?>">
 <button type="submit">Cari</button>
 <?php if ($q !== ''): ?><a href="index.php">Reset</a><?php endif; ?>
</form>

<table>
 <tr>
  <th>NIM</th><th>Nama</th><th>Fakultas</th><th>Prodi</th><th>Asal Sekolah</th>
  <th>Smt</th><th>Hobi</th><th>Agama</th><th>Jenis</th><th>Biaya Akhir (Rp)</th><th>Aksi</th>
 </tr>
 <?php foreach ($tampil as $m): ?>
 <tr>
  <td><?= aman($m->getNim()) ?></td>
  <td><?= aman($m->getNama()) ?></td>
  <td><?= aman($m->getFakultas()) ?></td>
  <td><?= aman($m->getProdi()) ?></td>
  <td><?= aman($m->getAsalSekolah()) ?></td>
  <td><?= aman($m->getSemester()) ?></td>
  <td><?= aman($m->getHobi()) ?></td>
  <td><?= aman($m->getAgama()) ?></td>
  <td><?= aman($m->getJenis()) ?></td>
  <td><?= aman(number_format($m->hitungBiaya(), 0, ',', '.')) ?></td>
  <td>
   <a href="index.php?edit=<?= urlencode($m->getNim()) ?>">Edit</a>
   <form class="inline hapus" method="post" action="index.php">
    <input type="hidden" name="aksi" value="hapus">
    <input type="hidden" name="nim" value="<?= aman($m->getNim()) ?>">
    <button type="submit">Hapus</button>
   </form>
  </td>
 </tr>
 <?php endforeach; ?>
 <?php if (count($tampil) === 0): ?>
 <tr><td colspan="11">Belum ada data.</td></tr>
 <?php endif; ?>
</table>

<script>
 const jenis = document.getElementById('jenis');
 const box = document.getElementById('boxPotongan');
 function atur() { box.style.display = jenis.value === 'beasiswa' ? 'block' : 'none'; }
 jenis.addEventListener('change', atur);
 atur();
 document.querySelectorAll('form.hapus').forEach(function (f) {
   f.addEventListener('submit', function (e) {
     if (!confirm('Hapus data ini?')) e.preventDefault();
   });
 });
</script>
</body>
</html>
