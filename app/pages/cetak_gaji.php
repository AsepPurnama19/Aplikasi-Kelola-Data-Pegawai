<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'login') {
  header('Location: ../index.php');
  exit;
}

require_once '../../vendor/autoload.php'; // Adjust the path as per your project structure

include '../../koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$absenCollection = $database->absen;
$pegawaiCollection = $database->pegawai;
$divisiCollection = $database->divisi;

$bulan = $_GET['bulan'] ?? date('Y-m');
$pegawaiList = $pegawaiCollection->find([]);

$attendanceSummary = [];
$totalGajiBersih = 0;

foreach ($pegawaiList as $pegawai) {
    $pegawai_id = $pegawai['_id'];
    $divisi = $pegawai['divisi'];
   
    // Ambil gaji divisi dari koleksi divisi
    $divisiData = $divisiCollection->findOne(['nama_divisi' => $divisi]);
    if (!$divisiData || !isset($divisiData['gaji_divisi'])) {
        // Jika data divisi atau gaji divisi tidak ditemukan, lanjutkan ke pegawai berikutnya
        continue;
    }
    $gaji_divisi = $divisiData['gaji_divisi'];

    $summary = [
        'nama_pegawai' => $pegawai['nama_pegawai'],
        'divisi' => $divisi,
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

    // Potongan 0,5% untuk setiap hari alfa
    $potongan_alfa = 0.005 * $summary['Alfa'] * $gaji_divisi;

    // Hitung gaji bersih
    $gaji_bersih = $gaji_divisi - $potongan_alfa;

    $summary['Potongan Alfa'] = $potongan_alfa;
    $summary['Gaji Bersih'] = $gaji_bersih;

    $totalGajiBersih += $gaji_bersih;
    $attendanceSummary[] = $summary;
}

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml(generatePdfContent($attendanceSummary, $totalGajiBersih, $bulan));
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_gaji.pdf", array("Attachment" => false));

function generatePdfContent($attendanceSummary, $totalGajiBersih, $bulan) {
  ob_start();
  ?>
  <html>
  <head>
    <style>
      body {
        font-family: Arial, sans-serif;
      }
      .header {
        text-align: center;
        margin-bottom: 20px;
      }
      .header h1 {
        margin: 0;
      }
      .header p {
        margin: 0;
        font-size: 14px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }
      table, th, td {
        border: 1px solid black;
      }
      th, td {
        padding: 8px;
        text-align: center;
      }
      .total-row {
        font-weight: bold;
      }
      .total-cell {
        text-align: right;
      }
    </style>
  </head>
  <body>
    <div class="header">
      <h1>Perusahaan</h1>
      <p>Laporan Gaji Pegawai - Bulan <?php echo date('F Y', strtotime($bulan)); ?></p>
      <p>----------------------------------------------------------------------------------------------------------------------------------------------------</p>
    </div>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Pegawai</th>
          <th>Divisi</th>
          <th>Hadir</th>
          <th>Sakit</th>
          <th>Izin</th>
          <th>Alfa</th>
          <th>Potongan Alfa</th>
          <th>Gaji Bersih</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($attendanceSummary as $key => $summary): ?>
          <tr>
            <td><?php echo $key + 1; ?></td>
            <td><?php echo ucfirst($summary['nama_pegawai']); ?></td>
            <td><?php echo ucfirst($summary['divisi']); ?></td>
            <td><?php echo $summary['Hadir']; ?></td>
            <td><?php echo $summary['Sakit']; ?></td>
            <td><?php echo $summary['Izin']; ?></td>
            <td><?php echo $summary['Alfa']; ?></td>
            <td><?php echo number_format($summary['Potongan Alfa'], 2); ?></td>
            <td><?php echo number_format($summary['Gaji Bersih'], 2); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="total-row">
          <td colspan="8" class="total-cell">Total Gaji Bersih yang Harus Dibayarkan</td>
          <td><?php echo number_format($totalGajiBersih, 2); ?></td>
        </tr>
      </tbody>
    </table>
  </body>
  </html>
  <?php
  return ob_get_clean();
}
?>
