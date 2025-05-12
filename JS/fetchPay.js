function fetchStudents() {
    let classSelected = document.getElementById("class").value;
    let dateSelected = document.getElementById("date").value;
    if (!classSelected || !dateSelected) return;

    fetch("payments_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "fetch_students", class: classSelected, date: dateSelected })
    })
    .then(response => response.json())
    .then(data => {
        let table = document.getElementById("students-table");
        let tbody = table.querySelector("tbody");
        let totalPaymentDiv = document.getElementById("total-payment");

        tbody.innerHTML = "";
        data.students.forEach(student => {
            let row = `
                <tr>
                    <td>${student.name}</td>
                    <td class="${student.total_paid > 0 ? 'paid' : 'not-paid'}">${student.status}</td>
                </tr>`;
            tbody.innerHTML += row;
        });

        table.style.display = "table";
        totalPaymentDiv.innerHTML = `إجمالي المدفوعات للفصل لهذا الشهر: <span style="color: blue;">${data.total_payment} جنيه</span>`;
        totalPaymentDiv.style.display = "block";
    })
    .catch(error => console.error('خطأ:', error));
}
function toggleMenu() {
    let sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
}