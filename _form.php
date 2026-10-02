<?php /** Partial form: variabel $judul, $data, $errors, $nimReadonly tersedia */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($judul) ?> - Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container py-5" style="max-width:600px">
    <div class="card shadow-sm border-0">
        <div class="card-header app-header text-white"><h5 class="mb-0"><?= h($judul) ?></h5></div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger"><ul class="mb-0">
                    <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                </ul></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control" value="<?= h($data['nim']) ?>" <?= $nimReadonly ? 'readonly' : '' ?> required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Mahasiswa</label>
                    <input type="text" name="nama" class="form-control" value="<?= h($data['nama']) ?>" required>
                </div>
                <div class="row">
                    <?php foreach (['p1' => 'Poin 1', 'p2' => 'Poin 2', 'p3' => 'Poin 3'] as $k => $label): ?>
                        <div class="col-4 mb-3">
                            <label class="form-label"><?= $label ?></label>
                            <input type="number" step="any" min="0" max="100" name="<?= $k ?>" class="form-control" value="<?= h($data[$k]) ?>" required>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
