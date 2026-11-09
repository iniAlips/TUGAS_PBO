<?php
declare(strict_types=1);
require_once __DIR__ . '/MahasiswaReguler.php';
require_once __DIR__ . '/MahasiswaBeasiswa.php';


$daftar = [
    new MahasiswaReguler("23001", "Andi", "Sains dan Teknologi", "Informatika", "SMAN 1", 4, "Membaca", "Islam", 5000000),
    new MahasiswaBeasiswa("23002", "Siti", "Sains dan Teknologi", "Informatika", "SMAN 2", 4, "Menulis", "Islam", 5000000, 60),
];

foreach ($daftar as $m) {
    echo $m->getNama() . ' / ' . $m->getJenis()
       . ' / Rp' . number_format($m->hitungBiaya(), 0, ',', '.') . "<br>";
}



$uji = [
    'NIM kosong (parent)'  => fn() => new MahasiswaReguler("", "Budi", "FST", "IF", "SMAN 1", 4, "", "Islam", 100),
    'Semester 15 (parent)' => fn() => new MahasiswaReguler("23003", "Budi", "FST", "IF", "SMAN 1", 15, "", "Islam", 100),
    'UKT -100 (child)'     => fn() => new MahasiswaReguler("23003", "Budi", "FST", "IF", "SMAN 1", 4, "", "Islam", -100),
    'Potongan 120% (child)'=> fn() => new MahasiswaBeasiswa("23003", "Budi", "FST", "IF", "SMAN 1", 4, "", "Islam", 100, 120),
];
foreach ($uji as $nama => $buat) {
    try {
        $buat();
        echo "$nama: DITERIMA (tidak sesuai)<br>";
    } catch (InvalidArgumentException $e) {
        echo "$nama: ditolak - " . $e->getMessage() . "<br>";
    }
}


$m = $daftar[0];
echo $m->getSemester() . "<br>";   // OK lewat getter
echo $m->semester;                  // Fatal error: Cannot access private property
