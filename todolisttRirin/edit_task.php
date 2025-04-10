<?php
include 'config.php';

// Ambil data tugas berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM tasks WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        echo "Tugas tidak ditemukan!";
        exit();
    }
}

// Proses update tugas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];

    $sql = "UPDATE tasks SET name='$name', priority='$priority', deadline='$deadline' WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
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
    background: rgba(255, 182, 193, 0.8); /* Warna pink transparan */
    padding: 25px;
    border-radius: 10px;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
    width: 400px;
    text-align: center;
}
        h2 {
            color: #d63384;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            color: #d63384;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d63384;
            border-radius: 5px;
            font-size: 16px;
            background: #fff5fa;
            color: #333;
            box-sizing: border-box;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20px;
        }
        .button-group button {
            width: 48%;
            background-color: #d63384;
            color: white;
            border: none;
            padding: 12px 0;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .button-group button:hover {
            background-color: #c2185b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Tugas</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nama Tugas:</label>
                <input type="text" name="name" value="Tugas Contoh" required>
            </div>

            <div class="form-group">
                <label>Prioritas:</label>
                <select name="priority">
                    <option value="Rendah">Rendah</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Tinggi">Tinggi</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deadline:</label>
                <input type="date" name="deadline" required>
            </div>

            <div class="button-group">
                <button type="submit">Simpan</button>
                <button type="button" onclick="window.location.href='index.php'">Kembali</button>
            </div>
        </form>
    </div>
</body>
</html>
