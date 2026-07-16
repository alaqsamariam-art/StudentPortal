<?php
session_start();

require_once "includes/config.php";
require_once "includes/functions.php";

$message = "";

if (isset($_POST['register'])) {
    $full_name = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = clean($_POST['phone']);
    $major = !empty($_POST['major']) ? clean($_POST['major']) : null;
    $level = !empty($_POST['level']) ? intval($_POST['level']) : null;

    // 1. التحقق من تعبئة الحقول الإجبارية
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($phone) || empty($_FILES['image']['name'])) {
        $message = showMessage("يرجى تعبئة جميع الحقول الإجبارية ورفع الصورة الشخصية.", "danger");
    } 
    // 2. التحقق من رقم الجوال (يجب أن يكون 10 خانات رقمية فقط)
    elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = showMessage("رقم الجوال غير صحيح، يجب أن يتكون من 10 أرقام فقط.", "danger");
    } 
    // 3. التحقق من تطابق كلمتي المرور
    elseif ($password != $confirm_password) {
        $message = showMessage("كلمتا المرور غير متطابقتين.", "danger");
    } 
    else {
        // 4. التحقق من أن البريد الإلكتروني غير مسجل مسبقاً
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = showMessage("البريد الإلكتروني مستخدم مسبقاً.", "warning");
        } else {
            
            // 5. معالجة رفع الصورة الشخصية
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_extensions = array("jpg", "jpeg", "png");

            // التحقق من امتداد الصورة المرفوعة
            if (!in_array($image_ext, $allowed_extensions)) {
                $message = showMessage("امتداد الصورة غير مسموح به! الصيغ المقبولة فقط: JPG, JPEG, PNG.", "danger");
            } else {
                // إنشاء مجلد uploads في حال لم يكن موجوداً في مجلد المشروع
                $upload_dir = "uploads/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // توليد اسم فريد جديد للصورة لمنع تكرار الأسماء وتداخل الصور
                $new_image_name = time() . "_" . uniqid() . "." . $image_ext;
                $upload_path = $upload_dir . $new_image_name;

                // نقل الملف المرفوع إلى مجلد uploads بنجاح
                if (move_uploaded_file($image_tmp, $upload_path)) {
                    
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // تحضير القيم لتقبل القيم الفارغة (NULL) بأمان تام في الاستعلام الإدخالي
                    $db_major = $major ? "'$major'" : "NULL";
                    $db_level = $level ? "$level" : "NULL";

                    $sql = "INSERT INTO users (full_name, email, password, phone, major, level, image, role) 
                            VALUES ('$full_name', '$email', '$hashed_password', '$phone', $db_major, $db_level, '$new_image_name', 0)";

                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['success'] = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
                        header("Location: login.php");
                        exit();
                    } else {
                        $message = showMessage("حدث خطأ أثناء إنشاء الحساب في قاعدة البيانات: " . mysqli_error($conn), "danger");
                    }
                } else {
                    $message = showMessage("فشل تحميل الصورة الشخصية إلى الخادم، يرجى المحاولة مجدداً.", "danger");
                }
            }
        }
    }
}
?>

<?php include "header.php"; ?>
<div class="container mt-5 mb-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header text-center bg-primary text-white py-3">
                    <h3 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill"></i> إنشاء حساب جديد</h3>
                </div>
                <div class="card-body p-4">
                    <?php echo $message; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" placeholder="أدخل اسمك الكامل" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">رقم الجوال <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="مثال: 059xxxxxxx" maxlength="10" required>
                            <div class="form-text">يجب أن يتكون من 10 خانات رقمية فقط.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">التخصص (اختياري)</label>
                            <select name="major" class="form-select">
                                <option value="">اختر تخصصك...</option>
                                <option value="هندسة البرمجيات">هندسة البرمجيات</option>
                                <option value="تطوير المواقع">تطوير المواقع</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">المستوى الدراسي (اختياري)</label>
                            <select name="level" class="form-select">
                                <option value="">اختر مستواك الدراسي...</option>
                                <option value="1">المستوى الأول (1)</option>
                                <option value="2">المستوى الثاني (2)</option>
                                <option value="3">المستوى الثالث (3)</option>
                                <option value="4">المستوى الرابع (4)</option>
                                <option value="5">المستوى الخامس (5)</option>
                                <option value="6">المستوى السادس (6)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الصورة الشخصية <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control" accept=".jpg, .jpeg, .png" required>
                            <div class="form-text">الصيغ المقبولة: JPG, JPEG, PNG.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="********" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-person-check-fill me-1"></i> إنشاء الحساب
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <p class="mb-0 text-muted">لديك حساب بالفعل؟ <a href="login.php" class="fw-bold text-primary">تسجيل الدخول</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>