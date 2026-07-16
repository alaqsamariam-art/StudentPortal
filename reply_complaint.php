<?php
// ==========================================
// 1. تشغيل الأخطاء وإدارة الجلسة والحماية
// ==========================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";
require_once "header.php";
require_once "sidebar.php";

$error_msg = "";
$success_msg = "";

// ==========================================
// 2. جلب الشكوى المراد الرد عليها
// ==========================================
if (isset($_GET['id'])) {
    $complaint_id = intval($_GET['id']);
    
    $query = "SELECT complaints.*, users.full_name 
              FROM complaints 
              JOIN users ON complaints.user_id = users.id 
              WHERE complaints.id = $complaint_id";
              
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $complaint = mysqli_fetch_assoc($result);
    } else {
        header("Location: admin_complaints.php");
        exit();
    }
} else {
    header("Location: admin_complaints.php");
    exit();
}

// ==========================================
// 3. معالجة إرسال الرد وإرسال إشعار تلقائي
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reply_text = mysqli_real_escape_string($conn, trim($_POST['reply_text']));
    $admin_id = $_SESSION['user_id'];
    
    if (!empty($reply_text)) {
        // أولاً: إدخال الرد في جدول ردود الشكاوى
        $insert_query = "INSERT INTO complaint_replies (complaint_id, admin_id, reply) 
                         VALUES ($complaint_id, $admin_id, '$reply_text')";
        
        if (mysqli_query($conn, $insert_query)) {
            
            // ثانياً: تحديث حالة الشكوى الأصلية لتصبح "مغلقة"
            $update_query = "UPDATE complaints SET status = 'مغلقة' WHERE id = $complaint_id";
            mysqli_query($conn, $update_query);
            
            // ثالثاً: إرسال إشعار للطالب في جدول الإشعارات (Notifications)
            $student_id = intval($complaint['user_id']);
            $comp_type_safe = mysqli_real_escape_string($conn, $complaint['complaint_type']);
            
            $notif_message = "تم الرد من قبل الإدارة على شكواكِ الخاصة بقسم: (" . $comp_type_safe . ")";
            $notif_query = "INSERT INTO notifications (user_id, message, is_read, created_at) 
                            VALUES ($student_id, '$notif_message', 0, NOW())";
            mysqli_query($conn, $notif_query);
            
            $success_msg = "تم إرسال الرد بنجاح، وإغلاق الشكوى، وتم تنبيه الطالب بإشعار!";
            $complaint['status'] = 'مغلقة';
        } else {
            $error_msg = "حدث خطأ أثناء إرسال الرد: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "الرجاء كتابة رد رسمي قبل الإرسال.";
    }
}
?>

<!-- واجهة التصميم المعتادة المتناسقة -->
<div class="main-content" style="padding: 20px; direction: rtl;">
    <div class="container-fluid mt-4">
        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-reply-fill me-2"></i> الرد على الشكوى رقم #<?php echo $complaint_id; ?></h5>
                <a href="admin_complaints.php" class="btn btn-sm btn-light px-3">العودة للخلف</a>
            </div>
            <div class="card-body p-4 bg-white">
                
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4"><i class="bi bi-check-circle-fill me-2"></i><?php echo $success_msg; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="bi bi-x-circle-fill me-2"></i><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <strong>اسم الطالب:</strong>
                        <p class="form-control-plaintext border-bottom fw-semibold text-dark"><?php echo htmlspecialchars($complaint['full_name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>قسم الشكوى:</strong>
                        <p class="form-control-plaintext border-bottom text-primary fw-semibold"><?php echo htmlspecialchars($complaint['complaint_type']); ?></p>
                    </div>
                    <div class="col-12 mb-3">
                        <strong>تفاصيل الشكوى المقدمة:</strong>
                        <div class="p-3 bg-light rounded border text-secondary" style="white-space: pre-line;">
                            <?php echo nl2br(htmlspecialchars($complaint['complaint_details'])); ?>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <strong class="d-block mb-1">حالة الشكوى الحالية:</strong>
                        <?php if ($complaint['status'] == 'جديدة'): ?>
                            <span class="badge bg-warning text-dark fs-6 px-3 py-1.5">جديدة</span>
                        <?php elseif ($complaint['status'] == 'قيد المعالجة'): ?>
                            <span class="badge bg-info text-dark fs-6 px-3 py-1.5">قيد المعالجة</span>
                        <?php else: ?>
                            <span class="badge bg-success text-white fs-6 px-3 py-1.5"><i class="bi bi-check-lg"></i> مغلقة (تم الرد وحل المشكلة)</span>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-4">

                <?php if ($complaint['status'] != 'مغلقة'): ?>
                    <form action="reply_complaint.php?id=<?php echo $complaint_id; ?>" method="POST" class="mt-2">
                        <div class="mb-3">
                            <label for="reply_text" class="form-label fw-bold">اكتب رد الإدارة الرسمي لحل المشكلة:</label>
                            <textarea class="form-control" id="reply_text" name="reply_text" rows="5" placeholder="أهلاً بكِ عزيزتي الطالبة، تم مراجعة الشكوى وقررنا..." required></textarea>
                        </div>
                        
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success px-4 me-2"><i class="bi bi-send-fill me-1"></i> إرسال الرد وحل المشكلة</button>
                            <a href="admin_complaints.php" class="btn btn-secondary px-4">إلغاء</a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mt-3 text-center border-0 shadow-sm">
                        <i class="bi bi-info-circle-fill me-1 fs-5"></i> تم الرد على هذه الشكوى وإغلاقها مسبقاً بنجاح، ولا يمكن إجراء تعديلات إضافية عليها الآن.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php 
include "footer.php"; 
?>