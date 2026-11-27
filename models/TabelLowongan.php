<?php
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/KontrakModelLowongan.php';
require_once __DIR__ . '/Lowongan.php';

class TabelLowongan extends DB implements KontrakModelLowongan {

    // Inisialisasi koneksi PDO melalui parent class DB
    public function __construct(
        string $host = "localhost",
        string $db_name = "mvp_kandidat_db",
        string $username = "root",
        string $password = ""
    ) {
        parent::__construct($host, $db_name, $username, $password);
    }

    // Mapping hasil query database menjadi objek Lowongan
    private function mapRowToLowongan(array $row): Lowongan {
        return new Lowongan(
            (int)$row['id'],
            $row['judulPosisi'],
            $row['departemen'],
            $row['lokasi'],
            (int)$row['kuota'],
            $row['status']
        );
    }

    // Mengambil seluruh data lowongan
    public function getAllLowongan(): array {
        $query = "SELECT * FROM lowongan ORDER BY id ASC";
        $this->executeQuery($query);
        $rows = $this->getAllResult();

        $list = [];
        foreach ($rows as $row) {
            $list[] = $this->mapRowToLowongan($row);
        }
        return $list;
    }

    // Mengambil 1 data lowongan berdasarkan ID
    public function getLowonganById(int $id): ?Lowongan {
        $query = "SELECT * FROM lowongan WHERE id = :id LIMIT 1";
        $this->executeQuery($query, ['id' => $id]);
        $rows = $this->getAllResult();

        if (empty($rows)) {
            return null;
        }
        return $this->mapRowToLowongan($rows[0]);
    }

    // Menambahkan lowongan baru
    public function addLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $query = "INSERT INTO lowongan (judulPosisi, departemen, lokasi, kuota, status) 
                  VALUES (:judulPosisi, :departemen, :lokasi, :kuota, :status)";
        $this->executeQuery($query, [
            'judulPosisi' => $judulPosisi,
            'departemen'  => $departemen,
            'lokasi'      => $lokasi,
            'kuota'       => $kuota,
            'status'      => $status
        ]);
    }

    // Memperbarui data lowongan berdasarkan ID
    public function updateLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $query = "UPDATE lowongan 
                  SET judulPosisi = :judulPosisi, departemen = :departemen, lokasi = :lokasi, kuota = :kuota, status = :status 
                  WHERE id = :id";
        $this->executeQuery($query, [
            'id'          => $id,
            'judulPosisi' => $judulPosisi,
            'departemen'  => $departemen,
            'lokasi'      => $lokasi,
            'kuota'       => $kuota,
            'status'      => $status
        ]);
    }

    // Menghapus lowongan berdasarkan ID
    public function deleteLowongan(int $id): void {
        $query = "DELETE FROM lowongan WHERE id = :id";
        $this->executeQuery($query, ['id' => $id]);
    }
}
?>