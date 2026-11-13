<?php
declare(strict_types=1);

abstract class Pasien {
    
    private string $noRm;
    private string $nama;
    private string $nik;
    private int $usia;
    private string $jenisKelamin; // 'L' atau 'P'

   
    public function __construct(string $noRm, string $nama, string $nik, int $usia, string $jenisKelamin) {
        $noRm = trim($noRm);
        $nik = trim($nik);

        if ($noRm === '') {
            throw new InvalidArgumentException('Nomor Rekam Medis (No RM) wajib diisi.');
        }
        if (strlen($nik) < 8) {
            throw new InvalidArgumentException('NIK minimal 8 digit.');
        }

        $this->noRm = $noRm; // Bersifat Read-Only (tidak ada setter untuk identitas utama)
        $this->nik = $nik;
        $this->setNama($nama);
        $this->setUsia($usia);
        $this->setJenisKelamin($jenisKelamin);
    }

   
    public function getNoRm(): string {
        return $this->noRm;
    }

    
    public function getNama(): string {
        return $this->nama;
    }

    public function setNama(string $nama): void {
        $nama = trim($nama);
        if (mb_strlen($nama) < 3) {
            throw new InvalidArgumentException('Nama pasien minimal 3 karakter.');
        }
        $this->nama = $nama;
    }

    
    public function getNik(): string {
        return $this->nik;
    }

    
    public function getUsia(): int {
        return $this->usia;
    }

    public function setUsia(int $usia): void {
        if ($usia < 0 || $usia > 130) {
            throw new InvalidArgumentException('Usia tidak valid (rentang 0 - 130 tahun).');
        }
        $this->usia = $usia;
    }

    public function getJenisKelamin(): string {
        return $this->jenisKelamin;
    }

    public function setJenisKelamin(string $jk): void {
        $jk = strtoupper(trim($jk));
        if ($jk !== 'L' && $jk !== 'P') {
            throw new InvalidArgumentException('Jenis kelamin harus "L" (Laki-laki) atau "P" (Perempuan).');
        }
        $this->jenisKelamin = $jk;
    }

    
    abstract public function hitungTotalBiaya(): int;
    abstract public function getJenisPelayanan(): string;
}