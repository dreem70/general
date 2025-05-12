<?php
// معلومات الاتصال بقاعدة البيانات
include 'db.php';
// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "فشل الاتصال بقاعدة البيانات."]));
}

// التحقق من نوع الطلب
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['lesson'])) {
    $lessonName = $_GET['lesson'];

    // استعلام لاسترجاع البيانات
    $sql = "SELECT lesson, details, files FROM lessons WHERE lesson = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $lessonName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($lesson, $details, $files);
        $stmt->fetch();
        echo json_encode(["success" => true, "details" => $details, "files" => $files]);
    } else {
        echo json_encode(["success" => false, "message" => "لم يتم العثور على الدرس."]);
    }
    $stmt->close();
}

// تحديث التفاصيل إذا تم إرسالها عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lesson']) && isset($_POST['details'])) {
    $lessonName = $_POST['lesson'];
    $newDetails = $_POST['details'];

    $sql = "UPDATE lessons SET details = ? WHERE lesson = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $newDetails, $lessonName);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث التفاصيل بنجاح."]);
    } else {
        echo json_encode(["success" => false, "message" => "فشل في تحديث التفاصيل."]);
    }
    $stmt->close();
}

// إضافة ملفات جديدة إلى العمود 'files' إذا تم إرسال ملفات عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lesson']) && isset($_FILES['files'])) {
    $lessonName = $_POST['lesson'];
    $files = $_FILES['files'];

    $fileNames = [];
    foreach ($files['name'] as $key => $fileName) {
        $tmpName = $files['tmp_name'][$key];
        $uploadDir = '../uploads/';
        $newFileName = basename($fileName);
        if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
            $fileNames[] = $newFileName;
        }
    }

    if (!empty($fileNames)) {
        // استرجاع الملفات الحالية
        $sql = "SELECT files FROM lessons WHERE lesson = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $lessonName);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($filesInDb);
            $stmt->fetch();
            $currentFiles = explode(",", $filesInDb);
            $currentFiles = array_map('trim', $currentFiles);
            $newFiles = array_merge($currentFiles, $fileNames);
            $updatedFiles = implode(",", $newFiles);

            // تحديث قاعدة البيانات
            $updateSql = "UPDATE lessons SET files = ? WHERE lesson = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $updatedFiles, $lessonName);

            if ($updateStmt->execute()) {
                echo json_encode(["success" => true, "message" => "تم إضافة الملفات بنجاح."]);
            } else {
                echo json_encode(["success" => false, "message" => "فشل في تحديث قاعدة البيانات."]);
            }
            $updateStmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "لم يتم العثور على الدرس."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "لم يتم تحميل أي ملفات."]);
    }
}

// حذف ملف معين عند طلبه عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lesson']) && isset($_POST['delete_file'])) {
    $lessonName = $_POST['lesson'];
    $fileToDelete = $_POST['delete_file'];

    // استرجاع الملفات الحالية
    $sql = "SELECT files FROM lessons WHERE lesson = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $lessonName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($files);
        $stmt->fetch();
        $fileList = explode(",", $files);
        $fileList = array_map('trim', $fileList);

        if (($key = array_search($fileToDelete, $fileList)) !== false) {
            unset($fileList[$key]);
            $updatedFiles = implode(",", $fileList);

            $updateSql = "UPDATE lessons SET files = ? WHERE lesson = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $updatedFiles, $lessonName);

            if ($updateStmt->execute()) {
                // حذف الملف من السيرفر
                $filePath = "../uploads/" . basename($fileToDelete);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                echo json_encode(["success" => true, "message" => "تم حذف الملف بنجاح."]);
            } else {
                echo json_encode(["success" => false, "message" => "فشل في تحديث قاعدة البيانات."]);
            }
            $updateStmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "الملف غير موجود في قاعدة البيانات."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "لم يتم العثور على الدرس."]);
    }
    $stmt->close();
}

// إغلاق الاتصال
$conn->close();
?>
