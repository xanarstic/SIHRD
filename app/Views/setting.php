<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengaturan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div id="page-content-wrapper">
        <div class="container mt-5">
            <h3>Setting</h3>

            <!-- Menampilkan pesan sukses -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Menampilkan error jika ada -->
            <?php if (isset($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form Edit Pengaturan -->
            <form action="/home/updatesetting" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="namawebsite" class="form-label">Nama Website</label>
                    <input type="text" class="form-control" id="namawebsite" name="namawebsite"
                        value="<?= old('namawebsite', $setting['namawebsite']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="icontab" class="form-label">Icon Tab (PNG/JPEG)</label>
                    <input type="file" class="form-control" id="icontab" name="icontab">
                    <?php if (!empty($setting['icontab'])): ?>
                        <p>Current Icon: <img src="<?= base_url('uploads/' . $setting['icontab']) ?>" alt="Icon Tab"
                                width="50"></p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="iconlogin" class="form-label">Icon Login (PNG/JPEG)</label>
                    <input type="file" class="form-control" id="iconlogin" name="iconlogin">
                    <?php if (!empty($setting['iconlogin'])): ?>
                        <p>Current Icon: <img src="<?= base_url('uploads/' . $setting['iconlogin']) ?>" alt="Icon Login"
                                width="50"></p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="iconmenu" class="form-label">Icon Menu (PNG/JPEG)</label>
                    <input type="file" class="form-control" id="iconmenu" name="iconmenu">
                    <?php if (!empty($setting['iconmenu'])): ?>
                        <p>Current Icon: <img src="<?= base_url('uploads/' . $setting['iconmenu']) ?>" alt="Icon Menu"
                                width="50"></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Update Pengaturan</button>
            </form>
        </div>
    </div>
</body>

</html>