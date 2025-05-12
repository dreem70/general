<?php
include 'db.php';

// حذف صف الطالب بناءً على المعرف
if (isset($_GET['delete'])) {
    $studentId = $_GET['delete'];
    $sqlDelete = "DELETE FROM perfict_student WHERE id = '$studentId'";

    if ($conn->query($sqlDelete) === TRUE) {
        
    } else {
        echo "خطأ في الحذف: " . $conn->error;
    }
}

// إدخال بيانات الطالب
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student = $_POST['student'];
    $imageName = "";

    // التحقق من رفع الصورة
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "perfict_student/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true); // إنشاء المجلد إذا لم يكن موجودًا
        }

        $imageName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // التحقق من أنواع الملفات المسموح بها
        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // تم رفع الصورة بنجاح
            } else {
                echo "حدث خطأ أثناء رفع الصورة.";
                exit;
            }
        } else {
            echo "يُسمح فقط بملفات JPG و JPEG و PNG و GIF.";
            exit;
        }
    }

    // إدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO perfict_student (stuedent, image) VALUES ('$student', '$imageName')";
    if ($conn->query($sql) === TRUE) {
        
    } else {
        echo "خطأ: " . $conn->error;
    }
}

// عرض بيانات الطلاب
$sql = "SELECT * FROM perfict_student";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <title>تعيين طالب مثالي</title>

    <link rel="stylesheet" href="../style/buttons.css">
    <link rel="stylesheet" href="../style/input.css">
    <link rel="stylesheet" href="../style/login.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/mobile.css">
    <link rel="stylesheet" href="../style/side_menu.css">
    <link rel="stylesheet" href="../style/login_container.css">
    <link rel="stylesheet" href="../style/card.css">
    <link rel="stylesheet" href="../style/form.css">
    <link rel="stylesheet" href="../style/table.css">
    <link rel="stylesheet" href="../style/label.css">
    <link rel="stylesheet" href="../style/H.css">
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

    <h2 id = "add_perfict">تعيين طالب مثالي</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <input id ="student_name" type="text" name="student" placeholder="اسم الطالب" required><br>
        <input type="file" name="image" accept="image/*"><br>
        <button id="done" type="submit">اضافة</button>
    </form>

    
    <?php if ($result->num_rows > 0): ?>
        <div id="perfict_table-container">
    <table id="perfict_table">
        <thead>
    <tr>
        <th>اسم الطالب</th>
        <th>الصورة</th>
    </tr>
</thead>
<tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td id = "student_name_table" ><?php echo $row['stuedent']; ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <div class="image-container">
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn">X</a>
                        <img src="perfict_student/<?php echo $row['image']; ?>" width="150" height="150" alt="صورة الطالب">
                    </div>
                <?php else: ?>
                    لا توجد صورة
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

    </table>
</div>

    <?php else: ?>
        <p>لا توجد بيانات للطلاب بعد.</p>
    <?php endif; ?>


    <script src="../JS/ToggleMenu.js"></script>


</body>
</html>
