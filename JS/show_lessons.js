   // استخدام fetch للحصول على الصفوف والفصول والوحدات والدروس من الخادم
fetch('../php/fetch_class.php')
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('class-container');
        
        data.forEach(classItem => {
            if (!classItem.class || !classItem.semesters || classItem.semesters.length === 0) return; // تخطي إذا لم يكن هناك صفوف أو فصول

            // إنشاء زر لكل صف
            const classBtn = document.createElement('button');
            classBtn.classList.add('btn', 'class-btn');
            classBtn.innerText = "الصف : " + classItem.class;  

            const semesterContainer = document.createElement('div');
            semesterContainer.classList.add('btn-container');

            classItem.semesters.forEach(semesterItem => {
                if (!semesterItem.semester || !semesterItem.units || semesterItem.units.length === 0) return; // تخطي إذا لم يكن هناك فصل أو وحدات
                
                const semesterBtn = document.createElement('button');
                semesterBtn.classList.add('btn', 'semester-btn');
                semesterBtn.innerText = "الفصل : " + semesterItem.semester;

                const unitContainer = document.createElement('div');
                unitContainer.classList.add('btn-container');

                semesterItem.units.forEach(unitItem => {
                    if (!unitItem.unit || !unitItem.lessons || unitItem.lessons.length === 0) return; // تخطي إذا لم يكن هناك وحدة أو دروس
                    
                    const unitBtn = document.createElement('button');
                    unitBtn.classList.add('btn', 'unit-btn');
                    unitBtn.innerText = "الوحدة : " + unitItem.unit;

                    const lessonContainer = document.createElement('div');
                    lessonContainer.classList.add('btn-container');

                    unitItem.lessons.forEach(lesson => {
                        if (!lesson || lesson.trim() === "") return; // تخطي الدروس الفارغة أو null
                        
                        const lessonBtn = document.createElement('button');
                        lessonBtn.classList.add('btn', 'lesson-btn');
                        lessonBtn.innerText = "الحصة : " + lesson;

                        lessonBtn.addEventListener('click', () => {
                            localStorage.setItem('lessonName', lesson);
                            window.location.href = '../html/lesson_show.html';
                        });

                        lessonContainer.appendChild(lessonBtn);
                    });

                    unitContainer.appendChild(unitBtn);
                    unitContainer.appendChild(lessonContainer);

                    unitBtn.addEventListener('click', () => {
                        lessonContainer.classList.toggle('active');
                    });
                });

                semesterContainer.appendChild(semesterBtn);
                semesterContainer.appendChild(unitContainer);

                semesterBtn.addEventListener('click', () => {
                    unitContainer.classList.toggle('active');
                });
            });

            container.appendChild(classBtn);
            container.appendChild(semesterContainer);

            classBtn.addEventListener('click', () => {
                semesterContainer.classList.toggle('active');
            });
        });
    })
    .catch(error => console.log(error));

    
