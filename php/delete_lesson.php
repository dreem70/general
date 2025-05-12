<?php
// بيانات الاتصال بقاعدة البيانات
include 'db.php';
// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(["error" => "فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

// تنفيذ عملية الحذف إذا تم إرسال بيانات للحذف
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lesson_ids'])) {
    $deletedCount = 0;
    foreach ($_POST['lesson_ids'] as $lesson_id) {
        $lesson_id = intval($lesson_id);
        $sql = "DELETE FROM lessons WHERE id = $lesson_id";
        if ($conn->query($sql) === TRUE) {
            $deletedCount++;
        }
    }
    echo json_encode(["message" => "تم حذف {$deletedCount} درس بنجاح!"]);
    exit();
}

// جلب جميع البيانات من الجدول
$sql = "SELECT id, class, unit, semester, lesson, details, files FROM lessons";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // تقصير التفاصيل إلى أول كلمتين فقط
        $words = explode(" ", $row['details']);
        $row['short_details'] = htmlspecialchars(implode(" ", array_slice($words, 0, 2)));
        if (count($words) > 2) {
            $row['short_details'] .= " ...";
        }

        $data[] = $row;
    }
}

// إرجاع البيانات كـ JSON
echo json_encode($data);

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
