<?php
require_once __DIR__ . '/KontrakPresenterLowongan.php';
require_once __DIR__ . '/../models/KontrakModelLowongan.php';
require_once __DIR__ . '/../views/KontrakViewLowongan.php';

class PresenterLowongan implements KontrakPresenterLowongan {
    private KontrakModelLowongan $model;
    private KontrakViewLowongan $view;

    // Menyuntikkan Model dan View Lowongan
    public function __construct(KontrakModelLowongan $model, KontrakViewLowongan $view) {
        $this->model = $model;
        $this->view = $view;
    }

    // Merakit baris HTML tabel lowongan di dalam Presenter
    private function buildRowHtml(int $no, Lowongan $lowongan): string {
        $id = (int)$lowongan->getId();
        $judul = htmlspecialchars($lowongan->getJudulPosisi());
        $dept = htmlspecialchars($lowongan->getDepartemen());
        $lokasi = htmlspecialchars($lowongan->getLokasi());
        $kuota = number_format($lowongan->getKuota()) . ' Orang';
        $status = htmlspecialchars($lowongan->getStatus());
        $badgeColor = ($status === 'Dibuka') ? '#16a34a' : '#dc2626';

        return "<tr>
            <td class='col-id'>{$no}</td>
            <td><strong>{$judul}</strong></td>
            <td>{$dept}</td>
            <td>{$lokasi}</td>
            <td>{$kuota}</td>
            <td><span style='color:{$badgeColor}; font-weight:600;'>{$status}</span></td>
            <td class='col-actions'>
                <a href='index.php?page=lowongan&screen=edit&id={$id}' class='btn btn-edit'>Edit</a>
                <form method='post' action='index.php?page=lowongan' style='display:inline;' onsubmit=\"return confirm('Hapus lowongan ini?')\">
                    <input type='hidden' name='action' value='hapus'>
                    <input type='hidden' name='id' value='{$id}'>
                    <button type='submit' class='btn btn-delete'>Hapus</button>
                </form>
            </td>
        </tr>";
    }

    // Mengolah data dari Model menjadi baris tabel HTML dan mengirimkannya ke View
    public function tampilkanLowongan(): string {
        $listLowongan = $this->model->getAllLowongan();
        $totalLowongan = count($listLowongan);
        $rowsHtml = '';

        if (empty($listLowongan)) {
            $rowsHtml = '<tr><td colspan="7" style="text-align:center; color:#6b7280; padding:24px;">Belum ada data lowongan.</td></tr>';
        } else {
            $no = 1;
            foreach ($listLowongan as $lowongan) {
                $rowsHtml .= $this->buildRowHtml($no, $lowongan);
                $no++;
            }
        }

        return $this->view->renderDaftar($rowsHtml, $totalLowongan);
    }

    // Menyiapkan data form tambah/ubah lowongan
    public function tampilkanFormLowongan(?int $id = null): string {
        if ($id !== null) {
            $lowongan = $this->model->getLowonganById($id);
            if ($lowongan === null) {
                header("Location: index.php?page=lowongan");
                exit();
            }

            $formData = [
                'id'          => $lowongan->getId(),
                'judulPosisi' => $lowongan->getJudulPosisi(),
                'departemen'  => $lowongan->getDepartemen(),
                'lokasi'      => $lowongan->getLokasi(),
                'kuota'       => $lowongan->getKuota(),
                'status'      => $lowongan->getStatus(),
            ];
            return $this->view->renderForm("Ubah Data Lowongan", "ubah", $formData);
        }

        return $this->view->renderForm("Tambah Lowongan Baru", "tambah", []);
    }

    // Menambah data lowongan lalu redirect
    public function tambahLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $this->model->addLowongan($judulPosisi, $departemen, $lokasi, $kuota, $status);
        header("Location: index.php?page=lowongan");
        exit();
    }

    // Mengubah data lowongan lalu redirect
    public function ubahLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $this->model->updateLowongan($id, $judulPosisi, $departemen, $lokasi, $kuota, $status);
        header("Location: index.php?page=lowongan");
        exit();
    }

    // Menghapus data lowongan lalu redirect
    public function hapusLowongan(int $id): void {
        $this->model->deleteLowongan($id);
        header("Location: index.php?page=lowongan");
        exit();
    }
}
?>