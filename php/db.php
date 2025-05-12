<?php
$servername = "sql103.infinityfree.com";
$username = "if0_37680309"; 
$password = "AFeltn3I9xaFP5W";     
$dbname = "if0_37680309_online_tech";

// الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
?>
