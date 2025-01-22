<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h3>Data Karyawan</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Karyawan</th>
                            <th>ID User</th>
                            <th>Gaji</th>
                            <th>Divisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($karyawans as $karyawan): ?>
                            <tr>
                                <td><?= $karyawan->id_karyawan ?></td>
                                <td><?= $karyawan->id_user ?> (<?= $karyawan->username ?>)</td>
                                <!-- Menampilkan ID User dan Nama User -->
                                <td><?= $karyawan->gaji ?></td>
                                <td><?= $karyawan->divisi ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $karyawan->id_karyawan ?>">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php foreach ($karyawans as $karyawan): ?>
        <!-- Modal -->
        <div class="modal fade" id="editModal<?= $karyawan->id_karyawan ?>" tabindex="-1"
            aria-labelledby="editModalLabel<?= $karyawan->id_karyawan ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/home/editKaryawan/<?= $karyawan->id_karyawan ?>" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?= $karyawan->id_karyawan ?>">Edit Karyawan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="gaji" class="form-label">Gaji</label>
                                <input type="text" class="form-control" id="gaji" name="gaji" value="<?= $karyawan->gaji ?>"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divisi</label>
                                <input type="text" class="form-control" id="divisi" name="divisi"
                                    value="<?= $karyawan->divisi ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>