<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['task_id'];
    $subtask_name = $_POST['subtask_name'];

    // Cek apakah tugas ini milik user yang sedang login
    $stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if (!empty(trim($subtask_name))) {
            $stmt_insert = $conn->prepare("INSERT INTO subtasks (task_id, name, status) VALUES (?, ?, 'Belum Selesai')");
            $stmt_insert->bind_param("is", $task_id, $subtask_name);
            if ($stmt_insert->execute()) {
                echo "<script>alert('Subtugas berhasil ditambahkan!'); window.location.href='index.php';</script>";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        echo "<script>alert('Anda tidak memiliki akses ke tugas ini!'); window.location.href='index.php';</script>";
    }
}

// Ambil daftar tugas hanya milik user yang login
$stmt_tasks = $conn->prepare("SELECT id, name FROM tasks WHERE user_id = ?");
$stmt_tasks->bind_param("i", $user_id);
$stmt_tasks->execute();
$tasks = $stmt_tasks->get_result();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Subtugas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
    font-family: 'Poppins', sans-serif;
    background: url('background1.jpg') no-repeat center center fixed; /* Ganti dengan nama file gambar */
    background-size: cover;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background:  rgba(245, 234, 235, 0.8);  /* Warna pink transparan */
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
    width: 350px;
    text-align: center;
    backdrop-filter: blur(8px); /* Efek blur di belakang */
}


        h2 {
            color: #d63384;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: #d63384;
            margin-bottom: 5px;
        }

        .input-field {
            padding: 12px;
            border: 1px solid #ffb3c1;
            border-radius: 8px;
            background-color: #fff;
            color: #d63384;
            width: 100%;
            box-sizing: border-box;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 10px; /* Tambahkan jarak antara tombol */
        }

        .submit-btn, .back-btn {
            background-color: #ff85a2;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            width: 50%; /* Biarkan tombol tetap proporsional */
            transition: 0.3s;
        }

        .submit-btn:hover, .back-btn:hover {
            background-color: #ff6384;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Subtugas</h2>
        <form method="post" class="form-box">
            <div class="form-group">
                <label for="task_id">Pilih Tugas:</label>
                <select name="task_id" id="task_id" required class="input-field">
                    <?php while ($row = $tasks->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subtask_name">Nama Subtugas:</label>
                <input type="text" name="subtask_name" id="subtask_name" class="input-field" required>
            </div>

            <div class="button-group">
                <button type="submit" class="submit-btn">Tambah</button>
                <a href="index.php" class="back-btn">Kembali</a>
            </div>
        </form>
    </div>
    
</body>
</html>
