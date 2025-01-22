<div class="bg-light border-right" id="sidebar-wrapper">
    <?php if (!empty($setting['icontab'])): ?>
        <div class="sidebar-icon">
            <img src="<?= base_url('uploads/' . $setting['iconmenu']) ?>" alt="Icon Tab">
        </div>
    <?php endif; ?>
    <div class="sidebar-heading">
        <strong>Welcome to <?= $setting['namawebsite']; ?>!</strong>
    </div>
    <div class="list-group list-group-flush">
        <!-- Menu untuk semua level -->
        <a href="<?= base_url('home/dashboard') ?>" class="list-group-item list-group-item-action bg-light">
            <i class="bi bi-house-door"></i> Dashboard
        </a>

        <?php if ($level == 'admin'): ?>
            <!-- Level 4 bisa melihat semua menu -->
            <a href="<?= base_url('home/user') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-person"></i> User Management
            </a>
            <a href="<?= base_url('home/karyawan') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-person-workspace"></i> Karyawan
            </a>
            <a href="<?= base_url('home/lowongan') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-file-earmark-plus"></i> Lowongan
            </a>
            <a href="<?= base_url('home/pelamar') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-file-earmark-check"></i> Pelamaran
            </a>
            <a href="<?= base_url('home/setting') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-gear"></i> Setting
            </a>
        <?php elseif ($level == 'HRD'): ?>
            <!-- Level 1: Dashboard, Karyawan, Lowongan, Pelamaran -->
            <a href="<?= base_url('home/karyawan') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-person-workspace"></i> Karyawan
            </a>
            <a href="<?= base_url('home/lowongan') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-file-earmark-plus"></i> Lowongan
            </a>
            <a href="<?= base_url('home/pelamar') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-file-earmark-check"></i> Pelamaran
            </a>
        <?php elseif ($level == 'Karyawan'): ?>
            <!-- Level 2: Dashboard saja -->
            <!-- Tidak ada menu tambahan untuk level 2 -->
        <?php elseif ($level == 'Pelamar'): ?>
            <!-- Level 3: Dashboard dan Lowongan -->
            <a href="<?= base_url('home/lowongan') ?>" class="list-group-item list-group-item-action bg-light">
                <i class="bi bi-file-earmark-plus"></i> Lowongan
            </a>
        <?php endif; ?>

        <!-- Menu logout selalu ditampilkan -->
        <a href="<?= base_url('home/logout') ?>" class="list-group-item list-group-item-action bg-light">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

<!-- Tambahkan link Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<style>
    /* Sidebar Styles */
    #sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 250px;
        background-color: #f8f9fa;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        overflow-y: auto;
        padding-top: 20px;
    }

    /* Konten utama bergeser sesuai lebar sidebar */
    #page-content-wrapper {
        margin-left: 250px;
        padding: 20px;
        transition: margin-left 0.2s ease-in-out;
    }

    /* Heading Sidebar */
    .sidebar-heading {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        padding: 15px 0;
        border-bottom: 1px solid #ddd;
    }

    /* Style untuk gambar ikon */
    .sidebar-icon {
        text-align: center;
        margin-bottom: 15px;
    }

    .sidebar-icon img {
        width: 200px;
        height: 100px;
    }

    /* Link Sidebar */
    .list-group-item {
        border: none;
        padding: 15px;
        font-size: 1rem;
        color: #333;
        text-decoration: none;
        display: flex;
        align-items: center;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .list-group-item i {
        margin-right: 10px;
        font-size: 1.2rem;
    }

    .list-group-item:hover {
        background-color: #007bff;
        color: #fff;
    }

    .list-group-item-action:active {
        background-color: #0056b3;
        color: #fff;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
        #sidebar-wrapper {
            width: 200px;
        }

        #page-content-wrapper {
            margin-left: 200px;
        }

        .sidebar-icon img {
            width: 60px;
            height: 60px;
        }
    }
</style>