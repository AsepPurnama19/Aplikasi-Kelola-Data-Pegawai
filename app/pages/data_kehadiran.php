<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'login') {
  header('Location: ../index.php');
  exit;
}

include '../../koneksi.php';

$absenCollection = $database->absen;
$pegawaiCollection = $database->pegawai;

$bulan = $_GET['bulan'] ?? date('Y-m');
$pegawaiList = $pegawaiCollection->find([]);

$attendanceSummary = [];
foreach ($pegawaiList as $pegawai) {
  $pegawai_id = $pegawai['_id'];
  $summary = [
    'nama_pegawai' => $pegawai['nama_pegawai'],
    'divisi' => $pegawai['divisi'],
    'Hadir' => 0,
    'Sakit' => 0,
    'Izin' => 0,
    'Alfa' => 0
  ];

  $attendanceRecords = $absenCollection->find([
    'pegawai_id' => $pegawai_id,
    'tanggal' => ['$regex' => '^' . $bulan]
  ]);

  foreach ($attendanceRecords as $record) {
    $status = $record['status'];
    if (isset($summary[$status])) {
      $summary[$status]++;
    }
  }

  $attendanceSummary[] = $summary;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../../assets/img/favicon.png">
  <title>Kelola Data Kehadiran</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="../../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
</head>
<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="dashboard.html" target="_blank">
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
                    <a class="nav-link" href="data_pegawai">
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
                    <a class="nav-link active" href="data_kehadiran">
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
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Data Kehadiran Pegawai</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Data Kehadiran Pegawai</h6>
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
        <div class="col-lg-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET">
                <div class="form-group">
                  <label for="bulan">Pilih Bulan</label>
                  <input type="month" class="form-control" id="bulan" name="bulan" value="<?php echo $bulan; ?>" required>
                  <button type="submit" class="btn btn-primary btn-sm mt-2">Tampilkan Data</button>
                  <div class="d-flex">
                <div class="input-group">
                  <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                  <input type="text" id="searchInput" class="form-control" placeholder="Cari Berdasarkan Nama">
                </div>
              </div>
                </div>
              </form>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0" id="kehadiranTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama Pegawai</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Divisi</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Hadir</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Sakit</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Izin</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Alfa</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($attendanceSummary as $summary): ?>
                      <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo ucfirst($summary['nama_pegawai']); ?></td>
                        <td><?php echo ucfirst($summary['divisi']); ?></td>
                        <td><?php echo $summary['Hadir']; ?></td>
                        <td><?php echo $summary['Sakit']; ?></td>
                        <td><?php echo $summary['Izin']; ?></td>
                        <td><?php echo $summary['Alfa']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../../assets/js/argon-dashboard.min.js?v=2.0.4"></script>
  <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
      var input = this.value.toLowerCase();
      var rows = document.querySelectorAll('#kehadiranTable tbody tr');

      rows.forEach(function(row) {
        var namaPegawai = row.cells[1].textContent.toLowerCase();
        if (namaPegawai.includes(input)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>
