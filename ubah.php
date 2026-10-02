<?php
require_once __DIR__ . '/helpers.php';

$judul = 'Ubah Data Mahasiswa';
$nimReadonly = true;
$errors = [];
$nim = $_GET['nim'] ?? ($_POST['nim'] ?? '');

try {
    $gs  = new GoogleSheets($config);
    $row = $gs->findByNim((string)$nim);
    if (!$row) {
        flash('Data mahasiswa tidak ditemukan.', 'danger');
        header('Location: index.php');
        exit;
    }
    $data = $row;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_POST['nim'] = $row['nim']; // NIM tidak dapat diubah
        [$data, $errors] = validasi($_POST);
        if (!$errors) {
            $gs->update($data['nim'], $data['nama'], $data['p1'], $data['p2'], $data['p3']);
            flash('Data mahasiswa berhasil diubah.');
            header('Location: index.php');
            exit;
        }
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
    $data = ['nim' => $nim, 'nama' => '', 'p1' => '', 'p2' => '', 'p3' => ''];
}
require __DIR__ . '/_form.php';
