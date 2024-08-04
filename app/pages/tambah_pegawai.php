<?php 
session_start(); 
if ($_SESSION['login'] != 'login') {
    header('Location: ../index.php');
    exit();
}
include '../../koneksi.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Fetch division data from the database
$divisiCollection = $database->divisi;
$divisiData = $divisiCollection->find()->toArray();

// Proses tambah pegawai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token Validation
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = 'Invalid CSRF token';
        $_SESSION['message_type'] = 'danger';
        header('Location: tambah_pegawai.php');
        exit();
    }

    // Form Data
    $nama_pegawai = $_POST['nama_pegawai'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $divisi = $_POST['divisi'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $nohp = $_POST['nohp'];

    // Data Validation (contoh validasi email dan nohp)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Invalid email format';
        $_SESSION['message_type'] = 'danger';
        header('Location: tambah_pegawai.php');
        exit();
    }

    if (!preg_match('/^\d+$/', $nohp)) {
        $_SESSION['message'] = 'Invalid phone number';
        $_SESSION['message_type'] = 'danger';
        header('Location: tambah_pegawai.php');
        exit();
    }

    // Simpan data ke MongoDB
    $pegawaiCollection = $database->pegawai;
    $result = $pegawaiCollection->insertOne([
        'nama_pegawai' => $nama_pegawai,
        'tgl_lahir' => $tgl_lahir,
        'jenis_kelamin' => $jenis_kelamin,
        'agama' => $agama,
        'divisi' => $divisi,
        'alamat' => $alamat,
        'email' => $email,
        'nohp' => $nohp,
    ]);

    if ($result->getInsertedCount() > 0) {
        $_SESSION['message'] = 'Data Pegawai berhasil ditambahkan';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error: Gagal menambahkan data pegawai';
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: data_pegawai.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kelola Data Pegawai</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous">
    </script>
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link id="pagestyle" href="../../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>
    <aside
        class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
        id="sidenav-main">
        <div class="sidenav-header">
            <a class="navbar-brand m-0" href="#">
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
                    <a class="nav-link" href="data_divisi">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Divisi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="data_pegawai">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-app text-info text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Pegawai</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="data_absen">
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
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="false">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Tambah Pegawai</h6>
                </nav>
            </div>
        </nav>
        <div class="container-fluid py-4">
            <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-md-8 d-flex align-items-center">
                            <h6 class="mb-0">Tambah Pegawai</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <hr class="horizontal dark mt-0">
                    <form role="form" action="tambah_pegawai.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="mb-3">
                            <label class="form-control-label">Nama Pegawai</label>
                            <input type="text" class="form-control" name="nama_pegawai" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">Divisi</label>
                            <select class="form-control" name="divisi" required>
                                <option value="" disabled selected>Pilih Divisi</option>
                                <?php foreach ($divisiData as $divisi): ?>
                                <option value="<?= $divisi['nama_divisi'] ?>"><?= $divisi['nama_divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tgl_lahir" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">Jenis Kelamin</label>
                            <select class="form-control" name="jenis_kelamin" required>
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="Laki - Laki">Laki - Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">Agama</label>
                            <select class="form-control" name="agama" required>
                                <option value="" disabled selected>Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                                <option value="Khonghucu">Khonghucu</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">Alamat</label>
                            <input type="text" class="form-control" name="alamat" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-control-label">No Hp</label>
                            <input type="text" class="form-control" name="nohp" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100 mt-4">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!--   Core JS Files   -->
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../../assets/js/argon-dashboard.min.js?v=2.0.4"></script>
</body>

</html>