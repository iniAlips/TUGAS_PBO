<?php
// Eksperimen: menambah jenis baru (MahasiswaPrestasi, UKT 80%).
require 'Mahasiswa.php';

class MahasiswaPrestasi extends Mahasiswa {
    public function hitungUKT(): int { return (int) (parent::hitungUKT() * 0.8); }
    public function jenis(): string { return 'prestasi'; }
}

$daftar = [
    new MahasiswaReguler('001', 'Andi', 'Informatika'),
    new MahasiswaBeasiswa('002', 'Siti', 'Informatika'),
    new MahasiswaPrestasi('005', 'Dewi', 'Informatika'),
];

foreach ($daftar as $objek) {
    echo $objek->tampil('lengkap') . ' | ' . $objek->jenis()
        . ' | ' . tampilUKT($objek) . '<br>';
}
