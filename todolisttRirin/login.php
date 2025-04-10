<?php
session_start();

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; // Simpan ID user
            $_SESSION['user'] = $row['username']; // Simpan username
            $_SESSION['login_success'] = true; // Tambahkan session untuk popup sukses

            echo "<script>
                    alert('Anda berhasil login!');
                    window.location.href='index.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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

/* Judul */
h2 {
    color: #d63384;
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
}

/* Form login transparan dengan sedikit pink */
form {
    background: rgba(255, 192, 203, 0.2); /* Pink sangat transparan */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    width: 300px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    backdrop-filter: blur(10px); /* Efek blur untuk estetika */
    border: 1px solid rgba(255, 192, 203, 0.4); /* Border pink samar */
}


/* Label */
label {
    display: block;
    margin-top: 10px;
    color: #6d214f;
    font-weight: bold;
    width: 100%;
    text-align: left;
}

/* Input dan tombol dengan efek lebih lembut */
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ff66a3;
    border-radius: 5px;
    background: rgba(255, 245, 248, 0.8); /* Sedikit transparan */
    box-sizing: border-box;
}

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

/* Link ke Register */
p {
    margin-top: 10px;
    font-size: 14px;
    color: #6d214f;
}

a {
    color: #d63384;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

/* Popup Alert */
.alert-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 204, 213, 0.8); /* Transparan pink */
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.alert-popup button {
    margin-top: 10px;
    background: #ff3385;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

.alert-popup button:hover {
    background: #d63384;
}

</style>
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</body>
</html>
