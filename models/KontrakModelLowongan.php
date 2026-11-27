<?php
// Kontrak interface untuk operasi data Lowongan
interface KontrakModelLowongan {
    public function getAllLowongan(): array;
    public function getLowonganById(int $id): ?Lowongan;
    public function addLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void;
    public function updateLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void;
    public function deleteLowongan(int $id): void;
}
?>