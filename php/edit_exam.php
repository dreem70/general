<?php
include 'db.php';

// جلب الصفوف المتاحة من قاعدة البيانات
$sql = "SELECT DISTINCT class_grade FROM questions ORDER BY class_grade";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.jpg" type="image/jpg">
    <title>تعديل الامتحان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/side_menu.css">


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; text-align: right; 
        background: url('../images/bg.jpg') no-repeat center center fixed;
        background-size: cover;
        }
        body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* طبقة شفافة فوق الخلفية */
    backdrop-filter: blur(8px); /* تأثير بلور */
    z-index: -1;
}
        h2 {
    color: #2C3E50 !important; /* تأكيد اللون */
    font-size: 32px; /* تكبير الخط */
    border: 2px solid black !important; /* إضافة إطار أسود */
    padding: 10px;
    text-align: center;
    display: inline-block; /* لضبط الإطار حول النص فقط */
    background-color: white; /* تحسين التباين */
}

/* تنسيق صندوق السؤال */
.question-box { 
    padding: 15px; 
    margin: 20px; 
    color: #2C3E50 !important; 
    border: 2px solid black !important; /* إطار أسود */
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    font-size: 20px; /* تكبير حجم الخط */
    margin-top: 50px; /* ترك مسافة من الأعلى */
    background-color: rgba(255, 255, 255, 0.8); /* خلفية خفيفة */
}

/* زر الحذف */
.delete-btn { 
    background-color: red; 
    color: white; 
    border: none; 
    cursor: pointer; 
    padding: 8px 15px; 
    font-size: 18px;
}

/* تنسيق القائمة المنسدلة */
select {
    font-size: 20px;
    padding: 10px;
    border: 2px solid black;
    color: #2C3E50;
    background-color: white;
}


.logo {
    position: absolute;
    top: 10px;  /* جعلها في الأعلى */
    left: 10px; /* جعلها في اليسار */
    width: 100px; /* تكبير الصورة */
    height: 100px;
    border-radius: 10px;
}
/* تحسين التنسيق للأجهزة الصغيرة */
@media (max-width: 600px) {
    body {
        background: url('../images/Mbg.jpg') no-repeat center center fixed;
        background-size: cover;
    }

    body::before {
        position: fixed;
    }
    .logo {
        width: 50px; /* تكبير الصورة */
    height: 50px;
    }
}
    </style>
</head>
<body>
<img src="../images/logo.jpg" alt="شعار منصة ابن مندور" class="logo">
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
<br><br><br>

<h2>تعديل الامتحان</h2>

<!-- قائمة اختيار الصف الدراسي -->
<label for="classGrade">اختر الصف:</label>
<select id="classGrade" onchange="loadQuestions()">
    <option value="">اختر الصف</option>
    <?php while ($row = $result->fetch_assoc()): ?>
        <option value="<?php echo htmlspecialchars($row['class_grade']); ?>"><?php echo htmlspecialchars($row['class_grade']); ?></option>
    <?php endwhile; ?>
</select>

<div id="questions-list"></div>

<script>
function loadQuestions() {
    var classGrade = $("#classGrade").val();
    if (classGrade === "") {
        $("#questions-list").html("");
        return;
    }

    $.ajax({
        url: 'get_questions.php',
        type: 'GET',
        data: { class_grade: classGrade },
        success: function(response) {
            $("#questions-list").html(response);
        }
    });
}

function deleteQuestion(questionId) {
    if (confirm("هل أنت متأكد أنك تريد حذف هذا السؤال؟")) {
        $.ajax({
            url: 'delete_question.php',
            type: 'POST',
            data: { id: questionId },
            success: function(response) {
                if (response == "success") {
                    $("#question-" + questionId).fadeOut();
                } else {
                    alert("فشل الحذف! حاول مرة أخرى.");
                }
            }
        });
    }
}

function toggleMenu() {
    let sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
}
</script>

</body>
</html>

<?php $conn->close(); ?>
