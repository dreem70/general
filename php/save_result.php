<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $studentClass = isset($_POST['studentClass']) ? $_POST['studentClass'] : 'غير معروف';
    $correct_answers = $_POST["correct_answers"];

    // تحقق مما إذا كان المستخدم قد أكمل الامتحان بالفعل
    $check_sql = "SELECT * FROM exam_results WHERE username = ? AND student_class = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $username, $studentClass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // المستخدم أكمل الامتحان بالفعل
        echo json_encode(["message" => "لقد أكملت هذا الاختبار مسبقًا"]);
    } else {
        // إدراج النتيجة الجديدة في قاعدة البيانات
        $insert_sql = "INSERT INTO exam_results (username, correct_answers, student_class) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sis", $username, $correct_answers, $studentClass);

        if ($stmt->execute()) {
            echo json_encode(["message" => "تم حفظ النتيجة بنجاح"]);
        } else {
            echo json_encode(["error" => "حدث خطأ أثناء الحفظ"]);
        }
    }

    $stmt->close();
}

$conn->close();
?>
