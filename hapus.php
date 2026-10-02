<?php
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $gs  = new GoogleSheets($config);
        $row = $gs->findByNim((string)($_POST['nim'] ?? ''));
        if ($row) {
            $gs->delete($row["nim"]);
            flash('Data mahasiswa berhasil dihapus.');
        } else {
            flash('Data mahasiswa tidak ditemukan.', 'danger');
        }
    } catch (Exception $e) {
        flash($e->getMessage(), 'danger');
    }
}
header('Location: index.php');
