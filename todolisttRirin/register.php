<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi password minimal 8 karakter
    if (strlen($password) < 8) {
        echo "<script>showAlert('Password harus minimal 8 karakter!');</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Enkripsi password

    // Cek apakah username sudah ada
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika username sudah terdaftar
    if ($result->num_rows > 0) {
        echo "<script>showAlert('Username sudah terdaftar!');</script>";
        exit; // Hentikan eksekusi jika username sudah ada
    }

    // Lanjutkan dengan proses pendaftaran jika username belum terdaftar
    $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ss", $username, $hashedPassword);
    $stmt->execute();
    $stmt->close();

    // Redirect atau lakukan tindakan lain setelah sukses
    echo "<script>showAlert('Registrasi berhasil!');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <style> 
    /* Body dengan background full layar */
    body {
        font-family: Arial, sans-serif;
        background: url('background1.jpg') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        flex-direction: column;
        position: relative;
    }

    /* Judul Register */
    h2 {
        color: #ff66a3; /* Warna pink */
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: bold;
    }

    /* Form register transparan dengan efek pink */
    form {
        background: rgba(255, 182, 193, 0.3); /* Pink sedikit lebih tegas */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        width: 320px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 182, 193, 0.5); /* Border pink lebih jelas */
    }

    /* Label */
    label {
        display: block;
        margin-top: 10px;
        color: #b22259;
        font-weight: bold;
        width: 100%;
        text-align: left;
    }

    /* Input */
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ff99b3;
        border-radius: 5px;
        background: rgba(255, 245, 248, 0.9);
        box-sizing: border-box;
    }

    /* Tombol */
    button {
        background: #ff66a3;
        color: white;
        border: none;
        padding: 10px;
        margin-top: 15px;
        width: 100%;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    button:hover {
        background: #ff3385;
        transform: scale(1.05);
    }

    /* Alert Popup */
    .alert-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 182, 193, 0.95); /* Pink lebih jelas */
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        text-align: center;
        z-index: 1000;
        font-weight: bold;
        color: #b22259;
    }

    .alert-popup button {
        margin-top: 10px;
        background: #ff3385;
        color: white;
        border: none;
        padding: 7px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .alert-popup button:hover {
        background: #d63384;
    }

    </style>
</head>
<body>
    <h2>Register</h2>
    <form id="registerForm" method="POST">
        <label>Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label>Password:</label>
        <input type="password" name="password" id="password" required>
        
        <label>Konfirmasi Password:</label>
        <input type="password" id="confirm_password" required>

        <button type="submit">Register</button>
        <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
    </form>

    <!-- JavaScript Validasi -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("registerForm").addEventListener("submit", function(event) {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;

            if (password.length < 8) {
                showAlert("Password harus minimal 8 karakter!");
                event.preventDefault();
                return;
            }

            if (password !== confirmPassword) {
                showAlert("Konfirmasi password tidak cocok!");
                event.preventDefault();
                return;
            }
        });
    });

    // Fungsi Popup Alert
    function showAlert(message) {
        let popup = document.createElement("div");
        popup.classList.add("alert-popup");
        popup.innerHTML = `<p>${message}</p><button onclick="this.parentElement.remove()">OK</button>`;
        document.body.appendChild(popup);
    }
    </script>
</body>
</html>
