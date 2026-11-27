<?php
require_once __DIR__ . '/KontrakPresenter.php';
require_once __DIR__ . '/../models/KontrakModel.php';
require_once __DIR__ . '/../views/KontrakView.php';

class PresenterKandidat implements KontrakPresenter {
    private KontrakModel $model;
    private KontrakView $view;

    // Menyuntikkan dependensi Model dan View melalui kontrak interface
    public function __construct(KontrakModel $model, KontrakView $view) {
        $this->model = $model;
        $this->view = $view;
    }

    // Merakit baris HTML tabel agar View tetap 100% pasif tanpa loop
    private function buildRowHtml(int $no, Kandidat $kandidat): string {
        $id = (int)$kandidat->getId();
        $nama = htmlspecialchars($kandidat->getNama());
        $posisi = htmlspecialchars($kandidat->getPosisi());
        $domisili = htmlspecialchars($kandidat->getDomisili());
        $skorTes = number_format($kandidat->getSkorTes()) . ' / 100';
        $pengalaman = number_format($kandidat->getPengalamanTahun()) . ' Tahun';

        return "<tr>
            <td class='col-id'>{$no}</td>
            <td><strong>{$nama}</strong></td>
            <td>{$posisi}</td>
            <td>{$domisili}</td>
            <td>{$skorTes}</td>
            <td>{$pengalaman}</td>
            <td class='col-actions'>
                <a href='index.php?page=kandidat&screen=edit&id={$id}' class='btn btn-edit'>Edit</a>
                <form method='post' action='index.php?page=kandidat' style='display:inline;' onsubmit=\"return confirm('Hapus kandidat ini?')\">
                    <input type='hidden' name='action' value='hapus'>
                    <input type='hidden' name='id' value='{$id}'>
                    <button type='submit' class='btn btn-delete'>Hapus</button>
                </form>
            </td>
        </tr>";
    }

    // Mengambil list kandidat dari Model, merakit HTML baris, dan mengirim ke View
    public function tampilkanKandidat(): string {
        $listKandidat = $this->model->getAllKandidat();
        $totalKandidat = count($listKandidat);
        $rowsHtml = '';

        if (empty($listKandidat)) {
            $rowsHtml = '<tr><td colspan="7" style="text-align:center; color:#6b7280; padding:24px;">Belum ada data kandidat.</td></tr>';
        } else {
            $no = 1;
            foreach ($listKandidat as $kandidat) {
                $rowsHtml .= $this->buildRowHtml($no, $kandidat);
                $no++;
            }
        }

        return $this->view->renderDaftar($rowsHtml, $totalKandidat);
    }

    // Menyiapkan data form (kosong untuk tambah, terisi otomatis untuk edit)
    public function tampilkanFormKandidat(?int $id = null): string {
        if ($id !== null) {
            $kandidat = $this->model->getKandidatById($id);
            if ($kandidat === null) {
                header("Location: index.php?page=kandidat");
                exit();
            }

            $formData = [
                'id'              => $kandidat->getId(),
                'nama'            => $kandidat->getNama(),
                'posisi'          => $kandidat->getPosisi(),
                'domisili'        => $kandidat->getDomisili(),
                'skorTes'         => $kandidat->getSkorTes(),
                'pengalamanTahun' => $kandidat->getPengalamanTahun(),
            ];
            return $this->view->renderForm("Ubah Data Kandidat", "ubah", $formData);
        }

        return $this->view->renderForm("Tambah Kandidat Baru", "tambah", []);
    }

    // Meneruskan aksi tambah data ke Model lalu redirect
    public function tambahKandidat(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $this->model->addKandidat($nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
        header("Location: index.php?page=kandidat");
        exit();
    }

    // Meneruskan aksi ubah data ke Model lalu redirect
    public function ubahKandidat(int $id, string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $this->model->updateKandidat($id, $nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
        header("Location: index.php?page=kandidat");
        exit();
    }

    // Meneruskan aksi hapus data ke Model lalu redirect
    public function hapusKandidat(int $id): void {
        $this->model->deleteKandidat($id);
        header("Location: index.php?page=kandidat");
        exit();
    }
}
?>