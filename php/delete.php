<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"])) {
    $username = $conn->real_escape_string($_POST["username"]);
    $sql = "DELETE FROM exam_results WHERE username = '$username'";
    
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}

$conn->close();
?>
