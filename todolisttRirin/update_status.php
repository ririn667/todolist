<?php
include 'config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $sql = "UPDATE tasks SET status='$status' WHERE id=$id";

    if ($conn->query($sql)) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
