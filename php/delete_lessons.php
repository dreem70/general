<?php
include 'db.php';
if ($conn->connect_error) {
    die(json_encode(["error" => "فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $deletedCount = 0;

    // حذف الصفوف بالكامل إذا تم تحديدها
    if (!empty($data["delete_row"])) {
        foreach ($data["delete_row"] as $lesson_id) {
            $lesson_id = intval($lesson_id);
            $sql = "DELETE FROM lessons WHERE id = $lesson_id";
            if ($conn->query($sql) === TRUE) {
                $deletedCount++;
            }
        }
    }

    // حذف البيانات الجزئية إذا لم يتم حذف الصف بالكامل
    $columns = [
        "delete_class" => "class",
        "delete_unit" => "unit",
        "delete_semester" => "semester",
        "delete_lesson" => "lesson",
        "delete_details" => "details",
        "delete_files" => "files"
    ];
    
    foreach ($columns as $key => $column) {
        if (!empty($data[$key])) {
            foreach ($data[$key] as $lesson_id) {
                $lesson_id = intval($lesson_id);
                $sql = "UPDATE lessons SET $column = NULL WHERE id = $lesson_id";
                if ($conn->query($sql) === TRUE) {
                    $deletedCount++;
                }
            }
        }
    }

    // حذف الصفوف التي جميع أعمدتها إما NULL أو فارغة
    $deleteEmptyRowsSql = "DELETE FROM lessons 
                           WHERE (class IS NULL OR class = '') 
                           AND (unit IS NULL OR unit = '') 
                           AND (semester IS NULL OR semester = '') 
                           AND (lesson IS NULL OR lesson = '') 
                           AND (details IS NULL OR details = '') 
                           AND (files IS NULL OR files = '')";
    $conn->query($deleteEmptyRowsSql);

    echo json_encode(["message" => "تم حذف {$deletedCount} عنصر بنجاح!"]);
    exit();
}

// جلب البيانات
$sql = "SELECT id, class, unit, semester, lesson, details, files FROM lessons";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // فحص إذا كانت جميع الأعمدة NULL أو فارغة، وإذا كانت كذلك يتم حذف الصف
        if (
            (is_null($row['class']) || $row['class'] === '') &&
            (is_null($row['unit']) || $row['unit'] === '') &&
            (is_null($row['semester']) || $row['semester'] === '') &&
            (is_null($row['lesson']) || $row['lesson'] === '') &&
            (is_null($row['details']) || $row['details'] === '') &&
            (is_null($row['files']) || $row['files'] === '')
        ) {
            $deleteSql = "DELETE FROM lessons WHERE id = " . $row['id'];
            $conn->query($deleteSql);
            continue; // الانتقال للصف التالي بعد الحذف
        }

        // إنشاء تفاصيل قصيرة من التفاصيل
        $words = explode(" ", $row['details']);
        $row['short_details'] = htmlspecialchars(implode(" ", array_slice($words, 0, 2)));
        if (count($words) > 2) {
            $row['short_details'] .= " ...";
        }

        // إخفاء القيم الفارغة
        $row['class'] = $row['class'] ?? '';
        $row['unit'] = $row['unit'] ?? '';
        $row['semester'] = $row['semester'] ?? '';
        $row['lesson'] = $row['lesson'] ?? '';
        $row['details'] = $row['short_details'] ?? '';
        $row['files'] = $row['files'] ?? '';

        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>
