# Sistem Manajemen Rekrutmen & Lowongan (PHP Native MVP)

> **Tugas Praktikum 9 (TP9) Desain dan Pemrograman Berorientasi Objek (DPBO)**  
> **Program Studi Ilmu Komputer - Universitas Pendidikan Indonesia**

### 👤 Identitas Mahasiswa
- **Nama**: Muhiban Fadlan Nursaid
- **NIM**: 2400382
- **Kelas**: DPBO C1
- **Janji**: Saya Muhiban Fadlan Nursaid dengan NIM 2400382 mengerjakan Tugas Praktikum 9 dalam mata kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin.

---

## Deskripsi Proyek

Aplikasi web manajemen rekrutmen karyawan berbasis **PHP Native** yang menerapkan pola arsitektur **Model-View-Presenter (MVP)** dengan pendekatan **Strict Passive View**. Aplikasi ini mengelola dua modul utama bertema HR/Rekrutmen yang saling terhubung melalui navigasi tab pada antarmuka:

1. **Modul Data Kandidat (`kandidat`)** — Mengelola daftar pelamar kerja beserta posisi yang dilamar, domisili, skor tes seleksi (`0-100`), dan pengalaman kerja (tahun). Data ditampilkan terurut berdasarkan `skorTes` tertinggi (`ORDER BY skorTes DESC, id ASC`).
2. **Modul Data Lowongan Pekerjaan (`lowongan`)** — Mengelola daftar lowongan yang tersedia beserta departemen, lokasi penempatan, kuota penerimaan, dan status lowongan (`Dibuka` / `Ditutup`). Data ditampilkan terurut berdasarkan `id ASC`.

---

## Desain Program

### 1. Arsitektur MVP (*Strict Passive View*)

Seluruh komponen aplikasi dipisahkan secara ketat ke dalam tiga lapisan utama dan berkomunikasi melalui **Interface Contract** serta **Dependency Injection** pada konstruktor untuk memutus ketergantungan (*decoupling*):

* **Model (`models/`):**
  * **Koneksi Database (`DB.php`):** Kelas dasar (*parent class*) yang mengelola koneksi **PDO** ke MySQL dengan konfigurasi `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`, `PDO::ATTR_EMULATE_PREPARES => false`, serta charset `utf8mb4`. Menyediakan method `connect()`, `executeQuery()`, `getAllResult()`, dan `close()`.
  * **Entity Class (`Kandidat.php`, `Lowongan.php`):** Merepresentasikan struktur objek data dari masing-masing baris tabel database dengan properti `private` yang bertipe ketat (*typed properties*). `Kandidat` menyediakan *getter* dan *setter*, sedangkan `Lowongan` menyediakan *getter* untuk mengakses properti entitas.
  * **Contract (`KontrakModel.php`, `KontrakModelLowongan.php`):** Mendefinisikan kontrak method CRUD (`getAll...()`, `get...ById()`, `add...()`, `update...()`, `delete...()`) yang wajib diimplementasikan oleh kelas akses data.
  * **Data Access (`TabelKandidat.php`, `TabelLowongan.php`):** Mewarisi `DB` dan mengimplementasikan kontrak model masing-masing. Seluruh *query* SQL dijalankan menggunakan **Prepared Statements** untuk mencegah *SQL Injection*, lalu hasil baris query dipetakan menjadi objek Entity melalui helper privat `mapRowToKandidat()` dan `mapRowToLowongan()`.

* **Presenter (`presenters/`):**
  * **Contract (`KontrakPresenter.php`, `KontrakPresenterLowongan.php`):** Mendefinisikan kontrak interaksi antara *entry point* (`index.php`) dan Presenter (`tampilkan...()`, `tampilkanForm...()`, `tambah...()`, `ubah...()`, `hapus...()`).
  * **Presenter Class (`PresenterKandidat.php`, `PresenterLowongan.php`):** Menjadi pusat kendali logika bisnis dan logika presentasi. Presenter menerima dependensi `Model` dan `View` melalui konstruktor berbasis interface, lalu:
    * Mengambil array objek Entity dari Model dan menghitung total data (`count()`).
    * Melakukan iterasi (`foreach`) untuk merakit baris HTML tabel (`<tr>...</tr>`) melalui helper privat `buildRowHtml()`, termasuk menangani tampilan kosong (*empty state*), sanitasi output menggunakan `htmlspecialchars()`, pemformatan angka (`number_format()`), serta pewarnaan badge status pada modul Lowongan (`#16a34a` untuk `Dibuka` dan `#dc2626` untuk `Ditutup`).
    * Menyiapkan array data *prefill* saat membuka form edit, atau melakukan *redirect* ke halaman daftar apabila ID yang dicari tidak ditemukan.
    * Menerapkan pola **Post-Redirect-Get (PRG)** setelah operasi mutasi (`tambah`, `ubah`, `hapus`) dengan melakukan `header("Location: ...")`.

