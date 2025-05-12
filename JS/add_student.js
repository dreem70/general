       // توليد كود عشوائي عند تحميل الصفحة
       function generateRandomCode() {
        return Math.floor(100000 + Math.random() * 900000); // 6 أرقام
    }

    document.getElementById("studentCode").value = generateRandomCode();

    function addStudent() {
var name = document.getElementById("studentName").value;
var code = document.getElementById("studentCode").value;
var studentClass = document.getElementById("studentClass").value; // إضافة القيمة المختارة من القائمة المنسدلة
var messageBox = document.getElementById("message");

if (name.trim() === "") {
    messageBox.innerHTML = "الرجاء إدخال الاسم!";
    messageBox.className = "message error";
    return;
}

var xhr = new XMLHttpRequest();
xhr.open("POST", "../php/add_student.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
        var response = xhr.responseText.trim();
        if (response === "success") {
            messageBox.innerHTML = "تمت إضافة الطالب بنجاح!";
            messageBox.className = "message success";
            document.getElementById("studentName").value = "";
            document.getElementById("studentCode").value = generateRandomCode(); // توليد كود جديد
        } else {
            messageBox.innerHTML = "حدث خطأ، يرجى المحاولة مرة أخرى.";
            messageBox.className = "message error";
        }
    }
};
xhr.send("name=" + encodeURIComponent(name) + "&code=" + encodeURIComponent(code) + "&class=" + encodeURIComponent(studentClass)); // إرسال قيمة الصف
}

function toggleMenu() {
let sidebar = document.getElementById("sidebar");
sidebar.classList.toggle("active");
}
