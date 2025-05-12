<?php
include 'db.php';
// التحقق من نجاح الاتصال
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "فشل الاتصال بقاعدة البيانات"]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['name']) && isset($data['class']) && isset($data['absence']) && isset($data['absence-date'])) {
    $name = $data['name'];
    $class = $data['class'];
    $absence = $data['absence'] ? 1 : 0;
    $absence_date = $data['absence-date'];

    // التحقق مما إذا كان الحضور/الغياب قد تم تسجيله مسبقًا في نفس التاريخ
    $check_stmt = $conn->prepare("SELECT id, absence FROM absence WHERE name = ? AND class = ? AND absence_date = ?");
    $check_stmt->bind_param("sss", $name, $class, $absence_date);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // إذا كان السجل موجودًا، يتم تحديثه
        $check_stmt->bind_result($id, $existing_absence);
        $check_stmt->fetch();

        // إذا كانت الحالة الحالية هي نفسها الحالة الجديدة (حضور أو غياب) فلا نحتاج لتحديث
        if ($existing_absence !== $absence) {
            // تحديث الحضور/الغياب إذا كانت الحالة مختلفة
            $update_stmt = $conn->prepare("UPDATE absence SET absence = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $absence, $id);
            if ($update_stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "خطأ أثناء التحديث"]);
            }
            $update_stmt->close();
        } else {
            // إذا كانت الحالة هي نفسها، لا نقوم بأي تعديل
            echo json_encode(["success" => true]);
        }
    } else {
        // إذا لم يكن هناك سجل، يتم إدخال البيانات
        $stmt = $conn->prepare("INSERT INTO absence (name, class, absence, absence_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $class, $absence, $absence_date);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "خطأ أثناء الإدخال"]);
        }
        $stmt->close();
    }
    $check_stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "بيانات غير مكتملة"]);
}

$conn->close();
?>
