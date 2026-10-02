<?php
session_start();

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/GoogleSheets.php';

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function fmt($n): string
{
    return rtrim(rtrim(number_format((float)$n, 2, ',', '.'), '0'), ',');
}

/** Hitung rata-rata, nilai tertinggi/terendah, dan keterangan. */
function hitung(array $r, float $batas): array
{
    $poin        = [$r['p1'], $r['p2'], $r['p3']];
    $r['avg']    = ($r['p1'] + $r['p2'] + $r['p3']) / 3;
    $r['max']    = max($poin);
    $r['min']    = min($poin);
    $r['ket']    = $r['avg'] >= $batas ? 'LULUS' : 'BELUM LULUS';
    return $r;
}

function flash(?string $msg = null, string $type = 'success')
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** Validasi input form tambah/ubah. Mengembalikan [data, errors]. */
function validasi(array $in): array
{
    $err  = [];
    $nim  = trim($in['nim'] ?? '');
    $nama = trim($in['nama'] ?? '');
    $poin = [];
    if ($nim === '' || !ctype_digit($nim)) {
        $err[] = 'NIM wajib diisi dan hanya berupa angka.';
    }
    if ($nama === '') {
        $err[] = 'Nama mahasiswa wajib diisi.';
    }
    foreach (['p1' => 'Poin 1', 'p2' => 'Poin 2', 'p3' => 'Poin 3'] as $k => $label) {
        $v = $in[$k] ?? '';
        if ($v === '' || !is_numeric($v) || $v < 0 || $v > 100) {
            $err[] = "$label harus berupa angka 0 sampai 100.";
            $poin[$k] = 0;
        } else {
            $poin[$k] = (float)$v;
        }
    }
    return [['nim' => $nim, 'nama' => $nama] + $poin, $err];
}
