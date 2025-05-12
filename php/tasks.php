<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["task"]) && isset($_POST["username"])) { 
        $task = $_POST["task"];
        $username = $_POST["username"];
        if (!empty($task) && !empty($username)) {
            $stmt = $conn->prepare("INSERT INTO tasks (task, username) VALUES (?, ?)");
            $stmt->bind_param("ss", $task, $username);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST["delete_task"])) {
        $task_id = $_POST["delete_task"];
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $stmt->close();
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "get" && isset($_GET["username"])) {
    $username = $_GET["username"];
    $stmt = $conn->prepare("SELECT id, task FROM tasks WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<li><label><input type='checkbox' onchange='deleteTask({$row["id"]})'> {$row["task"]}</label></li>";
    }
    $stmt->close();
    exit;
}
?>
