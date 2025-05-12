<?php
// الاتصال بقاعدة البيانات
include 'db.php';
// استرجاع التواريخ المتاحة
$dateQuery = "SELECT DISTINCT absence_date FROM absence ORDER BY absence_date DESC";
$dateResult = $conn->query($dateQuery);

// التحقق من تحديد التاريخ
$selectedDate = isset($_GET['date']) ? $_GET['date'] : '';

$daysOfWeek = [
    'Sunday' => 'الأحد',
    'Monday' => 'الاثنين',
    'Tuesday' => 'الثلاثاء',
    'Wednesday' => 'الأربعاء',
    'Thursday' => 'الخميس',
    'Friday' => 'الجمعة',
    'Saturday' => 'السبت'
];

// استرجاع بيانات الغياب والحضور
if ($selectedDate) {
    $absenceQuery = "SELECT * FROM absence WHERE absence_date = ?";
    $stmt = $conn->prepare($absenceQuery);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // حساب الإحصائيات
    $total = $result->num_rows;
    $absentCount = 0;
    while ($row = $result->fetch_assoc()) {
        if ($row['absence'] == 1) {
            $absentCount++;
        }
    }
    $presentCount = $total - $absentCount;
    $attendanceRate = $total > 0 ? round(($presentCount / $total) * 100, 2) : 0;

    // استخراج اسم اليوم
    $dayNameEnglish = date('l', strtotime($selectedDate)); // استخراج اسم اليوم بالإنجليزية
    $dayNameArabic = $daysOfWeek[$dayNameEnglish]; // تحويله إلى العربية
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/side_menu.css">
    <link rel="stylesheet" href="../style/check_absence.css">
    <link rel="stylesheet" href="../style/table.css">
    <link rel="stylesheet" href="../style/container.css">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



    <title>تقرير الغياب</title>
   

    <script>
    function toggleMenu() {
    let sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
}
    </script>
</head>
<body>

    <img src="../images/logo.jpg" alt="شعار منصة ابن مندور" class="logo">

    <h2>تقرير الغياب</h2>

    <form method="GET">
        <label for="date">اختر تاريخ الغياب:</label>
        <select name="date" id="date" onchange="this.form.submit()">
            <option value="">-- اختر التاريخ --</option>
            <?php while ($row = $dateResult->fetch_assoc()) { ?>
                <option value="<?= $row['absence_date'] ?>" <?= ($selectedDate == $row['absence_date']) ? 'selected' : '' ?>>
                    <?= $row['absence_date'] ?> - <?= $daysOfWeek[date('l', strtotime($row['absence_date']))] ?>
                </option>
            <?php } ?>
        </select>
    </form>
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

    <?php if ($selectedDate): ?>
        <h3>التاريخ المحدد: <?= $selectedDate ?> - <?= $dayNameArabic ?></h3>
        <h4>نسبة الحضور: <?= $attendanceRate ?>%</h4>
        <div class="container">
        <table>
            <tr>
                <th>الاسم</th>
                <th>الفصل</th>
            </tr>
            <?php
            $result->data_seek(0); // إعادة المؤشر لبداية النتائج
            while ($row = $result->fetch_assoc()) {
                if ($row['absence'] == 1) {
                    echo "<tr><td>{$row['name']}</td><td>{$row['class']}</td></tr>";
                }
            }
            ?>
        </table>
        </div>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
