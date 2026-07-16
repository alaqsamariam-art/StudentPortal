// ==========================================================================
// Smart Student Portal JavaScript (الملف النهائي المعتمد والمدمج لـ سما)
// ==========================================================================

// عند تحميل الصفحة بالكامل في المتصفح
document.addEventListener("DOMContentLoaded", function () {

    // ==========================================  
    // 1. إخفاء رسائل التنبيه والنجاح تلقائياً بعد 3 ثوانٍ  
    // ==========================================  
    setTimeout(function () {  
        let alerts = document.querySelectorAll(".alert");  
        alerts.forEach(function(alert){  
            alert.style.display = "none";  
        });  
    }, 3000);  


    // ==========================================  
    // 2. زر العودة للأعلى عند التمرير لأسفل الصفحة  
    // ==========================================  
    let topButton = document.getElementById("topBtn");  
    window.onscroll = function(){  
        if(topButton){  
            if(document.body.scrollTop > 200 || document.documentElement.scrollTop > 200){  
                topButton.style.display = "block";  
            } else {  
                topButton.style.display = "none";  
            }  
        }  
    };  


    // ==========================================  
    // 3. ميزة الوضع الليلي التفاعلية والمزامنة (هيدر + سايدبار)
    // ==========================================  
    const darkModeBtn = document.getElementById("darkModeBtn");  
    const lightModeBtn = document.getElementById("lightModeBtn");  
    const sidebarDarkModeBtn = document.getElementById("sidebarDarkModeBtn");  
    const sidebarLightModeBtn = document.getElementById("sidebarLightModeBtn");  

    // دالة لتطبيق الوضع المظلم وتحديث كافة الأزرار
    function enableDarkMode() {
        document.body.classList.add("dark-mode");  
        localStorage.setItem("theme", "dark");  
        
        // إخفاء القمر وإظهار الشمس في الهيدر
        if (darkModeBtn) darkModeBtn.classList.add("d-none"); 
        if (lightModeBtn) lightModeBtn.classList.remove("d-none"); 

        // إخفاء القمر وإظهار الشمس في السايدبار
        if (sidebarDarkModeBtn) sidebarDarkModeBtn.classList.add("d-none");
        if (sidebarLightModeBtn) sidebarLightModeBtn.classList.remove("d-none");
    }

    // دالة لتطبيق الوضع الفاتح وتحديث كافة الأزرار
    function disableDarkMode() {
        document.body.classList.remove("dark-mode");  
        localStorage.setItem("theme", "light");  
        
        // إخفاء الشمس وإظهار القمر في الهيدر
        if (lightModeBtn) lightModeBtn.classList.add("d-none"); 
        if (darkModeBtn) darkModeBtn.classList.remove("d-none"); 

        // إخفاء الشمس وإظهار القمر في السايدبار
        if (sidebarLightModeBtn) sidebarLightModeBtn.classList.add("d-none");
        if (sidebarDarkModeBtn) sidebarDarkModeBtn.classList.remove("d-none");
    }

    // فحص الخيار المحفوظ مسبقاً عند تحميل الصفحة
    if (localStorage.getItem("theme") === "dark") {  
        enableDarkMode();
    } else {
        disableDarkMode();
    }  

    // مستمع الأحداث لأزرار القمر (التحويل لليلي)
    if (darkModeBtn) {  
        darkModeBtn.addEventListener("click", function() { enableDarkMode(); });  
    }
    if (sidebarDarkModeBtn) {
        sidebarDarkModeBtn.addEventListener("click", function() { enableDarkMode(); });
    }

    // مستمع الأحداث لأزرار الشمس (التحويل للفاتح)
    if (lightModeBtn) {  
        lightModeBtn.addEventListener("click", function() { disableDarkMode(); });  
    }
    if (sidebarLightModeBtn) {
        sidebarLightModeBtn.addEventListener("click", function() { disableDarkMode(); });
    }


    // ==========================================
    // 4. ميزة البوت الآلي الذكي لنظام الشكاوى (الربط المتوافق والمطور)
    // ==========================================
    
    // جلب عناصر الواجهة المحددة في صفحة الشكاوى complaints.php
    const chatArea = document.getElementById("chatArea");
    const studentInput = document.getElementById("studentInput");
    const sendBtn = document.getElementById("sendBtn");
    const complaintForm = document.getElementById("complaintForm");

    // فحص شرطي: نتحقق من أننا داخل صفحة الشكاوى فقط لمنع حدوث أخطاء جافاسكريبت في الصفحات الأخرى
    if (chatArea && studentInput && sendBtn && complaintForm) {
        
        // بدء المحادثة الآلية وتوليد الأزرار تلقائياً بعد ثانية واحدة لإعطاء طابع تفاعلي ذكي
        setTimeout(() => {
            chatArea.innerHTML = `
                <div class="d-flex mb-3 text-end animate__animated animate__fadeIn">
                    <div class="bg-primary text-white p-3 rounded-3" style="max-width: 85%; font-size: 15px;">
                        <i class="bi bi-robot me-1"></i> مرحباً بكِ في نظام الشكاوى الذكي. أنا هنا لمساعدتكِ، يرجى اختيار القسم أو نوع المشكلة التي تواجهكِ من الخيارات بالأسفل:
                    </div>
                </div>
                
                <!-- تم ربط قيم data-type هنا بالتصنيفات الافتراضية الأربعة المعتمدة في قاعدة البيانات -->
                <div class="d-grid gap-2 my-3" id="botOptions">
                    <button type="button" class="btn btn-outline-primary text-start option-btn p-2" data-type="أكاديمية">
                        <i class="bi bi-book me-2"></i> 1. شكوى أكاديمية (تسجيل، مواد، جداول)
                    </button>
                    <button type="button" class="btn btn-outline-primary text-start option-btn p-2" data-type="مالية">
                        <i class="bi bi-cash-coin me-2"></i> 2. شكوى مالية (أقساط، دفعات، منح)
                    </button>
                    <button type="button" class="btn btn-outline-primary text-start option-btn p-2" data-type="تقنية">
                        <i class="bi bi-shield-lock me-2"></i> 3. شكوى تقنية (حساب البوابة، مودل، شبكة)
                    </button>
                    <button type="button" class="btn btn-outline-primary text-start option-btn p-2" data-type="خدمات طلابية">
                        <i class="bi bi-chat-right-text me-2"></i> 4. خدمات طلابية واقتراحات عامة
                    </button>
                </div>
            `;
            // استدعاء دالة تشغيل الأحداث للأزرار بعد توليدها في الواجهة
            setupOptionsEvents();
        }, 1000); 

        // دالة تفعيل الضغط على خيارات البوت والتفاعل مع الطالب
        function setupOptionsEvents() {
            const buttons = document.querySelectorAll(".option-btn");
            buttons.forEach(button => {
                button.addEventListener("click", function () {
                    const selectedType = this.getAttribute("data-type");

                    // إزالة صندوق خيارات الأزرار فور الضغط عليها لمنع الطالب من التكرار أو التلاعب بالخيارات
                    document.getElementById("botOptions").remove();
                    
                    // طباعة خيار الطالب في صندوق المحادثة لتبدو كمحادثة حقيقية
                    chatArea.innerHTML += `
                        <div class="d-flex justify-content-end mb-3 animate__animated animate__fadeIn">
                            <div class="bg-secondary text-white p-3 rounded-3" style="max-width: 85%; font-size: 15px;">
                                لقد اخترت قسم: ${selectedType}
                            </div>
                        </div>
                    `;

                    // رد البوت التلقائي التالي وفتح حقول النص للإدخال المباشر
                    setTimeout(() => {
                        chatArea.innerHTML += `
                            <div class="d-flex mb-3 animate__animated animate__fadeIn">
                                <div class="bg-primary text-white p-3 rounded-3" style="max-width: 85%; font-size: 15px;">
                                    <i class="bi bi-robot me-1"></i> تمام، تم تحديد القسم بنجاح. يرجى الآن كتابة تفاصيل مشكلتكِ بدقة في الحقل الأسفل، ثم اضغطي على زر الإرسال.
                                </div>
                            </div>
                        `;
                        
                        // تفعيل حقل الكتابة وزر الإرسال ونقل مؤشر الكتابة تلقائياً للحقل (Focus)
                        studentInput.disabled = false;
                        sendBtn.disabled = false;
                        studentInput.focus();

                        // الإجراء السحري: إنشاء عنصر input مخفي (Hidden) وحقنه داخل نموذج الفورم
                        // هذا الإجراء يمرر اسم القسم (أكاديمية، مالية..) للـ PHP عند الضغط على إرسال الشكوى
                        let hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "complaint_type";
                        hiddenInput.value = selectedType;
                        complaintForm.appendChild(hiddenInput);

                        // النزول التلقائي لأسفل صندوق الشات لملاحقة الرسائل الجديدة
                        chatArea.scrollTop = chatArea.scrollHeight;
                    }, 800);
                });
            });
        }
    }

}); // نهاية حدث الـ DOMContentLoaded الموحد


// ==========================================
// 5. رسالة تأكيد نافذة الـ Browser قبل الحذف
// ==========================================
function confirmDelete(){
    return confirm("هل أنت متأكد من حذف هذا العنصر؟");
}