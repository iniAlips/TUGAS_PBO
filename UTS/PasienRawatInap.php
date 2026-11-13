<?php
declare(strict_types=1);

require_once __DIR__ . '/Pasien.php';

final class PasienRawatInap extends Pasien {
    
    private string $nomorKamar;
    private string $kelasKamar; // VIP, Kelas 1, Kelas 2, Kelas 3
    private int $lamaInap;      // Hari
    private int $tarifHarian;   // Biaya per malam
    private int $biayaTindakan;  // Biaya dokter/tindakan medis

    public function __construct(
        string $noRm,
        string $nama,
        string $nik,
        int $usia,
        string $jenisKelamin,
        string $nomorKamar,
        string $kelasKamar,
        int $lamaInap,
        int $tarifHarian,
        int $biayaTindakan
    ) {
        parent::__construct($noRm, $nama, $nik, $usia, $jenisKelamin);

        if (trim($nomorKamar) === '') {
            throw new InvalidArgumentException('Nomor kamar rawat inap wajib diisi.');
        }
        if ($lamaInap < 1) {
            throw new InvalidArgumentException('Lama inap minimal 1 hari.');
        }
        if ($tarifHarian < 0 || $biayaTindakan < 0) {
            throw new InvalidArgumentException('Tarif kamar dan biaya tindakan tidak boleh negatif.');
        }

        $this->nomorKamar = trim($nomorKamar);
        $this->kelasKamar = trim($kelasKamar);
        $this->lamaInap = $lamaInap;
        $this->tarifHarian = $tarifHarian;
        $this->biayaTindakan = $biayaTindakan;
    }

    public function hitungBiayaKamar(): int {
        return $this->lamaInap * $this->tarifHarian;
    }

    public function hitungTotalBiaya(): int {
        return $this->hitungBiayaKamar() + $this->biayaTindakan;
    }

    public function getJenisPelayanan(): string {
        return 'Rawat Inap';
    }

    public function getNomorKamar(): string { return $this->nomorKamar; }
    public function getKelasKamar(): string { return $this->kelasKamar; }
    public function getLamaInap(): int { return $this->lamaInap; }
    public function getTarifHarian(): int { return $this->tarifHarian; }
    public function getBiayaTindakan(): int { return $this->biayaTindakan; }
}