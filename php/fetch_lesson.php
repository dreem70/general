<?php
// إعداد الاتصال بقاعدة البيانات
include 'db.php';
// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// الحصول على اسم الدرس من الـ GET
$lessonName = isset($_GET['lesson']) ? $_GET['lesson'] : '';

// الاستعلام عن البيانات من جدول الدروس
$sql = "SELECT details, files FROM lessons WHERE lesson_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $lessonName);
$stmt->execute();
$stmt->store_result();

// إذا تم العثور على الدرس
if ($stmt->num_rows > 0) {
    $stmt->bind_result($details, $files);
    $stmt->fetch();
    
    // تجهيز البيانات للإرجاع كـ JSON
    $response = array(
        'details' => $details,
        'files' => explode(',', $files)  // تقسيم الملفات إلى مصفوفة
    );
    echo json_encode($response);
} else {
    echo json_encode(array('error' => 'الدرس غير موجود.'));
}

// إغلاق الاتصال
$stmt->close();
$conn->close();
?>
