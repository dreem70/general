<?php
include 'db.php';
$classGrade = isset($_GET['class_grade']) ? $_GET['class_grade'] : ''; // استقبال الصف من الجافاسكريبت

$sql = "SELECT q.id AS question_id, q.question_text, q.class_grade,  
               s.id AS sub_question_id, s.sub_question_text, 
               a.id AS answer_id, a.answer_text, a.is_correct
        FROM questions q
        LEFT JOIN sub_questions s ON q.id = s.question_id
        LEFT JOIN answers a ON s.id = a.sub_question_id
        WHERE q.class_grade = ?  -- تصفية الأسئلة حسب الصف
        ORDER BY q.id, s.id, a.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $classGrade);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
$correct_answers = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $q_id = $row['question_id'];
        $s_id = $row['sub_question_id'];
        $a_id = $row['answer_id'];

        if (!isset($questions[$q_id])) {
            $questions[$q_id] = [
                "text" => $row["question_text"],
                "sub_questions" => []
            ];
        }

        if ($s_id && !isset($questions[$q_id]["sub_questions"][$s_id])) {
            $questions[$q_id]["sub_questions"][$s_id] = [
                "text" => $row["sub_question_text"],
                "answers" => []
            ];
        }

        if ($s_id) {
            $questions[$q_id]["sub_questions"][$s_id]["answers"][] = [
                "id" => $a_id,
                "text" => $row["answer_text"]
            ];

            if ($row["is_correct"] == 1) {
                $correct_answers[$s_id] = $a_id;
            }
        }
    }
}

$conn->close();
echo json_encode(["questions" => array_values($questions), "correct_answers" => $correct_answers]);
?>