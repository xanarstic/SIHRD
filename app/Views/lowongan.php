<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .job-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .job-card:hover {
            transform: translateY(-5px);
        }

        .job-header {
            display: flex;
            justify-content: space-between;
        }

        .job-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .job-company {
            color: #6c757d;
            font-size: 1rem;
        }

        .apply-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .apply-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div id="page-content-wrapper" class="container mt-5">
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

        <div class="row mb-4">
            <div class="col-md-12">
                <h3>Find Your Dream Job</h3>
                <p class="text-muted">Browse through a list of job opportunities and apply today!</p>
                <?php if ($level == 'HRD' || $level == 'admin'): ?>
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addLowonganModal">
                        Add New Lowongan
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="container mt-5">
            <!-- Search Bar -->
            <form method="get" action="/home/lowongan" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search Job Title..."
                        value="<?= esc($search) ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <!-- Job Listings -->
            <div class="row">
                <?php if (count($lowongans) > 0): ?>
                    <?php foreach ($lowongans as $lowongan): ?>
                        <div class="col-md-4">
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-title"><?= esc($lowongan->nama_lowongan) ?></div>
                                </div>
                                <div class="job-details mt-3">
                                    <p><strong>Job Requirements:</strong> <?= esc($lowongan->syarat) ?></p>
                                </div>
                                <div class="job-details mt-3">
                                    <p><strong>ID:</strong> <?= esc($lowongan->id_lowongan) ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <?php if ($level == 'Pelamar'): ?>
                                        <?php if ($lowongan->sudah_lamar): ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Already Applied (Please wait for Email)</button>
                                        <?php else: ?>
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#applyModal"
                                                data-id="<?= esc($lowongan->id_lowongan) ?>">Apply Now</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($level == 'HRD' || $level == 'admin'): ?>
                                        <!-- <button class="btn btn-warning btn-sm" data-id="<?= esc($lowongan->id_lowongan) ?>"
                                            data-bs-toggle="modal" data-bs-target="#editLowonganModal">Edit Lowongan</button> -->
                                        <a href="/home/deleteLowongan/<?= esc($lowongan->id_lowongan) ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this job?')">Close Lowongan</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted">No jobs found. Try searching for another keyword.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="/home/lowongan?page=<?= $i ?>&search=<?= esc($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>

            <!-- Modal Apply Now -->
            <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="/home/lamar" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="applyModalLabel">Apply for Job</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- ID Lowongan -->
                                <input type="hidden" id="lowongan_id" name="id_lowongan">

                                <div class="mb-3">
                                    <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" required>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" required>
                                </div>

                                <div class="mb-3">
                                    <label for="cv" class="form-label">CV</label>
                                    <input type="file" class="form-control" id="cv" name="cv" required>
                                </div>

                                <div class="mb-3">
                                    <label for="surat" class="form-label">Surat Lamaran</label>
                                    <input type="file" class="form-control" id="surat" name="surat" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Apply Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Popup untuk Menambah Lowongan -->
        <div class="modal fade" id="addLowonganModal" tabindex="-1" aria-labelledby="addLowonganModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/home/tambahLowongan" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLowonganModalLabel">Tambah Lowongan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_lowongan" class="form-label">Nama Lowongan</label>
                                <input type="text" class="form-control" id="nama_lowongan" name="nama_lowongan" required>
                            </div>
                            <div class="mb-3">
                                <label for="syarat" class="form-label">Syarat</label>
                                <input type="text" class="form-control" id="syarat" name="syarat" required>
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
        <!-- Modal Edit Lowongan -->
        <div class="modal fade" id="editLowonganModal" tabindex="-1" aria-labelledby="editLowonganModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/home/editLowongan" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLowonganModalLabel">Edit Lowongan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- ID Lowongan -->
                            <input type="hidden" id="edit_lowongan_id" name="id_lowongan">

                            <div class="mb-3">
                                <label for="edit_nama_lowongan" class="form-label">Nama Lowongan</label>
                                <input type="text" class="form-control" id="edit_nama_lowongan" name="nama_lowongan"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_syarat" class="form-label">Syarat</label>
                                <textarea class="form-control" id="edit_syarat" name="syarat" rows="3" required></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // Memasukkan id_lowongan ke dalam modal saat tombol "Apply Now" diklik
            const applyButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
            applyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const lowonganId = this.getAttribute('data-id');
                    document.getElementById('lowongan_id').value = lowonganId;
                });
            });

            // Memasukkan data lowongan yang dipilih ke dalam modal saat tombol "Edit" diklik
            const editButtons = document.querySelectorAll('.btn-warning');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const lowonganId = this.getAttribute('data-id');

                    // Ambil data lowongan dari server (misalnya menggunakan AJAX atau fetch)
                    fetch(`/home/getLowongan/${lowonganId}?t=${new Date().getTime()}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to fetch lowongan data');
                            }
                            return response.json();
                        })
                        .then(data => {
                            document.getElementById('edit_lowongan_id').value = data.id_lowongan;
                            document.getElementById('edit_nama_lowongan').value = data.nama_lowongan;
                            document.getElementById('edit_syarat').value = data.syarat;
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        });

                });
            });
        </script>
</body>

</html>