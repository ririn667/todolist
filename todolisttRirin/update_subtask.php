<?php
include 'config.php'; // Pastikan file config.php sudah ada untuk koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subtask_id = $_POST['id'];
    $status = ($_POST['status'] == 1) ? 'Selesai' : 'Belum Selesai';

    // Update status subtugas
    $sql = "UPDATE subtasks SET status='$status' WHERE id=$subtask_id";
    if ($conn->query($sql) === TRUE) {
        // Ambil ID tugas utama dari subtugas
        $task_id_query = "SELECT task_id FROM subtasks WHERE id=$subtask_id";
        $task_id_res = $conn->query($task_id_query);
        $task_id_row = $task_id_res->fetch_assoc();
        $task_id = $task_id_row['task_id'];

        // Cek apakah semua subtugas dari tugas utama ini sudah selesai
        $check_query = "SELECT COUNT(*) AS count FROM subtasks WHERE task_id=$task_id AND status='Belum Selesai'";
        $check_res = $conn->query($check_query);
        $count_row = $check_res->fetch_assoc();

        if ($count_row['count'] == 0) {
            // Jika semua subtugas selesai, tandai tugas utama sebagai "Selesai"
            $conn->query("UPDATE tasks SET status='Selesai' WHERE id=$task_id");
        } else {
            // Jika masih ada subtugas yang belum selesai, tugas utama tetap "Belum Selesai"
            $conn->query("UPDATE tasks SET status='Belum Selesai' WHERE id=$task_id");
        }
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid_request";
}
?>