<?php
require_once __DIR__ . '/KontrakViewLowongan.php';

class ViewLowongan implements KontrakViewLowongan {
    private string $skinPath;
    private string $formPath;

    // Menentukan lokasi file template HTML
    public function __construct(string $skinPath = '', string $formPath = '') {
        $this->skinPath = $skinPath ?: __DIR__ . '/../template/skin_lowongan.html';
        $this->formPath = $formPath ?: __DIR__ . '/../template/form_lowongan.html';
    }

    // Merender daftar lowongan dengan mengganti placeholder tanpa loop
    public function renderDaftar(string $tableRowsHtml, int $totalData): string {
        $template = file_get_contents($this->skinPath);

        $tokens = [
            '{{TABLE_ROWS}}' => $tableRowsHtml,
            '{{TOTAL_DATA}}' => (string)$totalData,
        ];

        return str_replace(array_keys($tokens), array_values($tokens), $template);
    }

    // Merender form tambah/edit lowongan dengan prefill data
    public function renderForm(string $title, string $action, array $formData = []): string {
        $template = file_get_contents($this->formPath);
        $status = $formData['status'] ?? 'Dibuka';

        $tokens = [
            '{{FORM_TITLE}}'      => htmlspecialchars($title),
            '{{ACTION}}'          => htmlspecialchars($action),
            '{{ID}}'              => htmlspecialchars((string)($formData['id'] ?? '')),
            '{{JUDUL_POSISI}}'    => htmlspecialchars($formData['judulPosisi'] ?? ''),
            '{{DEPARTEMEN}}'      => htmlspecialchars($formData['departemen'] ?? ''),
            '{{LOKASI}}'          => htmlspecialchars($formData['lokasi'] ?? ''),
            '{{KUOTA}}'           => htmlspecialchars((string)($formData['kuota'] ?? 1)),
            '{{SELECTED_DIBUKA}}' => ($status === 'Dibuka') ? 'selected' : '',
            '{{SELECTED_DITUTUP}}'=> ($status === 'Ditutup') ? 'selected' : '',
        ];

        return str_replace(array_keys($tokens), array_values($tokens), $template);
    }
}
?>