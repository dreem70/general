<?php
include 'db.php';

// التحقق من الطلبات القادمة من AJAX (جلب الطلاب أو حفظ الدفع)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action'])) {
        if ($data['action'] === "fetch_students" && isset($data['class'])) {
            // جلب الطلاب حسب الفصل
            $class = $data['class'];
            $sql = "SELECT name FROM students WHERE class = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $class);
            $stmt->execute();
            $result = $stmt->get_result();
            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
            echo json_encode($students);
            exit;
        } 
        
        elseif ($data['action'] === "save_payment" && isset($data['name'], $data['class'], $data['amount'], $data['payment_date'])) {
            // حفظ بيانات الدفع
            $stmt = $conn->prepare("INSERT INTO payments (student_name, class, amount, payment_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $data['name'], $data['class'], $data['amount'], $data['payment_date']);
            $response = $stmt->execute() ? ["success" => true] : ["success" => false, "error" => "خطأ في الإدخال"];
            echo json_encode($response);
            exit;
        }
        elseif ($data['action'] === "check_payment" && isset($data['name'], $data['class'])) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM payments WHERE student_name = ? AND class = ?");
            $stmt->bind_param("ss", $data['name'], $data['class']);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            echo json_encode(["paid" => $count > 0]);
            exit;
        }

    }
}

// جلب الفصول الدراسية
$sql = "SELECT DISTINCT class FROM students";
$result = $conn->query($sql);
$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row['class'];
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.jpg" type="image/jpg">

    <title>إدارة الرسوم الدراسية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/buttons.css">
    <link rel="stylesheet" href="../style/side_menu.css">
    <link rel="stylesheet" href="../style/H.css">
    <link rel="stylesheet" href="../style/card.css">
    <link rel="stylesheet" href="../style/fees.css">





</head>
<body>
<button class="menu-btn" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
      </button>

<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="../index.html"><i class="fas fa-sign-in-alt"></i> تسجيل خروج</a></li>
        <li><a href="../html/teacher_home.html"><i class="fas fa-home"></i> الرئيسية</a></li>
        <li><a href="../html/students.html"><i class="fas fa-user-graduate"></i> الطلاب</a></li>
        <li><a href="../html/add_student.html"><i class="fas fa-user-plus"></i> اضافة طالب جديد</a></li>
        <li><a href="../html/lessons_ctrl.html"><i class="fas fa-chalkboard-teacher"></i> ادارة الحصص</a></li>
        <li><a href="../html/exames_ctrl.html"><i class="fas fa-file-alt"></i> ادارة الامتحانات</a></li>

    </ul>
</div>
           <img src="../images/logo.jpg" alt="شعار منصة ابن مندور" class="logo">

<div class="container">
    <h2>دفع الرسوم الدراسية</h2>
    <a href="payments_status.php" id="payed" class="payment-btn">المصاريف المدفوعة</a>
    
    <label for="class">اختر الفصل:</label>
    <select id="class" onchange="fetchStudents()">
        <option value="">-- اختر الفصل --</option>
        <?php foreach ($classes as $class) { echo "<option value='$class'>$class</option>"; } ?>
    </select>
    
    <table id="students-table" style="display:none;">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>المبلغ</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

</div>
<script src="../JS/ToggleMenu.js"></script>
<script src="../JS/payments.js"></script>
</body>
</html>

