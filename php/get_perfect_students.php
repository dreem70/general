<?php
include 'db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT stuedent AS name, image FROM perfict_student";
$result = $conn->query($sql);

$students = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$conn->close();
echo json_encode($students);
?>
