<?php
require_once __DIR__ . '/helpers.php';

$judul = 'Tambah Data Mahasiswa';
$nimReadonly = false;
$data   = ['nim' => '', 'nama' => '', 'p1' => '', 'p2' => '', 'p3' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $errors] = validasi($_POST);
    if (!$errors) {
        try {
            $gs = new GoogleSheets($config);
            if ($gs->findByNim($data['nim'])) {
                $errors[] = 'NIM sudah terdaftar.';
            } else {
                $gs->append($data['nim'], $data['nama'], $data['p1'], $data['p2'], $data['p3']);
                flash('Data mahasiswa berhasil ditambahkan.');
                header('Location: index.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
require __DIR__ . '/_form.php';