* **View (`views/` & `template/`):**
  * **Contract (`KontrakView.php`, `KontrakViewLowongan.php`):** Mendefinisikan kontrak method render tampilan (`renderDaftar()` dan `renderForm()`).
  * **Passive View (`ViewKandidat.php`, `ViewLowongan.php`):** Bersifat **100% pasif**—tidak mengenal class Model maupun Entity, tidak melakukan perulangan (`foreach`), dan tidak menghitung agregat. View hanya membaca file template HTML (`template/*.html`) dan mengganti token *placeholder* menggunakan `str_replace()` dengan data yang telah disiapkan dan disanitasi (`htmlspecialchars()`).
  * **Template HTML (`template/`):** Terdiri dari template daftar (`skin.html`, `skin_lowongan.html`) dan template formulir (`form.html`, `form_lowongan.html`) lengkap dengan navigasi tab antar modul serta styling CSS internal.

### 2. Ringkasan Kontrak Interface & Token Template

| Modul | Interface | Method Kontrak | Token Placeholder Template |
| :--- | :--- | :--- | :--- |
| **Kandidat** | `KontrakModel`<br>`KontrakPresenter`<br>`KontrakView` | `getAllKandidat`, `getKandidatById`, `addKandidat`, `updateKandidat`, `deleteKandidat`<br>`tampilkanKandidat`, `tampilkanFormKandidat`, `tambahKandidat`, `ubahKandidat`, `hapusKandidat`<br>`renderDaftar`, `renderForm` | **Daftar (`skin.html`):** `{{TABLE_ROWS}}`, `{{TOTAL_DATA}}`<br>**Form (`form.html`):** `{{FORM_TITLE}}`, `{{ACTION}}`, `{{ID}}`, `{{NAMA}}`, `{{POSISI}}`, `{{DOMISILI}}`, `{{SKOR_TES}}`, `{{PENGALAMAN_TAHUN}}` |
| **Lowongan** | `KontrakModelLowongan`<br>`KontrakPresenterLowongan`<br>`KontrakViewLowongan` | `getAllLowongan`, `getLowonganById`, `addLowongan`, `updateLowongan`, `deleteLowongan`<br>`tampilkanLowongan`, `tampilkanFormLowongan`, `tambahLowongan`, `ubahLowongan`, `hapusLowongan`<br>`renderDaftar`, `renderForm` | **Daftar (`skin_lowongan.html`):** `{{TABLE_ROWS}}`, `{{TOTAL_DATA}}`<br>**Form (`form_lowongan.html`):** `{{FORM_TITLE}}`, `{{ACTION}}`, `{{ID}}`, `{{JUDUL_POSISI}}`, `{{DEPARTEMEN}}`, `{{LOKASI}}`, `{{KUOTA}}`, `{{SELECTED_DIBUKA}}`, `{{SELECTED_DITUTUP}}` |

### 3. Desain Database (`mvp_kandidat_db`)

Aplikasi menggunakan database MySQL **`mvp_kandidat_db`** (engine `InnoDB`, charset `utf8mb4`) yang didefinisikan pada [`mvp_db.sql`](mvp_db.sql) dengan dua tabel utama:

#### A. Tabel `kandidat`
| Kolom | Tipe Data | Atribut / Default | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | `INT(11)` | `NOT NULL`, `AUTO_INCREMENT`, `PRIMARY KEY` | Identitas unik kandidat |
| `nama` | `VARCHAR(100)` | `NOT NULL` | Nama lengkap kandidat |
| `posisi` | `VARCHAR(100)` | `NOT NULL` | Posisi pekerjaan yang dilamar |
| `domisili` | `VARCHAR(100)` | `NOT NULL` | Kota domisili kandidat |
| `skorTes` | `INT(11)` | `NOT NULL`, `DEFAULT 0` | Nilai hasil tes seleksi (`0-100`) |
| `pengalamanTahun` | `INT(11)` | `NOT NULL`, `DEFAULT 0` | Lama pengalaman kerja dalam satuan tahun |

