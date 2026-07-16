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

// التحقق من صلاحيات الأدمن
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
// 2. جلب بيانات المستخدم المراد تعديله
// ==========================================
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // استعلام لجلب بيانات المستخدم المحدد
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        header("Location: admin_users.php");
        exit();
    }
} else {
    header("Location: admin_users.php");
    exit();
}

// ==========================================
// 3. معالجة تحديث البيانات الشامل عند إرسال الفورم
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تطهير وتنظيف جميع المدخلات لمنع ثغرات SQL Injection تمامًا
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $major     = mysqli_real_escape_string($conn, trim($_POST['major']));
    $level     = mysqli_real_escape_string($conn, trim($_POST['level']));
    $role      = intval($_POST['role']);
    
    // تحديث البيانات الشاملة في قاعدة البيانات بأمان
    $update_query = "UPDATE users SET 
                        full_name = '$full_name', 
                        email = '$email', 
                        phone = '$phone', 
                        major = '$major', 
                        level = '$level', 
                        role = $role 
                     WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success_msg = "تم تحديث بيانات المستخدم بنجاح!";
        // إعادة جلب البيانات المحدثة لتظهر بشكل صحيح ومباشر في الحقول أمام الأدمن
        $user['full_name'] = $full_name;
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['major'] = $major;
        $user['level'] = $level;
        $user['role'] = $role;
    } else {
        $error_msg = "حدث خطأ أثناء تحديث البيانات: " . mysqli_error($conn);
    }
}
?>

<!-- ========================================== -->
<!-- 4. واجهة العرض والتصميم (HTML & Bootstrap) -->
<!-- ========================================== -->
<div class="main-content" style="padding: 20px; direction: rtl;">
    <div class="container-fluid mt-4">
        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> تعديل بيانات المستخدم الشاملة</h5>
                <a href="admin_users.php" class="btn btn-sm btn-dark px-3">العودة للخلف</a>
            </div>
            <div class="card-body p-4 bg-white">
                
                <!-- عرض رسائل النجاح أو الفشل -->
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4"><i class="bi bi-check-circle-fill me-2"></i><?php echo $success_msg; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="bi bi-x-circle-fill me-2"></i><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
                    
                    <div class="row">
                        <!-- حقل الاسم الكامل -->
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label fw-bold">الاسم الكامل</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        
                        <!-- حقل البريد الإلكتروني -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- حقل رقم الجوال -->
                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label fw-bold">رقم الجوال</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>

                        <!-- حقل التخصص الدراسي -->
                        <div class="col-md-4 mb-3">
                            <label for="major" class="form-label fw-bold">التخصص الدراسي</label>
                            <select class="form-select" id="major" name="major">
                                <option value="" <?php echo (empty($user['major'])) ? 'selected' : ''; ?>>لم يحدد بعد</option>
                                <option value="تطوير مواقع" <?php echo ($user['major'] == 'تطوير مواقع') ? 'selected' : ''; ?>>تطوير مواقع (Web Development)</option>
                                <option value="هندسة برمجيات" <?php echo ($user['major'] == 'هندسة برمجيات') ? 'selected' : ''; ?>>هندسة برمجيات (Software Engineering)</option>
                            </select>
                        </div>

                        <!-- حقل المستوى الدراسي -->
                        <div class="col-md-4 mb-3">
                            <label for="level" class="form-label fw-bold">المستوى الدراسي</label>
                            <select class="form-select" id="level" name="level">
                                <option value="" <?php echo (empty($user['level'])) ? 'selected' : ''; ?>>لم يحدد بعد</option>
                                <option value="1" <?php echo ($user['level'] == '1') ? 'selected' : ''; ?>>المستوى الأول</option>
                                <option value="2" <?php echo ($user['level'] == '2') ? 'selected' : ''; ?>>المستوى الثاني</option>
                                <option value="3" <?php echo ($user['level'] == '3') ? 'selected' : ''; ?>>المستوى الثالث</option>
                                <option value="4" <?php echo ($user['level'] == '4') ? 'selected' : ''; ?>>المستوى الرابع</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- حقل نوع الحساب (الصلاحية) -->
                    <div class="mb-4 col-md-6">
                        <label for="role" class="form-label fw-bold">صلاحية الحساب (الرتبة بالنظام)</label>
                        <select class="form-select" id="role" name="role">
                            <option value="0" <?php echo ($user['role'] == 0) ? 'selected' : ''; ?>>طالب (Student)</option>
                            <option value="1" <?php echo ($user['role'] == 1) ? 'selected' : ''; ?>>مسؤول (Admin)</option>
                        </select>
                    </div>
                    
                    <!-- أزرار الحفظ -->
                    <div class="mt-4 border-top pt-3 text-end">
                        <button type="submit" class="btn btn-success px-4 me-2"><i class="bi bi-save me-1"></i> حفظ كافة التعديلات</button>
                        <a href="admin_users.php" class="btn btn-secondary px-4">إلغاء</a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>