<?php
declare(strict_types=1);

require_once __DIR__ . '/PasienRawatInap.php';
require_once __DIR__ . '/PasienRawatJalan.php';

class DataPasien {
    /** @var array<string, Pasien> */
    private array $data = [];

    public function __construct(array $awal = []) {
        foreach ($awal as $p) {
            if ($p instanceof Pasien) {
                $this->data[$p->getNoRm()] = $p;
            }
        }
    }

    public function tambah(Pasien $p): bool {
        // Cek No RM duplikat
        if (isset($this->data[$p->getNoRm()])) {
            return false;
        }
        $this->data[$p->getNoRm()] = $p;
        return true;
    }

    public function cari(string $noRm): ?Pasien {
        return $this->data[$noRm] ?? null;
    }

    /** @return array<string, Pasien> */
    public function semua(): array {
        return $this->data;
    }

    public function ubah(string $noRmLama, Pasien $p): bool {
        if (!isset($this->data[$noRmLama]) || $p->getNoRm() !== $noRmLama) {
            return false;
        }
        $this->data[$noRmLama] = $p;
        return true;
    }

    public function hapus(string $noRm): bool {
        if (!isset($this->data[$noRm])) {
            return false;
        }
        unset($this->data[$noRm]);
        return true;
    }
}