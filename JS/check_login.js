function checkLogin() {
    var username = document.getElementById("username").value;
    var code = document.getElementById("code").value;

    if (username === "الجنرال" && code === "000000") {
        window.location.href = " html/teacher_home.html";
    } else {
        // إرسال البيانات إلى ملف PHP للتحقق من قاعدة البيانات
        var xhr = new XMLHttpRequest();
        xhr.open("POST", " php/check_login.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText.trim();
                if (response === "student") {
                    localStorage.setItem("username", username); // حفظ الاسم في localStorage
                    window.location.href = " html/student_home.html"; // توجيه للطالب
                } else {
                    document.getElementById("error").style.display = "block";
                }
            }
        };
        xhr.send("username=" + encodeURIComponent(username) + "&code=" + encodeURIComponent(code));
    }
}
