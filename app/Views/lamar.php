<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelamar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* CSS untuk memastikan gambar resize secara otomatis */
        .modal-img {
            display: block;
            max-width: 100%;
            max-height: 80vh;
            /* Maksimal tinggi modal */
            margin: 0 auto;
            object-fit: contain;
            /* Menyesuaikan gambar landscape atau portrait */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-8">
                <h3>Data Pelamar</h3>
            </div>
            <div class="col-md-4">
                <form method="get" action="">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= esc($search) ?>">
                        <select name="filter" class="form-select">
                            <option value="id_user" <?= $filter == 'id_user' ? 'selected' : '' ?>>Username</option>
                            <option value="id_lowongan" <?= $filter == 'id_lowongan' ? 'selected' : '' ?>>ID Lowongan</option>
                            <option value="nama_user" <?= $filter == 'nama_user' ? 'selected' : '' ?>>Nama User</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter Dropdown for Status -->
        <form method="get" action="">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Diterima" <?= $status == 'Diterima' ? 'selected' : '' ?>>Diterima</option>
                        <option value="Ditolak" <?= $status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pelamar</th>
                    <th>ID User</th>
                    <th>ID Lowongan</th>
                    <th>Tanggal Lahir</th>
                    <th>Alamat</th>
                    <th>CV</th>
                    <th>Surat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pelamars)): ?>
                    <?php foreach ($pelamars as $pelamar): ?>
                        <tr>
                            <td><?= $pelamar['id_pelamar'] ?></td>
                            <td><?= $pelamar['id_user'] ?> (<?= $pelamar['username'] ?>)</td>
                            <td><?= $pelamar['id_lowongan'] ?>
                                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#lowonganModal"
                                    data-id="<?= $pelamar['id_lowongan'] ?>">
                                    Lihat Lowongan
                                </button>
                            </td>
                            <td><?= $pelamar['tgl_lahir'] ?></td>
                            <td><?= $pelamar['alamat'] ?></td>
                            <td>
                                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#cvModal"
                                    data-url="/<?= $pelamar['cv'] ?>">Lihat CV</button>
                            </td>
                            <td>
                                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#suratModal"
                                    data-url="/<?= $pelamar['surat'] ?>">Lihat Surat</button>
                            </td>
                            <td>
                                <span class="btn btn-sm <?= $pelamar['status'] == 'Pending' ? 'btn-warning' : ($pelamar['status'] == 'Diterima' ? 'btn-success' : 'btn-danger') ?>">
                                    <?= $pelamar['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($pelamar['status'] == 'Pending'): ?>
                                    <form action="/home/updateStatusPelamar" method="POST" class="d-inline">
                                        <input type="hidden" name="id_pelamar" value="<?= $pelamar['id_pelamar'] ?>">
                                        <button type="submit" name="status" value="Diterima" class="btn btn-success btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menerima pelamar ini?')">Accept</button>
                                        <button type="submit" name="status" value="Ditolak" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menolak pelamar ini?')">Decline</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Data tidak ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?search=<?= esc($search) ?>&filter=<?= esc($filter) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>


    <!-- Modal for Lowongan -->
    <div class="modal fade" id="lowonganModal" tabindex="-1" aria-labelledby="lowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lowonganModalLabel">Detail Lowongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama Lowongan:</strong> <span id="nama_lowongan"></span></p>
                    <p><strong>Syarat:</strong></p>
                    <p id="syarat"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CV Modal -->
    <div class="modal fade" id="cvModal" tabindex="-1" aria-labelledby="cvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cvModalLabel">CV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="cvImage" class="modal-img" src="" alt="CV">
                </div>
            </div>
        </div>
    </div>

    <!-- Surat Modal -->
    <div class="modal fade" id="suratModal" tabindex="-1" aria-labelledby="suratModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suratModalLabel">Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="suratImage" class="modal-img" src="" alt="Surat">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load the CV or Surat into the image tag in the modal
        document.addEventListener('DOMContentLoaded', function() {
            var cvModal = document.getElementById('cvModal');
            var suratModal = document.getElementById('suratModal');

            cvModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var image = cvModal.querySelector('img');
                image.src = url;
            });

            suratModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var image = suratModal.querySelector('img');
                image.src = url;
            });

            cvModal.addEventListener('hidden.bs.modal', function() {
                var image = cvModal.querySelector('img');
                image.src = '';
            });

            suratModal.addEventListener('hidden.bs.modal', function() {
                var image = suratModal.querySelector('img');
                image.src = '';
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const lowonganModal = document.getElementById('lowonganModal');

            // Event listener for modal trigger buttons
            lowonganModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // Button that triggered the modal
                const idLowongan = button.getAttribute('data-id'); // Extract info from data-id attribute

                // Fetch lowongan data via AJAX
                fetch('/home/getLowonganById', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest' // Indicate AJAX request
                        },
                        body: JSON.stringify({
                            id_lowongan: idLowongan
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // Debug response data
                        document.getElementById('nama_lowongan').textContent = data.nama_lowongan;
                        document.getElementById('syarat').textContent = data.syarat;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
    </div>
</body>

</html>