<?php
declare(strict_types=1);

require_once __DIR__ . '/Pasien.php';

final class PasienRawatJalan extends Pasien {
    // 5 Atribut khusus Pasien Rawat Jalan
    private string $poliTujuan;
    private string $namaDokter;
    private int $biayaPemeriksaan;
    private int $biayaObat;
    private int $diskonAsuransi; // Persen (0 - 100)

    // Method 1: Constructor Anak
    public function __construct(
        string $noRm,
        string $nama,
        string $nik,
        int $usia,
        string $jenisKelamin,
        string $poliTujuan,
        string $namaDokter,
        int $biayaPemeriksaan,
        int $biayaObat,
        int $diskonAsuransi
    ) {
        parent::__construct($noRm, $nama, $nik, $usia, $jenisKelamin);

        if (trim($poliTujuan) === '' || trim($namaDokter) === '') {
            throw new InvalidArgumentException('Poli tujuan dan nama dokter wajib diisi.');
        }
        if ($biayaPemeriksaan < 0 || $biayaObat < 0) {
            throw new InvalidArgumentException('Biaya pemeriksaan dan obat tidak boleh bernilai negatif.');
        }
        if ($diskonAsuransi < 0 || $diskonAsuransi > 100) {
            throw new InvalidArgumentException('Diskon asuransi harus bernilai antara 0% hingga 100%.');
        }

        $this->poliTujuan = trim($poliTujuan);
        $this->namaDokter = trim($namaDokter);
        $this->biayaPemeriksaan = $biayaPemeriksaan;
        $this->biayaObat = $biayaObat;
        $this->diskonAsuransi = $diskonAsuransi;
    }

    // Method 2: Menghitung total kotor
    public function hitungBiayaKotor(): int {
        return $this->biayaPemeriksaan + $this->biayaObat;
    }

    // Method 3: Method Overriding dengan pembagian bulat integer (intdiv)
    public function hitungTotalBiaya(): int {
        $totalKotor = $this->hitungBiayaKotor();
        return intdiv($totalKotor * (100 - $this->diskonAsuransi), 100);
    }

    // Method 4: Method Overriding identitas jenis
    public function getJenisPelayanan(): string {
        return 'Rawat Jalan';
    }

    // Getter untuk tampilan
    public function getPoliTujuan(): string { return $this->poliTujuan; }
    public function getNamaDokter(): string { return $this->namaDokter; }
    public function getBiayaPemeriksaan(): int { return $this->biayaPemeriksaan; }
    public function getBiayaObat(): int { return $this->biayaObat; }
    public function getDiskonAsuransi(): int { return $this->diskonAsuransi; }
}