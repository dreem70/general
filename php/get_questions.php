<?php
include 'db.php';
$classGrade = isset($_GET['class_grade']) ? $_GET['class_grade'] : '';

$sql = "SELECT id, question_text FROM questions WHERE class_grade = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $classGrade);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()):
?>
        <div class="question-box" id="question-<?php echo $row['id']; ?>">
            <span><?php echo htmlspecialchars($row['question_text']); ?></span>
            <button class="delete-btn" onclick="deleteQuestion(<?php echo $row['id']; ?>)">X</button>
        </div>
<?php
    endwhile;
} else {
    echo "<p>لا توجد أسئلة لهذا الصف.</p>";
}

$stmt->close();
$conn->close();
?>
