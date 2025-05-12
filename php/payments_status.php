<?php
include 'db.php';

// التحقق من الطلبات القادمة من AJAX (جلب الطلاب حسب الصف والتاريخ)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action']) && $data['action'] === "fetch_students" && isset($data['class']) && isset($data['date'])) {
        $class = $data['class'];
        $date = $data['date'];

        // جلب بيانات الطلاب وحالة الدفع بناءً على الفصل والتاريخ
        $sql = "SELECT s.name, 
                       COALESCE(SUM(p.amount), 0) AS total_paid
                FROM students s
                LEFT JOIN payments p ON s.name = p.student_name 
                    AND s.class = p.class 
                    AND DATE_FORMAT(p.payment_date, '%Y-%m') = ?
                WHERE s.class = ?
                GROUP BY s.name";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $date, $class);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        $total_class_payment = 0;

        while ($row = $result->fetch_assoc()) {
            $row['status'] = ($row['total_paid'] > 0) ? "✅ مدفوع" : "❌ غير مدفوع";
            $students[] = $row;
            $total_class_payment += $row['total_paid'];
        }

        echo json_encode(["students" => $students, "total_payment" => $total_class_payment]);
        exit;
    }
}

// جلب الفصول الدراسية
$sql = "SELECT DISTINCT class FROM students";
$result = $conn->query($sql);
$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row['class'];
}

// جلب الأشهر المتاحة من قاعدة البيانات
$sql = "SELECT DISTINCT DATE_FORMAT(payment_date, '%Y-%m') AS payment_month FROM payments ORDER BY payment_month DESC";
$result = $conn->query($sql);
$dates = [];
while ($row = $result->fetch_assoc()) {
    $dates[] = $row['payment_month'];
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.jpg" type="image/jpg">

    <title>حالة دفع الرسوم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/card.css">
    <link rel="stylesheet" href="../style/input.css">
    <link rel="stylesheet" href="../style/side_menu.css">
    <link rel="stylesheet" href="../style/table.css">


    
       
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
    <h2>حالة دفع الرسوم الدراسية</h2>

    <label for="class">اختر الفصل:</label>
    <select id="class" onchange="fetchStudents()">
        <option value="">-- اختر الفصل --</option>
        <?php foreach ($classes as $class) { echo "<option value='$class'>$class</option>"; } ?>
    </select>

    <label for="date">اختر الشهر:</label>
    <select id="date" onchange="fetchStudents()">
        <option value="">-- اختر الشهر --</option>
        <?php foreach ($dates as $date) { echo "<option value='$date'>$date</option>"; } ?>
    </select>

    <table id="students-table" style="display:none;">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>حالة الدفع</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="total-payment" class="total" style="display:none;"></div>
</div>

<script src="../JS/ToggleMenu.js"></script>
<script src="../JS/fetchPay.js"></script>


</body>
</html>
