<?php
include 'db.php';
// استقبال البيانات من نموذج HTML
$class = $_POST['class'];
$unit = $_POST['unit'];
$semester = $_POST['semester'];
$lesson = $_POST['lesson'];
$details = $_POST['details'];

// رفع الملفات
$filePaths = [];
$targetDir = "../uploads/";

if (isset($_FILES['file'])) {
    $files = $_FILES['file'];

    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = basename($files['name'][$i]);
        $targetFilePath = $targetDir . $fileName;

        // نقل الملف إلى المجلد المحدد
        if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {
            $filePaths[] = $targetFilePath;
        }
    }
}

// تحويل مصفوفة المسارات إلى سلسلة مفصولة بفواصل لتخزينها في قاعدة البيانات
$filePathsString = implode(",", $filePaths);

// إدخال البيانات في قاعدة البيانات
$sql = "INSERT INTO lessons (class, unit, semester, lesson, details, files) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $class, $unit, $semester, $lesson, $details, $filePathsString);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success"; // إذا تم الإدخال بنجاح
} else {
    echo "error"; // إذا حدث خطأ
}

$stmt->close();
$conn->close();
?>
