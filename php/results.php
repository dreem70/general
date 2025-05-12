<?php
include 'db.php';

$sql = "SELECT 
            er.username, 
            er.student_class, 
            er.correct_answers, 
            COALESCE(qs.total_questions, 0) AS total_questions
        FROM exam_results er
        LEFT JOIN (
            SELECT q.class_grade, COUNT(sq.id) AS total_questions
            FROM questions q
            LEFT JOIN sub_questions sq ON q.id = sq.question_id
            GROUP BY q.class_grade
        ) qs ON er.student_class = qs.class_grade
        ORDER BY er.student_class, er.correct_answers DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.jpg" type="image/jpg">
    <title>نتائج الامتحان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/side_menu.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            background: url('../images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            padding: 20px;
            text-align: center;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            filter: blur(10px);
            z-index: -1;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
    color: #2c3e50; /* لون أزرق داكن */
    text-shadow: 
        -1px -1px 0 white,  
         1px -1px 0 white,  
        -1px  1px 0 white,  
         1px  1px 0 white,
        -1px  0px 0 white,  
         1px  0px 0 white,  
         0px -1px 0 white,  
         0px  1px 0 white;
}
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            color:#2c3e50;
            font-size:20px;
        }
        th {
            background-color: #2196F3;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        


        .logo {
    position: absolute;
    top: 10px;  /* جعلها في الأعلى */
    left: 10px; /* جعلها في اليسار */
    width: 100px; /* تكبير الصورة */
    height: 100px;
    border-radius: 10px;
}

        @media (max-width: 600px) {
    body {
        background: url('../images/Mbg.jpg') no-repeat center center fixed;
        background-size: cover;
    }
    body::before {
        position: fixed;
    }
    .container {
        width: 90%;
        padding: 10px;
    }
    h2 {
        font-size: 18px;
    }
    table {
        font-size: 14px;
    }
    th, td {
        padding: 5px;
        font-size: 16px;
    }
    .delete-btn {
        padding: 3px 6px;
        font-size: 14px;
    }
    .menu-btn {
        padding: 8px 12px;
        font-size: 16px;
    }
    .sidebar {
        width: 200px;
    }
    .sidebar ul li {
        padding: 10px;
    }
    .sidebar ul li a {
        font-size: 16px;
    }
    .logo {
        width: 70px;
        height: 70px;
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

<div class="container">
    <h2>جدول نتائج الامتحانات</h2>
    <table>
        <tr>
            <th>الاسم</th>
            <th>الصف</th>
            <th>الدرجة</th>
            <th>عدد الأسئلة</th>
            <th>حذف</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr id="row_<?php echo $row["username"]; ?>">
                    <td><?php echo htmlspecialchars($row["username"]); ?></td>
                    <td><?php echo htmlspecialchars($row["student_class"]); ?></td>
                    <td><?php echo htmlspecialchars($row["correct_answers"]); ?></td>
                    <td><?php echo htmlspecialchars($row["total_questions"]); ?></td>
                    <td>
                        <button class="delete-btn" onclick="deleteRow('<?php echo $row["username"]; ?>')">❌</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">لا توجد نتائج حتى الآن</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<script>
function deleteRow(username) {
    if (confirm("هل أنت متأكد من حذف هذا السجل؟")) {
        fetch("delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "username=" + encodeURIComponent(username)
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                document.getElementById("row_" + username).remove();
            } else {
                alert("فشل في حذف السجل!");
            }
        })
        .catch(error => console.error("Error:", error));
    }
}
function toggleMenu() {
    let sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
}
</script>

</body>
</html>

<?php
$conn->close();
?>
