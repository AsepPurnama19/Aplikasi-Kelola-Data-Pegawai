<?php

require 'vendor/autoload.php'; // Load Composer's autoloader

use MongoDB\Client;

try {
    // Replace the connection string and database name with your actual MongoDB connection string and database name
    $mongoClient = new Client("mongodb://127.0.0.1:27017");
    $database = $mongoClient->selectDatabase('pegawai');
} catch (Exception $e) {
    die('Error connecting to MongoDB server: ' . $e->getMessage());
}
?>
