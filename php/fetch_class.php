<?php
// إعدادات قاعدة البيانات
include 'db.php';
// التحقق من الاتصال
if ($conn->connect_error) {
    die("الاتصال بقاعدة البيانات فشل: " . $conn->connect_error);
}

// جلب الصفوف المميزة من العمود class من جدول lessons
$sql_class = "SELECT DISTINCT class FROM lessons";
$result_class = $conn->query($sql_class);

// إنشاء مصفوفة لتخزين الصفوف والفصول والوحدات والدروس
$classes = [];
if ($result_class->num_rows > 0) {
    while ($row_class = $result_class->fetch_assoc()) {
        $class = $row_class["class"];
        
        // جلب الفصول المتعلقين بكل صف
        $sql_semester = "SELECT DISTINCT semester FROM lessons WHERE class = '$class'";
        $result_semester = $conn->query($sql_semester);
        
        $semesters = [];
        if ($result_semester->num_rows > 0) {
            while ($row_semester = $result_semester->fetch_assoc()) {
                $semester = $row_semester["semester"];
                
                // جلب الوحدات المتعلقين بكل فصل
                $sql_unit = "SELECT DISTINCT unit FROM lessons WHERE class = '$class' AND semester = '$semester'";
                $result_unit = $conn->query($sql_unit);
                
                $units = [];
                if ($result_unit->num_rows > 0) {
                    while ($row_unit = $result_unit->fetch_assoc()) {
                        $unit = $row_unit["unit"];
                        
                        // جلب الدروس المتعلقين بكل وحدة
                        $sql_lesson = "SELECT DISTINCT lesson FROM lessons WHERE class = '$class' AND semester = '$semester' AND unit = '$unit'";
                        $result_lesson = $conn->query($sql_lesson);
                        
                        $lessons = [];
                        if ($result_lesson->num_rows > 0) {
                            while ($row_lesson = $result_lesson->fetch_assoc()) {
                                $lessons[] = $row_lesson["lesson"];
                            }
                        } else {
                            $lessons = ["لا توجد دروس"];
                        }

                        // إضافة الدروس إلى الوحدات
                        $units[] = [
                            "unit" => $unit,
                            "lessons" => $lessons
                        ];
                    }
                } else {
                    $units = ["لا توجد وحدات"];
                }

                // إضافة الوحدات إلى الفصول
                $semesters[] = [
                    "semester" => $semester,
                    "units" => $units
                ];
            }
        } else {
            $semesters = ["لا توجد فصول"];
        }

        // إضافة الفصول إلى الصفوف
        $classes[] = [
            "class" => $class,
            "semesters" => $semesters
        ];
    }
} else {
    $classes = ["لا توجد صفوف"];
}

$conn->close();

// إرجاع الصفوف والفصول والوحدات والدروس كـ JSON
echo json_encode($classes);
?>