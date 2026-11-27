<?php
// Entity class yang merepresentasikan satu baris data tabel lowongan
class Lowongan {
    private ?int $id;
    private string $judulPosisi;
    private string $departemen;
    private string $lokasi;
    private int $kuota;
    private string $status;

    // Konstruktor untuk mengisi seluruh properti entitas Lowongan
    public function __construct(
        ?int $id,
        string $judulPosisi,
        string $departemen,
        string $lokasi,
        int $kuota,
        string $status
    ) {
        $this->id = $id;
        $this->judulPosisi = $judulPosisi;
        $this->departemen = $departemen;
        $this->lokasi = $lokasi;
        $this->kuota = $kuota;
        $this->status = $status;
    }

    // Getter untuk mengakses properti privat
    public function getId(): ?int { return $this->id; }
    public function getJudulPosisi(): string { return $this->judulPosisi; }
    public function getDepartemen(): string { return $this->departemen; }
    public function getLokasi(): string { return $this->lokasi; }
    public function getKuota(): int { return $this->kuota; }
    public function getStatus(): string { return $this->status; }
}
?>