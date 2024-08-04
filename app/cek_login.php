<?php
session_start();

require '../koneksi.php';

$userCollection = $database->users; // Collection name in MongoDB

$username = $_POST['username']; // Capture username
$password = $_POST['password']; // Capture password

try {
    // Query MongoDB to find data matching the login credentials
    $result = $userCollection->findOne(['username' => $username, 'password' => $password]);

    if ($result) {
        $_SESSION['username'] = $result['username'];
        $_SESSION['password'] = $result['password'];
        $_SESSION['login'] = 'login';

        header('Location: pages/');
    } else {
        $_SESSION['msg'] = 1;
        header('Location: index.php');
    }
} catch (Exception $e) {
    die('Error querying MongoDB: ' . $e->getMessage());
}
?>
