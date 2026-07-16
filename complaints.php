<?php 
/**
 * --------------------------------------------------------------------
 * كود الـ Backend: معالجة البيانات وحفظ شكاوى البوت في قاعدة البيانات
 * --------------------------------------------------------------------
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. الحماية الأمنية: التحقق من تسجيل الدخول (مربوط بـ login.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. استدعاء ملفات الاتصال والدوال المعتمدة في المشروع الجديد لزميلتكِ
require_once "includes/config.php";   // يحتوي على اتصال قاعدة البيانات $conn
require_once "includes/functions.php"; // يحتوي على الدالة الأمنية clean()

// متغير لتخزين رسالة النجاح لكي نعرضها داخل صندوق المحادثة لاحقاً
$submission_success = false;

// الحل هنا: تعريف المتغير بقيمة افتراضية فارغة لمنع تحذير الفيجوال ستوديو نهائياً
$complaint_type = "";

// 3. الفحص البرمجي: التأكد من أن الطالب قام بالضغط على زر الإرسال الصريح عبر طريقة POST
if (isset($_POST['submit_complaint_btn'])) {
    
    // استقبال البيانات القادمة من الفورم وتطهيرها باستخدام الدالة المعتمدة في المشروع clean()
    $complaint_type = clean($_POST['complaint_type']);
    $complaint_details = clean($_POST['complaint_details']);
    
    // جلب رقم الطالب الحقيقي والنشط حالياً من الجلسة الأمنيّة (Session)
    $user_id = $_SESSION['user_id']; 
    
    // 4. كتابة استعلام الـ SQL المدمج مع هيكل الجدول الجديد تماماً
    $query = "INSERT INTO complaints (user_id, complaint_type, complaint_details, status, created_at) 
              VALUES ('$user_id', '$complaint_type', '$complaint_details', 'جديدة', NOW())";
              
    // 5. تنفيذ الاستعلام وفحص النتيجة
    if (mysqli_query($conn, $query)) {
        // إذا نجح الإدخال، نغير حالة المتغير إلى true ليعلم نظام العرض بذلك
        $submission_success = true;
    } else {
        // في حال حدوث خطأ في قاعدة البيانات، يظهر تنبيه برمجي واضح للمطور
        die("خطأ في إرسال الشكوى لقاعدة البيانات: " . mysqli_error($conn));
    }
}

/**
 * --------------------------------------------------------------------
 * كود الـ Frontend: بناء هيكل صفحة الشكاوى الذكية (صندوق المحادثة - Chatbox)
 * --------------------------------------------------------------------
 */
require_once 'header.php'; 
require_once 'sidebar.php'; 
?>

<!-- المحتوى الرئيسي لصفحة الشكاوى (تم تعديل الهوامش والكلاسات للتناسق الكامل) -->
<div class="main-content" style="padding: 20px; min-height: 85vh; direction: rtl;">
    <div class="container py-4">
        <div class="row justify-content-center">
            
            <div class="col-md-8 col-lg-6">
                
                <div class="card shadow border-0 rounded-4">
                    
                    <!-- 1️⃣ رأس صندوق المحادثة (Card Header) -->
                    <div class="card-header bg-primary text-white p-3 d-flex align-items-center rounded-top-4">
                        <i class="bi bi-robot fs-3 me-2"></i>
                        <div class="ms-2">
                            <h6 class="mb-0 fw-bold text-white">المساعد الذكي للشكاوى</h6>
                            <small class="text-white-50">متصل الآن لمساعدتكِ يا <?php echo htmlspecialchars($_SESSION['full_name']); ?></small>
                        </div>
                    </div>

                    <!-- 2️⃣ منطقة ظهور الرسائل والخيارات (Card Body) -->
                    <div class="card-body p-4 bg-light" id="chatArea" style="height: 400px; overflow-y: auto;">
                        
                        <?php if ($submission_success): ?>
                            <!-- إذا تم حفظ الشكوى بنجاح في قاعدة البيانات، يعرض البوت رسالة تأكيد فورية ومبهجة للطالب -->
                            <div class="d-flex mb-3 text-end animate__animated animate__fadeIn">
                                <div class="bg-success text-white p-3 rounded-3 w-100" style="font-size: 15px;">
                                    <i class="bi bi-check-circle-fill me-1"></i> شكراً لكِ يا <?php echo htmlspecialchars($_SESSION['full_name']); ?>! لقد استلمتُ تفاصيل شكوتكِ بنجاح تحت تصنيف (<?php echo htmlspecialchars($complaint_type); ?>)، وتم تسجيلها في النظام وتحويلها إلى لوحة الإدارة للمتابعة الفورية.
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- الرسالة المؤقتة الافتراضية تظهر فقط إذا لم يتم الإرسال بعد، وتتحكم بها الجافاسكريبت -->
                            <div class="text-center text-muted my-5">
                                <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                                <p>جاري تحميل محادثة الدعم الفني...</p>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- 3️⃣ منطقة إدخال وتفاصيل المشكلة (Card Footer) -->
                    <div class="card-footer bg-white p-3 rounded-bottom-4">
                        
                        <form id="complaintForm" action="complaints.php" method="POST">
                            <div class="input-group">
                                
                                <textarea class="form-control" rows="1" id="studentInput" name="complaint_details" placeholder="اكتب تفاصيل مشكلتك هنا..." style="resize: none;" disabled></textarea>
                                
                                <button class="btn btn-primary ms-2" type="submit" id="sendBtn" name="submit_complaint_btn" disabled>
                                    <i class="bi bi-send-fill"></i>
                                </button>
                                
                            </div>
                        </form>
                        
                    </div>

                </div> <!-- نهاية الـ Card -->

            </div>
        </div>
    </div>
</div>

<?php 
require_once 'footer.php'; 
?>