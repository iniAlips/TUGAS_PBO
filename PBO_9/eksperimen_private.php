<?php
// Eksperimen: mengakses property private dari luar class.
// Sengaja dipisah dari uji_oop.php (baris percobaan tidak digabung).
require 'Mahasiswa.php';

$m = new MahasiswaReguler('003', 'Budi', 'TI');

try {
    echo $m->nama;
} catch (Error $e) {
    // Hasil: Cannot access private property MahasiswaReguler::$nama
    echo get_class($e) . ': ' . $e->getMessage() . '<br>';
}

echo 'Cara yang benar: ' . $m->getNama();
