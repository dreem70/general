<?php
// إعدادات قاعدة البيانات
include 'db.php';
// التحقق من الاتصال
if ($conn->connect_error) {
    die("الاتصال بقاعدة البيانات فشل: " . $conn->connect_error);
}

// استعلام جلب بيانات الطلاب
$sql = "SELECT id, name, code,class FROM students";
$result = $conn->query($sql);

$students = [];
if ($result->num_rows > 0) {
    // تجميع البيانات
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode($students);
} else {
    echo json_encode([]);
}

// إغلاق الاتصال
$conn->close();
?>
