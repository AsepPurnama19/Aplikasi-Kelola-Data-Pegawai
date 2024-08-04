<?php
session_start();
include '../../../koneksi.php';

# Proses Tambah
if (isset($_GET['act']) && $_GET['act'] == 'tambah') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $nama_divisi = $_POST['nama_divisi'];
        $gaji_divisi = $_POST['gaji_divisi'];

        $divisiCollection = $database->divisi;
        $result = $divisiCollection->insertOne([
            'nama_divisi' => $nama_divisi,
            'gaji_divisi' => $gaji_divisi,
        ]);
        $_SESSION['success'] = "Berhasil Menambahkan Data Divisi";
        header('Location: ../data_divisi');
        exit();
    }
}

#Proses Update
if (isset($_GET['act']) && $_GET['act'] == 'edit') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $divisi_id = $_POST['id'];
        $nama_divisi = $_POST['nama_divisi'];
        $gaji_divisi = $_POST['gaji_divisi'];

        $divisiCollection = $database->divisi;
        $result = $divisiCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($divisi_id)],
            ['$set' => [
                'nama_divisi' => $nama_divisi,
                'gaji_divisi' => $gaji_divisi,
            ]]
        );

        if ($result->getModifiedCount() > 0) {
            $_SESSION['success'] = "Berhasil Mengedit Data Divisi";
            header('Location: ../data_divisi.php');
            exit();
        } else {
            echo "Error updating data: No documents modified.";
        }
    }
}

# Proses Hapus
if (isset($_GET['act']) && $_GET['act'] == 'hapus') {
    if (isset($_GET['id'])) {
        $divisiCollection = $database->divisi;

        try {
            $objectId = new MongoDB\BSON\ObjectId($_GET['id']);
            $result = $divisiCollection->deleteOne(['_id' => $objectId]);

            if ($result->getDeletedCount() > 0) {
                $_SESSION['success'] = "Berhasil Menghapus Data Divisi";
                header('Location: ../data_divisi');
            } else {
                echo "Error deleting document";
            }
        } catch (MongoDB\Driver\Exception\InvalidArgumentException $e) {
            echo "Invalid ObjectId string";
        }
        exit();
    }
}
?>