#### B. Tabel `lowongan`
| Kolom | Tipe Data | Atribut / Default | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | `INT(11)` | `NOT NULL`, `AUTO_INCREMENT`, `PRIMARY KEY` | Identitas unik lowongan |
| `judulPosisi` | `VARCHAR(100)` | `NOT NULL` | Nama posisi pekerjaan yang dibuka |
| `departemen` | `VARCHAR(100)` | `NOT NULL` | Divisi atau departemen terkait |
| `lokasi` | `VARCHAR(100)` | `NOT NULL` | Lokasi penempatan kerja |
| `kuota` | `INT(11)` | `NOT NULL`, `DEFAULT 1` | Jumlah kebutuhan kandidat |
| `status` | `VARCHAR(20)` | `NOT NULL`, `DEFAULT 'Dibuka'` | Status lowongan (`Dibuka` / `Ditutup`) |

> **Data Awal (*Seed Data*):** File [`mvp_db.sql`](mvp_db.sql) telah menyertakan **5 data awal kandidat** dan **3 data awal lowongan** agar aplikasi langsung dapat diuji setelah impor database.

---

## Penjelasan Alur Program

```text
User (Browser)
  │
  ├─── [GET / POST Request] ───> index.php (Router / Entry Point)
  │                                  │
  │                                  ▼
  │                         Presenter (KontrakPresenter*)
  │                           │                     │
  │             [1. Request/Mutate Data]    [3. Kirim HTML Rows & Total]
  │                           ▼                     ▼
  │                 Model (KontrakModel*)   View (KontrakView*)
  │                           │                     │
  │             [2. Return Entity Array]    [4. Replace Token HTML]
  │                           │                     │
  └─── [Rendered HTML] <──────┴─────────────────────┘
```

### 1. Inisialisasi & Routing (`index.php`)
`index.php` bertindak sebagai *front controller* / *entry point* tunggal yang menentukan modul aktif berdasarkan parameter query URL `?page=`:

| Method | Parameter URL / Form | Aksi yang Dijalankan |
| :--- | :--- | :--- |
| `GET` | `?page=kandidat` *(atau default tanpa parameter)* | Menampilkan tabel daftar kandidat (`tampilkanKandidat()`) |
| `GET` | `?page=kandidat&screen=add` | Menampilkan form tambah kandidat kosong (`tampilkanFormKandidat()`) |
| `GET` | `?page=kandidat&screen=edit&id={id}` | Menampilkan form ubah kandidat terisi data lama (`tampilkanFormKandidat($id)`) |
| `POST` | `?page=kandidat` dengan `action=tambah\|ubah\|hapus` | Menyimpan, memperbarui, atau menghapus kandidat lalu *redirect* ke daftar |
| `GET` | `?page=lowongan` | Menampilkan tabel daftar lowongan (`tampilkanLowongan()`) |
| `GET` | `?page=lowongan&screen=add` | Menampilkan form tambah lowongan kosong (`tampilkanFormLowongan()`) |
| `GET` | `?page=lowongan&screen=edit&id={id}` | Menampilkan form ubah lowongan terisi data lama (`tampilkanFormLowongan($id)`) |
| `POST` | `?page=lowongan` dengan `action=tambah\|ubah\|hapus` | Menyimpan, memperbarui, atau menghapus lowongan lalu *redirect* ke daftar |

### 2. Alur Read (Menampilkan Daftar Data)
1. `index.php` menginstansiasi triad MVP sesuai modul (`Tabel*`, `View*`, `Presenter*`) dan memanggil `tampilkanKandidat()` atau `tampilkanLowongan()`.
2. Presenter memanggil `getAllKandidat()` atau `getAllLowongan()` pada Model yang mengembalikan array objek Entity (`Kandidat[]` / `Lowongan[]`).
3. Presenter menghitung jumlah data dengan `count()`, merakit setiap baris HTML `<tr>` melalui `buildRowHtml()` (atau menampilkan pesan kosong jika data belum ada), lalu meneruskannya ke `renderDaftar()` pada View.
4. View memuat `skin.html` / `skin_lowongan.html` dan mengganti token `{{TABLE_ROWS}}` serta `{{TOTAL_DATA}}` sebelum dikirim ke browser.

