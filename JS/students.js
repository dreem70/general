        // إعداد الاتصال بقاعدة البيانات واستخراج بيانات الطلاب
        window.onload = function() {
            fetch('../php/get_students.php')
            .then(response => response.json())
            .then(data => {
                const studentsList = document.getElementById("students-list");
                const studentCount = document.getElementById("student-count");

                data.forEach(student => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td><input type="checkbox" class="checkbox" data-id="${student.id}"></td>
                        <td>${student.name}</td>
                        <td>${student.code}</td>
                        <td>${student.class}</td>
                        <td>
                            <input type="radio" name="attendance-${student.id}" value="attendance" checked> حضور
                            <input type="radio" name="attendance-${student.id}" value="absence"> غياب
                        </td>
                    `;
                    studentsList.appendChild(row);
                });


                // عرض عدد الطلاب
                studentCount.innerText = data.length;
            })
            .catch(error => console.error('Error fetching data:', error));
            

        }

        // التحقق من الطلاب المحددين
        function getSelectedStudents() {
            const selectedStudents = [];
            const checkboxes = document.querySelectorAll(".checkbox:checked");
            checkboxes.forEach(checkbox => {
                selectedStudents.push(checkbox.getAttribute('data-id'));
            });
            return selectedStudents;
        }

        // عملية تأكيد الحذف
        function confirmDelete() {
            const selectedStudents = getSelectedStudents();
            if (selectedStudents.length > 0) {
                if (confirm("هل تريد حذف الطلاب المحددين؟")) {
                    deleteSelectedStudents(selectedStudents);
                }
            } else {
                alert("لم يتم اختيار أي طالب للحذف");
            }
        }

        // حذف الطلاب المحددين
        function deleteSelectedStudents(studentIds) {
            fetch('../php/delete_student.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: studentIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // إزالة الصفوف المحذوفة من الصفحة
                    studentIds.forEach(id => {
                        const row = document.querySelector(`.checkbox[data-id='${id}']`).closest("tr");
                        row.remove();
                    });

                    // تحديث عداد الطلاب
                    const studentCount = document.getElementById("student-count");
                    studentCount.innerText = parseInt(studentCount.innerText) - studentIds.length;
                } else {
                    alert("فشل في حذف الطلاب");
                }
            })
            .catch(error => console.error('Error deleting students:', error));
        }

        function toggleMenu() {
    let sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
}

function markAttendance(name, studentClass, isAbsent) {
    const absenceData = {
        name: name,
        class: studentClass,
        absence: isAbsent,
        "absence-date": new Date().toISOString().split('T')[0] // تاريخ اليوم
    };

    return new Promise((resolve, reject) => {
        fetch('../php/mark_absence.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(absenceData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resolve();
            } else {
                reject("فشل في تعديل الحضور/الغياب");
            }
        })
        .catch(error => reject(error));
    });
}



function searchStudents() {
    const searchInput = document.getElementById("search-input").value.toLowerCase();
    const rows = document.querySelectorAll("#students-list tr");

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const code = row.cells[2].textContent.toLowerCase();
        const studentClass = row.cells[3].textContent.toLowerCase();

        // التحقق مما إذا كان النص المدخل في مربع البحث موجودًا في الاسم أو الكود أو الفصل
        if (name.includes(searchInput) || code.includes(searchInput) || studentClass.includes(searchInput)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

function applyAttendanceChanges() {
    const selectedStudents = getSelectedStudents();
    if (selectedStudents.length > 0) {
        // إظهار صورة التحميل
        document.getElementById("loading").style.display = "block";
        
        let completedRequests = 0; // عداد لعدد العمليات المكتملة

        selectedStudents.forEach(studentId => {
            const radioButtons = document.querySelectorAll(`input[name="attendance-${studentId}"]`);
            let isAbsent = false;
            radioButtons.forEach(radio => {
                if (radio.checked && radio.value === "absence") {
                    isAbsent = true;
                }
            });
            const studentRow = document.querySelector(`.checkbox[data-id='${studentId}']`).closest("tr");
            const studentName = studentRow.cells[1].textContent;
            const studentClass = studentRow.cells[3].textContent;

            markAttendance(studentName, studentClass, isAbsent)
            .then(() => {
                completedRequests++;
                // إذا تم إتمام جميع العمليات، إخفاء صورة التحميل
                if (completedRequests === selectedStudents.length) {
                    document.getElementById("loading").style.display = "none";
                    alert("تم تسجيل الغياب بنجاح");
                }
            })
            .catch(error => {
                completedRequests++;
                console.error('Error marking attendance:', error);
                if (completedRequests === selectedStudents.length) {
                    document.getElementById("loading").style.display = "none";
                    alert("حدث خطأ أثناء تسجيل الغياب");
                }
            });
        });
    } else {
        alert("لم يتم اختيار أي طالب");
    }
}


function toggleSelectAll() {
    const checkboxes = document.querySelectorAll(".checkbox");
    const allSelected = Array.from(checkboxes).every(checkbox => checkbox.checked);
    checkboxes.forEach(checkbox => checkbox.checked = !allSelected);
}


document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector(".dropbtn");
        button.addEventListener("click", function (event) {
            // إغلاق أي قائمة مفتوحة أخرى
            dropdowns.forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove("active");
                }
            });

            // تبديل حالة القائمة
            dropdown.classList.toggle("active");

            // منع إغلاق القائمة عند النقر على عناصرها
            event.stopPropagation();
        });
    });

    // إغلاق القوائم عند النقر في أي مكان آخر في الصفحة
    document.addEventListener("click", function () {
        dropdowns.forEach(dropdown => dropdown.classList.remove("active"));
    });
});

