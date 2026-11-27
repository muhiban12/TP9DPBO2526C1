<?php
// Memuat seluruh dependensi Model, View, dan Presenter untuk Kandidat & Lowongan
require_once __DIR__ . '/models/TabelKandidat.php';
require_once __DIR__ . '/views/ViewKandidat.php';
require_once __DIR__ . '/presenters/PresenterKandidat.php';

require_once __DIR__ . '/models/TabelLowongan.php';
require_once __DIR__ . '/views/ViewLowongan.php';
require_once __DIR__ . '/presenters/PresenterLowongan.php';

// Menentukan modul aktif berdasarkan parameter URL ?page= (default: kandidat)
$page = $_GET['page'] ?? 'kandidat';

if ($page === 'lowongan') {
    // Inisialisasi triad MVP untuk modul Lowongan
    $model = new TabelLowongan('localhost', 'mvp_kandidat_db', 'root', '');
    $view = new ViewLowongan();
    $presenter = new PresenterLowongan($model, $view);

    // Menangani aksi POST (Create, Update, Delete) untuk Lowongan
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'tambah') {
            $presenter->tambahLowongan(
                trim($_POST['judulPosisi'] ?? ''),
                trim($_POST['departemen'] ?? ''),
                trim($_POST['lokasi'] ?? ''),
                (int)($_POST['kuota'] ?? 1),
                trim($_POST['status'] ?? 'Dibuka')
            );
        } elseif ($action === 'ubah' && isset($_POST['id'])) {
            $presenter->ubahLowongan(
                (int)$_POST['id'],
                trim($_POST['judulPosisi'] ?? ''),
                trim($_POST['departemen'] ?? ''),
                trim($_POST['lokasi'] ?? ''),
                (int)($_POST['kuota'] ?? 1),
                trim($_POST['status'] ?? 'Dibuka')
            );
        } elseif ($action === 'hapus' && isset($_POST['id'])) {
            $presenter->hapusLowongan((int)$_POST['id']);
        }
        header("Location: index.php?page=lowongan");
        exit();
    } else {
        // Menangani tampilan GET (List, Form Add, Form Edit) untuk Lowongan
        $screen = $_GET['screen'] ?? 'list';
        if ($screen === 'add') {
            echo $presenter->tampilkanFormLowongan();
        } elseif ($screen === 'edit' && isset($_GET['id'])) {
            echo $presenter->tampilkanFormLowongan((int)$_GET['id']);
        } else {
            echo $presenter->tampilkanLowongan();
        }
    }
} else {
    // Inisialisasi triad MVP untuk modul Kandidat
    $model = new TabelKandidat('localhost', 'mvp_kandidat_db', 'root', '');
    $view = new ViewKandidat();
    $presenter = new PresenterKandidat($model, $view);

    // Menangani aksi POST (Create, Update, Delete) untuk Kandidat
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'tambah') {
            $presenter->tambahKandidat(
                trim($_POST['nama'] ?? ''),
                trim($_POST['posisi'] ?? ''),
                trim($_POST['domisili'] ?? ''),
                (int)($_POST['skorTes'] ?? 0),
                (int)($_POST['pengalamanTahun'] ?? 0)
            );
        } elseif ($action === 'ubah' && isset($_POST['id'])) {
            $presenter->ubahKandidat(
                (int)$_POST['id'],
                trim($_POST['nama'] ?? ''),
                trim($_POST['posisi'] ?? ''),
                trim($_POST['domisili'] ?? ''),
                (int)($_POST['skorTes'] ?? 0),
                (int)($_POST['pengalamanTahun'] ?? 0)
            );
        } elseif ($action === 'hapus' && isset($_POST['id'])) {
            $presenter->hapusKandidat((int)$_POST['id']);
        }
        header("Location: index.php?page=kandidat");
        exit();
    } else {
        // Menangani tampilan GET (List, Form Add, Form Edit) untuk Kandidat
        $screen = $_GET['screen'] ?? 'list';
        if ($screen === 'add') {
            echo $presenter->tampilkanFormKandidat();
        } elseif ($screen === 'edit' && isset($_GET['id'])) {
            echo $presenter->tampilkanFormKandidat((int)$_GET['id']);
        } else {
            echo $presenter->tampilkanKandidat();
        }
    }
}
?>