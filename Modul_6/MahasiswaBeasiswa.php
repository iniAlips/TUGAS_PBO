<?php
declare(strict_types=1);
require_once __DIR__ . '/Mahasiswa.php';

final class MahasiswaBeasiswa extends Mahasiswa
{
    private int $ukt;
    private int $potonganPersen;

    public function __construct(
        string $nim, string $nama, string $fakultas, string $prodi,
        string $asalSekolah, int $semester, string $hobi, string $agama,
        int $ukt, int $potonganPersen
    ) {
        
        parent::__construct($nim, $nama, $fakultas, $prodi, $asalSekolah, $semester, $hobi, $agama);
        $this->setUkt($ukt);
        $this->setPotonganPersen($potonganPersen);
    }

    public function getUkt(): int { return $this->ukt; }
    public function getPotonganPersen(): int { return $this->potonganPersen; }

    public function setUkt(int $ukt): void
    {
        if ($ukt < 0) throw new InvalidArgumentException("UKT tidak boleh negatif");
        $this->ukt = $ukt;
    }

    public function setPotonganPersen(int $potonganPersen): void
    {
        if ($potonganPersen < 0 || $potonganPersen > 100) {
            throw new InvalidArgumentException("Potongan harus 0–100 persen");
        }
        $this->potonganPersen = $potonganPersen;
    }

   
    public function hitungBiaya(): int
    {
        return intdiv($this->ukt * (100 - $this->potonganPersen), 100);
    }

    public function getJenis(): string { return 'Beasiswa'; }
}
