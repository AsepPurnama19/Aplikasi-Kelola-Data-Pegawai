<?php
session_start();
if ($_SESSION['login'] != 'login') header('Location: ../index.php');
include '../../koneksi.php'; // Menghubungkan ke file koneksi.php

// Mendapatkan koleksi divisi dari MongoDB
$divisiCollection = $database->divisi;

if (isset($_GET['delete_id'])) {
  $deleteId = new MongoDB\BSON\ObjectId($_GET['delete_id']);
  $divisiCollection->deleteOne(['_id' => $deleteId]);
  $_SESSION['success'] = "Data Divisi berhasil dihapus.";
  header('Location: data_divisi');
  exit;
}

// Mengambil semua dokumen dari koleksi divisi
$divisiList = $divisiCollection->find();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../../assets/img/favicon.png">
    <title>
        Kelola Data Pegawai
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>
    <aside
        class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="https://demos.creative-tim.com/argon-dashboard/pages/dashboard.html"
                target="_blank">
                <img src="../../assets/img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold">Kelola Data Pegawai</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="data_divisi">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Divisi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="data_pegawai">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-app text-info text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Pegawai</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="data_absen">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-calendar-grid-58 text-success text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Absen</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="data_kehadiran">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-archive-2 text-info text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Kehadiran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cetak_gaji">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-paper-diploma text-info text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Cetak Gaji</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-world-2 text-danger text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
    <main class="main-content position-relative border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="false">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white"
                                href="javascript:;">Pages</a></li>
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Data Divisi</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Data Divisi</h6>
                </nav>
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Data Divisi</h6>
                            <a href="tambah_divisi" class="btn btn-primary btn-sm">Tambah Divisi</a>
                            <div class="d-flex">
                                <div class="input-group">
                                    <span class="input-group-text text-body"><i class="fas fa-search"
                                            aria-hidden="true"></i></span>
                                    <input type="text" id="searchInput" class="form-control"
                                        placeholder="Cari Berdasarkan Nama">
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid py-4">
                            <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['success']; ?>
                                <button type="button" class="btn-close" data-bs-dismis="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" id="divisiTable">
                                    <thead>
                                        <tr>
                                            <th class="mb-0 text-sm">No</th>
                                            <th class="mb-0 text-sm">Nama Divisi</th>
                                            <th class="mb-0 text-sm">Gaji Divisi</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                    $no = 1;
                    foreach ($divisiList as $divisi) {?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?php echo $no++; ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            <?php echo ucfirst($divisi['nama_divisi']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            Rp. <?php echo number_format($divisi['gaji_divisi'], 2); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <a href="edit_divisi?id=<?php echo $divisi['_id']; ?>"
                                                    class="btn btn-link text-dark px-3 mb-0" data-toggle="tooltip"
                                                    data-original-title="Edit user">
                                                    Edit
                                                </a>
                                                <a> | </a>
                                                <a href="?delete_id=<?php echo $divisi['_id']; ?>"
                                                    onclick="return confirm('Are you sure you want to delete this item?');"
                                                    class="btn btn-link text-danger text-gradient px-3 mb-0">Delete</a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Script to filter the table based on search input -->
    <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var input = this.value.toLowerCase();
        var rows = document.querySelectorAll('#divisiTable tbody tr');

        rows.forEach(function(row) {
            var namaDivisi = row.cells[1].textContent.toLowerCase();
            if (namaDivisi.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>
</body>

</html>