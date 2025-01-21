<div class="bg-light border-right" id="sidebar-wrapper">
    <div class="sidebar-heading">
        <strong>Welcome to <?= $setting['namawebsite']; ?>!</strong>
    </div>
    <div class="list-group list-group-flush">
        <a href="<?= base_url('home/dashboard') ?>" class="list-group-item list-group-item-action bg-light">Dashboard</a>
        <a href="<?= base_url('home/user') ?>" class="list-group-item list-group-item-action bg-light">User Management</a>
        <a href="<?= base_url('home/lamar') ?>" class="list-group-item list-group-item-action bg-light">Pelamaran</a>
        <a href="<?= base_url('home/karyawan') ?>" class="list-group-item list-group-item-action bg-light">Karyawan</a>
        <a href="<?= base_url('home/setting') ?>" class="list-group-item list-group-item-action bg-light">Setting</a>
        <a href="<?= base_url('home/logout') ?>" class="list-group-item list-group-item-action bg-light">Logout</a>
    </div>
</div>

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
        /* Sesuai dengan lebar sidebar */
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

    /* Link Sidebar */
    .list-group-item {
        border: none;
        padding: 15px;
        font-size: 1rem;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease;
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
    }
</style>