<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';


$user_id = $_SESSION['user_id']; // Ambil user_id dari session
$query = "SELECT * FROM tasks where user_id = '$user_id' ORDER BY FIELD(priority, 'High', 'Medium', 'Low')";
$result = mysqli_query($conn, $query);
$today = date("Y-m-d");

function getSubtasks($task_id, $conn) {
    $subtasks = [];
    $sql = "SELECT * FROM subtasks WHERE task_id = $task_id";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $subtasks[] = $row;
    }
    return $subtasks;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color:rgb(255, 202, 223);
            text-align: center;
        }
        .btn {
            background: #ff66a3;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        .btn:hover {
            background: #ff3385;
        }
        .task-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            padding: 20px;
        }
        .task-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            padding: 15px;
            width: 250px;
            min-height: 180px;
            border-radius: 10px;
            background: #ffb3d9;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .task-header {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 5px; /* Jarak antara checkbox dan judul */
            justify-content: flex-start; /* Pastikan judul ada di samping checkbox */
        }
        .task-checkbox {
            width: 20px;
            height: 20px;
        }

        .task-card:hover {
            transform: scale(1.05);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
        }
        .task-title {
            font-size: 18px;
            font-weight: bold;
            color: #6d214f;
            text-align: left; /* Judul tetap di kiri */
        }
        .subtask-list {
            width: 100%;
            margin-top: 5px;
            text-align: left; /* Agar subtugas ada di kiri */
        }
        .subtask-item {
            display: flex;
            align-items: center;
            gap: 5px;
            padding-left: 0px; /* Pastikan subtugas tetap di sisi kiri */
        }
        .task-info {
            font-size: 14px;
            color: #6d214f;
        }
        .task-actions {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 10px;
        }
        .edit-btn, .delete-btn {
            text-decoration: none;
            font-size: 18px;
            color: #d63384;
            font-weight: bold;
        }
        .edit-btn:hover, .delete-btn:hover {
            color: #ff3385;
        }
        h2 {
            color: #ff66a3;
        }
        .priority-high {
            color: red;
            font-weight: bold;
        }

        .priority-medium {
            color: orange;
            font-weight: bold;
        }

        .priority-low {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>To-Do List</h2>
    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['user']); ?>!</p>
    <a href="logout.php" class="btn logout-btn">Logout</a>
    <a href="add_task.php" class="btn">Tambah Tugas</a>
    
    <div class="task-list">
        <?php while ($row = $result->fetch_assoc()) { 
            $isLate = ($row['status'] !== 'Selesai' && $row['deadline'] < $today);
            $subtasks = getSubtasks($row['id'], $conn);
            $allSubtasksDone = count($subtasks) > 0 && !in_array("Belum Selesai", array_column($subtasks, 'status'));
            $taskCompleted = ($row['status'] == 'Selesai' || $allSubtasksDone);
            $priorityClass = strtolower($row['priority']) == 'high' ? 'priority-high' :
                 (strtolower($row['priority']) == 'medium' ? 'priority-medium' : 'priority-low');
?>
    
    <div class="task-card">
    <div class="task-header">
    <input type="checkbox" class="task-checkbox" data-task-id="<?php echo $row['id']; ?>" <?php echo $taskCompleted ? 'checked' : ''; ?> disabled>
    <div class="task-title"><?php echo htmlspecialchars($row['name']); ?></div>
</div>
    
    <div class="task-info">
        <strong>Status:</strong> <span class="task-status"> <?php echo $row['status']; ?> </span>
    </div>
    <div class="task-info">
        <strong>Prioritas:</strong> <span class="<?php echo $priorityClass; ?>"><?php echo $row['priority']; ?></span>
    </div>
    <div class="task-info">
        <strong>Deadline:</strong> <?php echo $row['deadline']; ?>
    </div>
    <?php if ($isLate) { ?>
        <div class="task-info"><span class="late">Terlambat!</span></div>
    <?php } ?>

    <div class="subtask-list">
        <strong>Subtugas:</strong>
        <?php foreach ($subtasks as $subtask) { ?>
            <div class="subtask-item">
                <input type="checkbox" class="subtask-checkbox" data-subtask-id="<?php echo $subtask['id']; ?>" data-task-id="<?php echo $row['id']; ?>" <?php echo ($subtask['status'] == 'Selesai') ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($subtask['name']); ?></span>
            </div>
        <?php } ?>
    </div>

    <div class="task-actions">
        <?php if (!$taskCompleted && !$isLate) { ?>
            <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="edit-btn"><i class="fas fa-edit"></i></a>
        <?php } ?>
        <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Apakah kamu yakin ingin menghapus tugas ini?')">
    <i class="fas fa-trash-alt"></i>
</a>

        <?php if (!$taskCompleted && !$isLate) { ?>
            <a href="add_subtask.php" class="btn">+ Tambah Subtugas</a>
        <?php } ?>
    </div>
</div>
        <?php } ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    // ✅ Event listener untuk checkbox subtugas
    document.querySelectorAll('.subtask-checkbox').forEach(subtaskCheckbox => {
        subtaskCheckbox.addEventListener('change', function() {
            let taskId = this.dataset.taskId;
            let subtasks = document.querySelectorAll(`.subtask-checkbox[data-task-id='${taskId}']`);
            let taskCheckbox = document.querySelector(`.task-checkbox[data-task-id='${taskId}']`);

            // 🔹 Jika semua subtugas dicentang, tugas utama juga dicentang
            let allChecked = [...subtasks].every(sub => sub.checked);
            if (taskCheckbox) {
                taskCheckbox.checked = allChecked;
            }

            // 🔹 Update status tugas utama di database
            updateTaskStatus(taskId, allChecked ? 'Selesai' : 'Belum Selesai');
        });
    });

    // ✅ Event listener untuk checkbox tugas utama
    document.querySelectorAll('.task-checkbox').forEach(taskCheckbox => {
        taskCheckbox.addEventListener('change', function() {
            let taskId = this.dataset.taskId;
            let checked = this.checked;
            let subtasks = document.querySelectorAll(`.subtask-checkbox[data-task-id='${taskId}']`);

            // 🔹 Jika tugas utama dicentang, semua subtugas dicentang juga
            subtasks.forEach(sub => sub.checked = checked);

            // 🔹 Update status di database
            updateTaskStatus(taskId, checked ? 'Selesai' : 'Belum Selesai');
        });
    });
});

// ✅ Fungsi untuk update status ke database
function updateTaskStatus(taskId, status) {
    fetch('update_task_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `task_id=${taskId}&status=${status}`
    })
    .then(response => response.text())
    .then(data => console.log('Response:', data))
    .catch(error => console.error('Error:', error));
}


    </script>
</body>
</html>