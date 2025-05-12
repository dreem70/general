      document.getElementById('lessonForm').addEventListener('submit', function(event) {
    event.preventDefault(); // منع الإرسال التلقائي للبيانات

    const formData = new FormData(this);
    const messageBox = document.getElementById("message");

    // عرض صورة التحميل
    messageBox.innerHTML = "<img src='../images/loading.gif' alt='جاري التحميل...'>";
    messageBox.className = "message loading";

    fetch('../php/add_lesson.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            messageBox.innerHTML = "تم إضافة الحصة بنجاح!";
            messageBox.className = "message success";
            document.getElementById("lessonForm").reset();
        } else {
            messageBox.innerHTML = "حدث خطأ، يرجى المحاولة مرة أخرى.";
            messageBox.className = "message error";
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageBox.innerHTML = "حدث خطأ في الاتصال بالخادم.";
        messageBox.className = "message error";
    });
});
