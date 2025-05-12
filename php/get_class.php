<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    
    $stmt = $conn->prepare("SELECT class FROM students WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true, "class" => $row["class"]]);
    } else {
        echo json_encode(["success" => false, "message" => "الطالب غير موجود"]);
    }

    $stmt->close();
}

$conn->close();
?>
