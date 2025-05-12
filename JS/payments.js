function fetchStudents() {
    let classSelected = document.getElementById("class").value;
    if (!classSelected) return;

    fetch("fees.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "fetch_students", class: classSelected })
    })
    .then(response => response.json())
    .then(data => {
        let table = document.getElementById("students-table");
        let tbody = table.querySelector("tbody");
        tbody.innerHTML = "";

        // إضافة صف جديد يحتوي على تاريخ اليوم في بداية الجدول
        let today = new Date().toISOString().split('T')[0];
        tbody.innerHTML += `
            <tr style="background: #ddd; font-weight: bold;">
                <td colspan="2">تاريخ اليوم: ${today}</td>
            </tr>`;

        data.forEach(student => {
            checkPaymentStatus(student.name, classSelected, function(paid) {
                let row;
                if (paid) {
                    row = `
                        <tr>
                            <td>${student.name}</td>
                            <td style="color: green; font-weight: bold;">تم الدفع بالفعل</td>
                        </tr>`;
                } else {
                    row = `
                        <tr>
                            <td>${student.name}</td>
                            <td><input type="number" min="0" data-name="${student.name}" data-class="${classSelected}" onchange="savePayment(this)"></td>
                        </tr>`;
                }
                tbody.innerHTML += row;
                table.style.display = "table";
            });
        });
    })
    .catch(error => console.error('خطأ:', error));
}

function checkPaymentStatus(name, className, callback) {
    fetch("fees.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "check_payment", name: name, class: className })
    })
    .then(response => response.json())
    .then(data => callback(data.paid))
    .catch(error => console.error("خطأ في التحقق من الدفع:", error));
}

function savePayment(input) {
    let name = input.getAttribute("data-name");
    let className = input.getAttribute("data-class");
    let amount = input.value;
    let today = new Date().toISOString().split('T')[0];

    if (amount <= 0) {
        alert("الرجاء إدخال مبلغ صالح");
        input.value = "";
        return;
    }

    fetch("fees.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "save_payment", name: name, class: className, amount: amount, payment_date: today })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("تم حفظ المبلغ بنجاح");
        } else {
            alert("خطأ في حفظ المبلغ");
        }
    })
    .catch(error => console.error("خطأ في الحفظ:", error));
}

