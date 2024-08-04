<?php
session_start();
if ($_SESSION['login'] != 'login') {
    header('Location: ../index.php');
    exit();
}

// Include the correct path for koneksi.php based on your directory structure
include '../../koneksi.php';

// Check if $client (MongoDB client) and $database (selected database) are properly initialized
if (!isset($client) || !isset($database)) {
    $_SESSION['message'] = 'Database connection error';
    $_SESSION['message_type'] = 'danger';
    header('Location: data_pegawai.php');
    exit();
}

// Proses tambah pegawai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token Validation (assuming $_SESSION['csrf_token'] is already set)
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['message'] = 'Invalid CSRF token';
        $_SESSION['message_type'] = 'danger';
        header('Location: tambah_pegawai.php');
        exit();
    }

    // Form Data (assuming all form fields are properly validated)
    $nama_pegawai = $_POST['nama_pegawai'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $divisi = $_POST['divisi'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $nohp = $_POST['nohp'];

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

#Proses Update
if (isset($_GET['act']) && $_GET['act'] == 'edit') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $pegawai_id = $_POST['id'];
        $nama_pegawai = $_POST['nama_pegawai'];
        $divisi = $_POST['divisi'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $agama = $_POST['agama'];
        $alamat = $_POST['alamat'];
        $email = $_POST['email'];
        $nohp = $_POST['nohp'];

        $pegawaiCollection = $database->pegawai;
        $result = $pegawaiCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($pegawai_id)],
            ['$set' => [
                'nama_pegawai' => $nama_pegawai,
                'divisi' => $divisi,
                'tgl_lahir' => $tgl_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'agama' => $agama,
                'alamat' => $alamat,
                'email' => $email,
                'nohp' => $nohp,
            ]]
        );

        if ($result->getModifiedCount() > 0) {
            $_SESSION['success'] = "Berhasil Mengedit Data Pegawai";
            header('Location: ../data_pegawai');
            exit();
        } else {
            echo "Error updating data: No documents modified.";
        }
    }
}

# Proses Hapus
if (isset($_GET['act']) && $_GET['act'] == 'hapus' && isset($_GET['id'])) {
    // Sanitize the ID parameter
    $id = new MongoDB\BSON\ObjectId($_GET['id']);

    // Execute the delete operation
    $result = $pegawaiCollection->deleteOne(['_id' => $id]);

    if ($result->getDeletedCount() === 1) {
        // Set success message
        $_SESSION['success'] = "Pegawai berhasil dihapus.";
    } else {
        // Set error message
        $_SESSION['error'] = "Gagal menghapus pegawai.";
    }

    // Redirect back to the data_pegawai page
    header('Location: ../data_pegawai.php');
    exit();
}
?>
