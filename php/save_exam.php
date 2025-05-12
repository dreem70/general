<?php
include 'db.php';
$data = json_decode(file_get_contents("php://input"), true);
$classGrade = $data["classGrade"]; // الحصول على الصف

foreach ($data["questions"] as $question) {
    $stmt = $conn->prepare("INSERT INTO questions (question_text, class_grade) VALUES (?, ?)");
    $stmt->bind_param("ss", $question["text"], $classGrade);
    $stmt->execute();
    $questionId = $stmt->insert_id;

    foreach ($question["subQuestions"] as $subQuestion) {
        $stmt = $conn->prepare("INSERT INTO sub_questions (question_id, sub_question_text) VALUES (?, ?)");
        $stmt->bind_param("is", $questionId, $subQuestion["text"]);
        $stmt->execute();
        $subQuestionId = $stmt->insert_id;

        foreach ($subQuestion["answers"] as $answer) {
            $stmt = $conn->prepare("INSERT INTO answers (sub_question_id, answer_text, is_correct) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $subQuestionId, $answer["text"], $answer["correct"]);
            $stmt->execute();
        }
    }
}

$conn->close();
echo json_encode(["message" => "تم حفظ الامتحان بنجاح"]);
?>
