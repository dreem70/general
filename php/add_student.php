<?php
include 'db.php';

// استقبال البيانات من JavaScript
$user = $_POST['name']; // الاسم
$code = $_POST['code']; // الكود
$class = $_POST['class']; // الصف المختار

// إدخال البيانات في قاعدة البيانات
$sql = "INSERT INTO students (name, code, class) VALUES (?, ?, ?)"; // استعلام الإدخال
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user, $code, $class); // ربط المتغيرات
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success"; // إذا تم الإدخال بنجاح
} else {
    echo "error"; // إذا حدث خطأ
}

$stmt->close();
$conn->close();
?>