### 3. Alur Create & Update (Tambah & Ubah Data)
1. Saat pengguna mengklik tombol **+ Tambah** (`?screen=add`), Presenter memanggil `renderForm()` dengan data kosong dan `action=tambah`.
2. Saat pengguna mengklik tombol **Edit** (`?screen=edit&id=...`), Presenter mengambil data lama melalui `getKandidatById($id)` / `getLowonganById($id)`. Jika ID ditemukan, data dipetakan ke array `$formData` untuk mengisi token *placeholder* (*prefill*) dengan `action=ubah`; jika tidak ditemukan, pengguna diarahkan kembali ke halaman daftar.
3. Saat form disubmit (`POST`), `index.php` melakukan `trim()` / casting `(int)` pada input dan memanggil `tambah...()` atau `ubah...()` pada Presenter. Presenter meneruskannya ke Model dan melakukan *redirect* kembali ke halaman daftar (*Post-Redirect-Get*).

### 4. Alur Delete (Hapus Data)
1. Pada setiap baris tabel terdapat form `POST` tersembunyi (`action=hapus` dan `id`) dengan dialog konfirmasi JavaScript (`onsubmit="return confirm(...)"`).
2. Setelah dikonfirmasi, `index.php` meneruskan `id` ke `hapusKandidat($id)` atau `hapusLowongan($id)` pada Presenter, yang memanggil `delete...($id)` pada Model lalu me-*redirect* kembali ke halaman daftar.

---

## Struktur Direktori Repositori

```text
TP9/
├── index.php                              # Entry point, dependency wiring, dan routing GET/POST
├── mvp_db.sql                             # Skema database mvp_kandidat_db & data awal (seed)
├── README.md                              # Dokumentasi proyek
├── dokumentasi/
│   └── dokumentasi_TP9.mkv                # Rekaman layar demonstrasi aplikasi (CRUD)
├── models/
│   ├── DB.php                             # Kelas dasar koneksi PDO & eksekusi prepared statement
│   ├── Kandidat.php                       # Entity class tabel kandidat
│   ├── KontrakModel.php                   # Interface contract model kandidat
│   ├── TabelKandidat.php                  # Data access layer kandidat (CRUD)
│   ├── Lowongan.php                       # Entity class tabel lowongan
│   ├── KontrakModelLowongan.php           # Interface contract model lowongan
│   └── TabelLowongan.php                  # Data access layer lowongan (CRUD)
├── presenters/
│   ├── KontrakPresenter.php               # Interface contract presenter kandidat
│   ├── PresenterKandidat.php              # Presenter logika bisnis & rakit HTML kandidat
│   ├── KontrakPresenterLowongan.php       # Interface contract presenter lowongan
│   └── PresenterLowongan.php              # Presenter logika bisnis & rakit HTML lowongan
├── views/
│   ├── KontrakView.php                    # Interface contract passive view kandidat
│   ├── ViewKandidat.php                   # Implementasi passive view kandidat
│   ├── KontrakViewLowongan.php            # Interface contract passive view lowongan
│   └── ViewLowongan.php                   # Implementasi passive view lowongan
└── template/
    ├── skin.html                          # Template daftar kandidat
    ├── form.html                          # Template form tambah/ubah kandidat
    ├── skin_lowongan.html                 # Template daftar lowongan
    └── form_lowongan.html                 # Template form tambah/ubah lowongan
```

---

## Cara Menjalankan Aplikasi

1. **Prasyarat:** Pastikan **XAMPP** (dengan **PHP 7.4+ / PHP 8.x**, Apache, dan MySQL/MariaDB) sudah terpasang.
2. Letakkan folder proyek (`TP9`) di dalam direktori web server lokal (`C:\xampp\htdocs\TP9\` atau `D:\Software\XAMPP\htdocs\TP9\`).
3. Jalankan layanan **Apache** dan **MySQL** melalui **XAMPP Control Panel**.
4. Buka `http://localhost/phpmyadmin`, lalu impor file [`mvp_db.sql`](mvp_db.sql) untuk membuat database `mvp_kandidat_db` beserta tabel `kandidat`, `lowongan`, dan data awalnya.
5. *(Opsional)* Jika konfigurasi MySQL lokal Anda berbeda dari bawaan XAMPP (`host = localhost`, `dbname = mvp_kandidat_db`, `username = root`, `password = ""`), sesuaikan kredensial pada inisialisasi `TabelLowongan` dan `TabelKandidat` di [`index.php`](index.php).
6. Akses aplikasi melalui browser pada alamat:
   ```text
   http://localhost/TP9/
   ```

---

## Dokumentasi Program

Rekaman layar (*screenrecord*) yang mendemonstrasikan seluruh fitur CRUD (**Create, Read, Update, Delete**) pada modul **Data Kandidat** dan **Data Lowongan** tersimpan di dalam folder:

* [`dokumentasi/dokumentasi_TP9.mkv`](dokumentasi/dokumentasi_TP9.mkv)
