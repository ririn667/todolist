<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Ambil user_id dari sesi
    $name = $_POST['name'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $subtasks = isset($_POST['subtasks']) ? $_POST['subtasks'] : [];

    // Validasi Priority
    $valid_priorities = ["Low", "Medium", "High"];
    if (!in_array($priority, $valid_priorities)) {
        die("Error: Prioritas tidak valid.");
    }

    // Gunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, name, priority, deadline, status) VALUES (?, ?, ?, ?, 'Belum Selesai')");
    $stmt->bind_param("isss", $user_id, $name, $priority, $deadline);

    if ($stmt->execute()) {
        $task_id = $conn->insert_id;

        // Tambahkan subtugas
        $stmt_subtask = $conn->prepare("INSERT INTO subtasks (task_id, name, status) VALUES (?, ?, 'Belum Selesai')");
        foreach ($subtasks as $subtask_name) {
            if (!empty(trim($subtask_name))) {
                $stmt_subtask->bind_param("is", $task_id, $subtask_name);
                $stmt_subtask->execute();
            }
        }

        echo "<script>alert('Tugas berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="style.css">
    <style>
    body {
    font-family: Arial, sans-serif;
    background: url('background1.jpg') no-repeat center center fixed; 
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
}
.container {
    background: rgba(245, 234, 235, 0.8); /* Warna pink transparan */
    padding: 25px;
    border-radius: 10px;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
    width: 400px;
    text-align: center;
}


h2 {
    color: #d63384;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 15px;
}

.form-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

label {
    font-weight: bold;
    color: #d63384;
    align-self: flex-start;
    margin-bottom: 5px;
}

.input-field, select, .btn {
    padding: 10px;
    border: 2px solid #ff66a3;
    border-radius: 5px;
    width: 100%;
    font-size: 16px;
    box-sizing: border-box; /* Agar padding tidak mempengaruhi ukuran */
}

.input-field {
    display: block;
}

#subtask-container {
    width: 100%;
}

.button-container {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 10px;
}

.btn {
    background: #ff66a3;
    color: white;
    border: none;
    font-weight: bold;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    text-align: center;
    font-size: 16px;
    display: block;
    margin-top: 10px;
}

.btn:hover {
    background: #ff3385;
}



    </style>
    <script>
        function addSubtaskField() {
            let container = document.getElementById("subtask-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "subtasks[]";
            input.placeholder = "Nama Subtugas";
            input.className = "input-field"; // Tambahkan class agar sesuai dengan CSS
            container.appendChild(input);
            container.appendChild(document.createElement("br"));
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Tambah Tugas</h2>
        <form method="post" class="form-box">
            <label>Nama Tugas:</label>
            <input type="text" name="name" class="input-field" required>

            <label>Prioritas:</label>
            <select name="priority" class="input-field">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>

            <label>Deadline:</label>
            <input type="date" name="deadline" class="input-field" required>

            <label>Subtugas:</label>
            <div id="subtask-container">
                <input type="text" name="subtasks[]" class="input-field" placeholder="Nama Subtugas">
            </div>
            
        <button class="btn">Simpan</button>
        <a href="index.php" class="btn">Kembali</a>

        </form>
    </div>
</body>
</html>