<?php
$servername = "localhost"; // Ganti jika pakai server lain
$username = "root"; // Ganti jika ada username khusus
$password = ""; // Ganti sesuai password database
$database = "todolist"; // Sesuaikan dengan nama database

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>