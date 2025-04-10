<?php
session_start();
?>

<script>
    if (confirm('Anda yakin akan logout?')) {
        window.location.href = 'logout_process.php'; // Logout dilakukan di file terpisah
    } else {
        window.location.href = 'index.php'; // Jika batal, tetap di index
    }
</script>
