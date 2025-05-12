<?php
// إعدادات قاعدة البيانات
include 'db.php';
// التحقق من الاتصال
if ($conn->connect_error) {
    die("الاتصال بقاعدة البيانات فشل: " . $conn->connect_error);
}

// الحصول على بيانات الطلاب المرسلة من JavaScript
$data = json_decode(file_get_contents("php://input"), true);
$studentIds = $data['ids'];

// التحقق من وجود الطلاب ثم حذفهم
if (!empty($studentIds)) {
    // إعداد الاستعلام لحذف الطلاب
    $ids = implode(',', $studentIds);  // تحويل المصفوفة إلى سلسلة من الأرقام مفصولة بفواصل
    $sql = "DELETE FROM students WHERE id IN ($ids)";

    if ($conn->query($sql) === TRUE) {
        // إذا تم الحذف بنجاح
        echo json_encode(["success" => true]);
    } else {
        // في حال حدوث خطأ أثناء الحذف
        echo json_encode(["success" => false, "message" => "حدث خطأ أثناء الحذف"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "المعرفات غير صالحة"]);
}

// إغلاق الاتصال
$conn->close();
?>
