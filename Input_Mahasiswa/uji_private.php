<?php
// Uji 06: akses langsung property private -> harus error
declare(strict_types=1);
require_once __DIR__ . '/Mahasiswa.php';

$m = new Mahasiswa("23001", "Siti", "Sains dan Teknologi", "Informatika", "SMAN 1", 4, "Membaca", "Islam");
echo $m->getSemester() . "<br>";   // OK lewat getter
echo $m->semester;                  // Fatal error: Cannot access private property
