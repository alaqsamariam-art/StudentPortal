<?php 
/**
 * --------------------------------------------------------------------
 * صفحة استعراض الشكاوى وردود الإدارة (complaint_replies.php) - خاص بالطالب
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

// 2. استدعاء ملفات الاتصال بالداتابيز المعتمدة في مشروعكم
require_once "includes/config.php";   

// جلب رقم الطالب الحالي من الجلسة النشطة
$user_id = $_SESSION['user_id']; 

// 3. استعلام SQL ذكي ومدمج (JOIN) لجلب الشكاوى مع ردود الإدارة عليها إن وجدت
$query = "SELECT c.*, cr.reply AS admin_reply, cr.reply_date 
          FROM complaints c
          LEFT JOIN complaint_replies cr ON c.id = cr.complaint_id
          WHERE c.user_id = '$user_id'
          ORDER BY c.created_at DESC";

$result = mysqli_query($conn, $query);

// استدعاء الهيدر والسايد بار لتضمين التصميم الموحد
include "header.php";
include "sidebar.php";
?>

<!-- محتوى الصفحة الرئيسي المتناسق مع لوحة تحكم الطالب -->
<div class="main-content" style="padding: 20px; min-height: 85vh; direction: rtl;">
  <div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <!-- عنوان الصفحة الأنيق -->
            <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                <i class="bi bi-journal-text text-primary fs-2 me-3"></i>
                <div class="ms-3">
                    <h3 class="fw-bold text-dark mb-0">سجل الشكاوى وردود الإدارة</h3>
                    <p class="text-muted small mb-0">هنا يمكنكِ متابعة حالة طلباتكِ والاطلاع على ردود الدعم الفني والأكاديمي.</p>
                </div>
            </div>

            <!-- جدول استعراض الشكاوى -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="p-3" style="width: 15%;">القسم / النوع</th>
                                    <th class="p-3" style="width: 35%;">تفاصيل الشكوى</th>
                                    <th class="p-3" style="width: 15%;">تاريخ التقديم</th>
                                    <th class="p-3" style="width: 15%;">الحالة</th>
                                    <th class="p-3" style="width: 20%;">رد الإدارة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // تحديد لون الشارة بناءً على حالة الشكوى لتعزيز تجربة المستخدم UX
                                        $badge_class = "bg-info text-dark";
                                        if ($row['status'] == 'قيد المعالجة') $badge_class = "bg-warning text-dark";
                                        if ($row['status'] == 'مغلقة') $badge_class = "bg-success text-white";
                                ?>
                                        <tr>
                                            <!-- نوع القسم القادم من البوت -->
                                            <td class="p-3 fw-bold text-primary">
                                                <span class="badge bg-light text-primary border border-primary px-2 py-1">
                                                    <?php echo htmlspecialchars($row['complaint_type']); ?>
                                                </span>
                                            </td>
                                            
                                            <!-- تفاصيل نص الشكوى -->
                                            <td class="p-3 text-secondary">
                                                <?php echo htmlspecialchars($row['complaint_details']); ?>
                                            </td>
                                            
                                            <!-- تاريخ الإرسال التلقائي -->
                                            <td class="p-3 text-muted" style="font-size: 13px;">
                                                <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>
                                            </td>
                                            
                                            <!-- شارة الحالة الديناميكية -->
                                            <td class="p-3">
                                                <span class="badge <?php echo $badge_class; ?> rounded-pill px-3 py-1">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                            
                                            <!-- عمود رد الإدارة التفاعلي -->
                                            <td class="p-3">
                                                <?php if (!empty($row['admin_reply'])) { ?>
                                                    <div class="bg-light p-2 rounded border-start border-success border-3" style="font-size: 13px;">
                                                        <strong class="text-success d-block mb-1"><i class="bi bi-reply-fill"></i> الرد:</strong>
                                                        <?php echo htmlspecialchars($row['admin_reply']); ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <span class="text-muted italic text-center d-block" style="font-size: 13px;">
                                                        <i class="bi bi-hourglass-split"></i> بانتظار المراجعة...
                                                    </span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php 
                                    }
                                } else { 
                                ?>
                                    <!-- رسالة تظهر في حال لم يسبق للطالب تقديم أي شكاوى -->
                                    <tr>
                                        <td colspan="5" class="text-center p-5 text-muted">
                                            <i class="bi bi-chat-left-x fs-1 d-block mb-3 text-secondary"></i>
                                            لا توجد لديكِ أي شكاوى مسجلة حالياً في النظام.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
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