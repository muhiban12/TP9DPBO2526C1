<?php
// Kontrak View untuk Lowongan (Strict Passive View tanpa dependensi Model/Entity)
interface KontrakViewLowongan {
    public function renderDaftar(string $tableRowsHtml, int $totalData): string;
    public function renderForm(string $title, string $action, array $formData = []): string;
}
?>