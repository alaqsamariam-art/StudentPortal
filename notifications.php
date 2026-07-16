<?php 
/**
 * --------------------------------------------------------------------
 * صفحة نظام الإشعارات والتنبيهات (notifications.php) - خاص بالطالب
 * --------------------------------------------------------------------
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. الحماية الأمنية: التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. استدعاء ملف الاتصال بالداتابيز
require_once "includes/config.php";   

$user_id = $_SESSION['user_id']; 

// 3. الإجراء الذكي: بمجرد دخول الطالب للصفحة، نحدث كل إشعاراته غير المقروءة لتصبح مقروءة (is_read = 1)
$update_query = "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id' AND is_read = 0";
mysqli_query($conn, $update_query);

// 4. جلب كافة الإشعارات الخاصة بهذا الطالب من قاعدة البيانات لترتيبها من الأحدث للأقدم
$query = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// استدعاء الهيدر والسايد بار مباشرة من المجلد الرئيسي للموقع
include "header.php";
include "sidebar.php";
?>

<!-- محتوى الصفحة الرئيسي المتناسق مع الهوية البصرية المرنة للموقع -->
<div class="main-content" style="padding: 20px; min-height: 85vh; direction: rtl;">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-11">
                
                <!-- عنوان الصفحة الأنيق -->
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                    <i class="bi bi-bell text-primary fs-2 me-3 animate__animated animate__swing"></i>
                    <div class="ms-3">
                        <h3 class="fw-bold text-dark mb-0">مركز الإشعارات والتنبيهات</h3>
                        <p class="text-muted small mb-0">تابع آخر المستجدات والردود الواردة من إدارة البوابة أولاً بأول.</p>
                    </div>
                </div>

                <!-- عرض الإشعارات -->
                <div class="row">
                    <div class="col-12">
                        <?php 
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <!-- بطاقة الإشعار التفاعلية -->
                                <div class="card shadow-sm border-0 rounded-3 mb-3 border-start border-primary border-4 bg-white">
                                    <div class="card-body d-flex align-items-center justify-content-between p-3 flex-wrap gap-3">
                                        <div class="d-flex align-items-center">
                                            <!-- أيقونة التنبيه -->
                                            <div class="bg-light-primary text-primary p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #e6f0ff;">
                                                <i class="bi bi-chat-left-dots fs-5 text-primary"></i>
                                            </div>
                                            <!-- نص التنبيه والتاريخ -->
                                            <div class="ms-2">
                                                <p class="mb-1 text-dark fw-semibold" style="font-size: 14px;">
                                                    <?php echo htmlspecialchars($row['message']); ?>
                                                </p>
                                                <span class="text-muted" style="font-size: 12px;">
                                                    <i class="bi bi-clock me-1 text-secondary"></i> <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- زر سريع للانتقال لجدول الردود ومراجعة الشكوى -->
                                        <a href="complaint_replies.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            عرض التفاصيل <i class="bi bi-arrow-left small ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                        <?php 
                            }
                        } else { 
                        ?>
                            <!-- رسالة تظهر في حال كان مركز الإشعارات فارغاً -->
                            <div class="card shadow-sm border-0 rounded-3 p-5 text-center text-muted">
                                <div class="card-body">
                                    <i class="bi bi-bell-slash fs-1 d-block mb-3 text-secondary"></i>
                                    <h5 class="fw-bold text-dark">صندوق الإشعارات فارغ</h5>
                                    <p class="small mb-0">لا توجد لديكِ أي تنبيهات أو إشعارات جديدة في الوقت الحالي.</p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php 
// استدعاء الفوتر الموحد للمشروع لغلق التاجات والسكربتات
include "footer.php"; 
?>