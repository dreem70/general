<?php
include 'db.php';

// استقبال البيانات من JavaScript
$user = $_POST['username'];
$code = $_POST['code'];

// البحث عن المستخدم في قاعدة البيانات
$sql = "SELECT * FROM students WHERE name = ? AND code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "student"; // إذا وجد في قاعدة البيانات، توجيهه للطالب
} else {
    echo "error"; // إذا لم يوجد، إظهار الخطأ
}

$stmt->close();
$conn->close();
?>
