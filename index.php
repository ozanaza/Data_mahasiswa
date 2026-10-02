<?php
require_once __DIR__ . '/helpers.php';

$batas  = (float)$config['batas_lulus'];
$error  = null;
$semua  = [];

try {
    $gs    = new GoogleSheets($config);
    $semua = array_map(fn($r) => hitung($r, $batas), $gs->getAll());
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Statistik (dihitung dari seluruh data, sebelum filter)
$total      = count($semua);
$rataSemua  = $total ? array_sum(array_column($semua, 'avg')) / $total : 0;
$jmlLulus   = count(array_filter($semua, fn($r) => $r['ket'] === 'LULUS'));
$jmlBelum   = $total - $jmlLulus;

// Filter, pencarian, dan sorting
$status = $_GET['status'] ?? 'semua';
$sort   = $_GET['sort'] ?? '';
$q      = trim($_GET['q'] ?? '');

$data = $semua;
if ($status === 'lulus') {
    $data = array_filter($data, fn($r) => $r['ket'] === 'LULUS');
} elseif ($status === 'belum') {
    $data = array_filter($data, fn($r) => $r['ket'] === 'BELUM LULUS');
}
if ($q !== '') {
    $data = array_filter($data, fn($r) => stripos($r['nama'], $q) !== false || stripos($r['nim'], $q) !== false);
}
$data = array_values($data);
if ($sort === 'asc') {
    usort($data, fn($a, $b) => $a['avg'] <=> $b['avg']);
} elseif ($sort === 'desc') {
    usort($data, fn($a, $b) => $b['avg'] <=> $a['avg']);
}

$flash = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="app-header text-white py-4 mb-4">
    <div class="container">
        <h2 class="mb-1">Aplikasi Data Mahasiswa</h2>
        <p class="mb-0 opacity-75">Pengelolaan nilai mahasiswa berbasis PHP dan Google Sheets</p>
    </div>
</div>

<div class="container pb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?> alert-dismissible fade show">
            <?= h($flash['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><strong>Terjadi kesalahan:</strong> <?= h($error) ?></div>
    <?php endif; ?>

    <!-- Ringkasan -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Jumlah Mahasiswa</div><div class="stat-value"><?= $total ?></div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Rata-rata Seluruh Mahasiswa</div><div class="stat-value"><?= fmt($rataSemua) ?></div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Lulus</div><div class="stat-value text-success"><?= $jmlLulus ?></div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Belum Lulus</div><div class="stat-value text-danger"><?= $jmlBelum ?></div></div></div>
    </div>

    <!-- Kontrol: filter, sorting, pencarian -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Filter Keterangan</label>
                    <select name="status" class="form-select">
                        <option value="semua" <?= $status === 'semua' ? 'selected' : '' ?>>Semua</option>
                        <option value="lulus" <?= $status === 'lulus' ? 'selected' : '' ?>>LULUS</option>
                        <option value="belum" <?= $status === 'belum' ? 'selected' : '' ?>>BELUM LULUS</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Urutkan Rata-rata</label>
                    <select name="sort" class="form-select">
                        <option value="" <?= $sort === '' ? 'selected' : '' ?>>Default</option>
                        <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Tertinggi ke terendah</option>
                        <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Terendah ke tertinggi</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Cari NIM / Nama</label>
                    <input type="text" name="q" class="form-control" value="<?= h($q) ?>" placeholder="Ketik kata kunci">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
                    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted">Menampilkan <strong><?= count($data) ?></strong> dari <strong><?= $total ?></strong> mahasiswa</span>
        <a href="tambah.php" class="btn btn-success">+ Tambah Mahasiswa</a>
    </div>

    <!-- Tabel data -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th><th>NIM</th><th>Nama Mahasiswa</th>
                        <th class="text-center">Poin 1</th><th class="text-center">Poin 2</th><th class="text-center">Poin 3</th>
                        <th class="text-center">Rata-rata</th><th class="text-center">Nilai Tertinggi</th><th class="text-center">Nilai Terendah</th>
                        <th class="text-center">Keterangan</th><th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$data): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">Tidak ada data yang ditampilkan.</td></tr>
                <?php endif; ?>
                <?php foreach ($data as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= h($r['nim']) ?></td>
                        <td><?= h($r['nama']) ?></td>
                        <td class="text-center"><?= fmt($r['p1']) ?></td>
                        <td class="text-center"><?= fmt($r['p2']) ?></td>
                        <td class="text-center"><?= fmt($r['p3']) ?></td>
                        <td class="text-center fw-semibold"><?= fmt($r['avg']) ?></td>
                        <td class="text-center"><?= fmt($r['max']) ?></td>
                        <td class="text-center"><?= fmt($r['min']) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $r['ket'] === 'LULUS' ? 'bg-success' : 'bg-danger' ?>"><?= h($r['ket']) ?></span>
                        </td>
                        <td class="text-center text-nowrap">
                            <a href="ubah.php?nim=<?= urlencode($r['nim']) ?>" class="btn btn-sm btn-warning">Ubah</a>
                            <form action="hapus.php" method="post" class="d-inline"
                                  onsubmit="return confirm('Hapus data <?= h(addslashes($r['nama'])) ?>?');">
                                <input type="hidden" name="nim" value="<?= h($r['nim']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <p class="text-muted small mt-3 mb-0">Ketentuan: rata-rata &ge; <?= fmt($batas) ?> dinyatakan LULUS, di bawah itu BELUM LULUS.</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
