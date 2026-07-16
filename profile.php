<?php 
/**
 * --------------------------------------------------------------------
 * صفحة الملف الشخصي الذكية والمطورة (profile.php) - تعرض بيانات الطالب أو الإدمن بالكامل
 * --------------------------------------------------------------------
 */
session_start();

// 1. الحماية الأمنية: التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. استدعاء ملف الاتصال بالداتابيز
require_once "includes/config.php";   

$user_id = intval($_SESSION['user_id']); 

// 3. جلب بيانات المستخدم الحالية من الداتابيز (بما فيها الحقول الجديدة)
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// استدعاء الهيدر والسايد بار الموحدين مباشرة من المجلد الرئيسي
require_once "header.php";
require_once "sidebar.php";
?>

<div class="main-content p-4" style="margin-left: 260px; min-height: 85vh; direction: rtl;">
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                
                <!-- عنوان الصفحة -->
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                    <i class="bi bi-person-badge text-primary fs-2 ms-3"></i>
                    <div>
                        <h3 class="fw-bold text-dark mb-0">الملف الشخصي للمستخدم</h3>
                        <p class="text-muted small mb-0">هنا تجد تفاصيل حسابك وصلاحياتك داخل النظام المعتمَد.</p>
                    </div>
                </div>

                <!-- بطاقة عرض البيانات الشخصية المطورة -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="bg-primary p-4 text-white text-center position-relative" style="background: linear-gradient(135deg, #0d6efd, #0b3d91) !important;">
                        <!-- تمييز الرتبة بناءً على النظام الرقمي المشترك (0 طالب، 1 أدمن) -->
                        <div class="position-absolute top-0 start-0 m-3">
                            <?php if ($user['role'] == 1) { ?>
                                <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-shield-lock-fill"></i> لوحة الإدارة</span>
                            <?php } else { ?>
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-mortarboard-fill"></i> حساب طالب</span>
                            <?php } ?>
                        </div>
                        
                        <!-- عرض الصورة الشخصية للطالب من مجلد uploads مع إطار أبيض أنيق وجذاب -->
                        <div class="mb-3 mt-2">
                            <?php 
                            // تحديد مسار الصورة؛ إن لم تكن متوفرة نستخدم الصورة الافتراضية
                            $user_image = (!empty($user['image'])) ? $user['image'] : 'default-avatar.jpg';
                            $image_path = "uploads/" . $user_image;
                            ?>
                            <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                 alt="الصورة الشخصية" 
                                 class="rounded-circle border border-4 border-white shadow-sm" 
                                 style="width: 110px; height: 110px; object-fit: cover;">
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                        <p class="mb-0 text-white-50" style="font-size: 14px;"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    
                    <div class="card-body p-4 bg-white text-start" style="text-align: right !important;">
                        <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom" style="font-size: 16px;"><i class="bi bi-card-checklist text-primary me-1"></i> البيانات والملفات المعتمدة</h5>
                        
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person text-primary"></i> الاسم الكامل</label>
                                <span class="text-dark fw-semibold"><?php echo htmlspecialchars($user['full_name']); ?></span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-envelope text-primary"></i> البريد الإلكتروني</label>
                                <span class="text-dark fw-semibold"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-telephone text-primary"></i> رقم الجوال</label>
                                <span class="text-dark fw-semibold"><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-shield-check text-primary"></i> نوع الصلاحية (Role)</label>
                                <span class="badge <?php echo ($user['role'] == 1) ? 'bg-danger' : 'bg-primary'; ?> px-2.5 py-1.5">
                                    <?php echo ($user['role'] == 1) ? 'مسؤول نظام / Admin' : 'طالب / Student'; ?>
                                </span>
                            </div>
                            
                            <!-- إظهار حقول التخصص والمستوى بشكل خاص ومميز للطالب فقط -->
                            <?php if ($user['role'] == 0) { ?>
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block mb-1"><i class="bi bi-book text-primary"></i> التخصص الدراسي</label>
                                    <span class="text-dark fw-semibold">
                                        <?php echo (!empty($user['major'])) ? htmlspecialchars($user['major']) : '<span class="text-muted fw-normal">لم يحدد بعد</span>'; ?>
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block mb-1"><i class="bi bi-layers text-primary"></i> المستوى الأكاديمي</label>
                                    <span class="text-dark fw-semibold">
                                        <?php echo (!empty($user['level'])) ? "المستوى " . htmlspecialchars($user['level']) : '<span class="text-muted fw-normal">لم يحدد بعد</span>'; ?>
                                    </span>
                                </div>
                            <?php } ?>

                            <div class="col-sm-6">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-calendar-event text-primary"></i> تاريخ انضمامك للنظام</label>
                                <span class="text-dark fw-semibold" style="font-size: 13px;">
                                    <?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php 
include "footer.php"; 
?>