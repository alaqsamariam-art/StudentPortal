<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

$user_id = $_SESSION['user_id'];
$message = "";

// جلب تخصص المستخدم وتأمينه
$user_query = mysqli_query($conn, "SELECT major FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$user_major = $user_data['major'];

if (isset($_POST['register_course'])) {
    $course_id = intval($_POST['course_id']);

    $check_duplicate = mysqli_query($conn, "SELECT id FROM student_courses WHERE student_id = '$user_id' AND course_id = '$course_id'");

    if (mysqli_num_rows($check_duplicate) > 0) {
        $message = '<div class="alert alert-warning text-center border-0 shadow-sm mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i>أنت مسجلة في هذا المساق بالفعل.</div>';
    } else {
        $insert_query = "INSERT INTO student_courses (student_id, course_id) VALUES ('$user_id', '$course_id')";
        if (mysqli_query($conn, $insert_query)) {
            $message = '<div class="alert alert-success text-center border-0 shadow-sm mb-4"><i class="bi bi-check-circle-fill me-2"></i>تم تسجيل المساق بنجاح وتنزيله بجدولك الدراسي.</div>';
        } else {
            $message = '<div class="alert alert-danger text-center border-0 shadow-sm mb-4"><i class="bi bi-x-circle-fill me-2"></i>حدث خطأ غير متوقع أثناء تسجيل المساق.</div>';
        }
    }
}

require_once "header.php";
require_once "sidebar.php";
?>

<div class="main-content" style="padding: 20px; min-height: 85vh; direction: rtl;">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                    <i class="bi bi-book-half text-primary fs-2 me-3"></i>
                    <div class="ms-3">
                        <h3 class="fw-bold text-dark mb-0">بوابة تسجيل المساقات الدراسية</h3>
                        <p class="text-muted small mb-0">هنا يمكنكِ الاطلاع على المواد المتاحة لتخصصكِ وتسجيلها في خطة الفصل الحالي بكل سهولة.</p>
                    </div>
                </div>

                <?php echo $message; ?>

                <?php if (empty($user_major)) { ?>
                    <div class="alert alert-warning border-0 shadow-sm p-4" role="alert">
                        <h4 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> تنبيه هام جداً!</h4>
                        <p class="mb-2">يرجى التوجه الفوري إلى صفحة <a href="profile.php" class="fw-bold text-dark text-decoration-underline">الملف الشخصي</a> أولاً وتحديد تخصصك الدراسي (هندسة برمجيات أو تطوير مواقع).</p>
                        <hr>
                        <p class="mb-0 small">بمجرد تعيين تخصصكِ، سيقوم النظام تلقائياً بفتح البوابة لتسجيل المساقات المتطابقة مع خطتكِ الأكاديمية.</p>
                    </div>
                <?php } else { ?>

                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-primary text-white p-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-grid-3x3-gap me-2"></i> المساقات المتاحة لتخصص (<?php echo htmlspecialchars($user_major); ?>)</h5>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <div class="row g-4">
                                <?php
                                $courses_query = mysqli_query($conn, "SELECT * FROM courses WHERE major_type = '$user_major'");
                                if (mysqli_num_rows($courses_query) == 0) {
                                    echo "<div class='col-12 text-center text-muted my-4'><i class='bi bi-folder-x fs-1 d-block mb-2'></i>لا توجد مساقات مضافة حالياً لهذا التخصص في قاعدة البيانات.</div>";
                                } else {
                                    while ($course = mysqli_fetch_assoc($courses_query)) {
                                        $c_id = $course['id'];
                                        $check_reg = mysqli_query($conn, "SELECT id FROM student_courses WHERE student_id = '$user_id' AND course_id = '$c_id'");
                                        $is_registered = (mysqli_num_rows($check_reg) > 0);
                                ?>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card h-100 border rounded-3 text-center p-3 shadow-sm hover-shadow-md transition">
                                                <div class="my-3 text-primary">
                                                    <i class="bi bi-journal-bookmark-fill" style="font-size: 45px;"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($course['course_name']); ?></h6>
                                                <p class="text-muted small mb-3"><?php echo htmlspecialchars($course['major_type']); ?></p>

                                                <form method="POST">
                                                    <input type="hidden" name="course_id" value="<?php echo $c_id; ?>">
                                                    <?php if ($is_registered) { ?>
                                                        <button type="button" class="btn btn-secondary w-100 disabled py-2" disabled>
                                                            <i class="bi bi-check-all"></i> مسجل مسبقاً
                                                        </button>
                                                    <?php } else { ?>
                                                        <button type="submit" name="register_course" class="btn btn-outline-primary w-100 py-2">
                                                            <i class="bi bi-plus-circle me-1"></i> تسجيل المساق
                                                        </button>
                                                    <?php } ?>
                                                </form>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-dark text-white p-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-calendar-check me-2"></i> جدول مساقاتي المسجلة حالياً</h5>
                        </div>
                        <div class="card-body p-0 bg-white">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="p-3" style="width: 10%;">#</th>
                                            <th class="p-3" style="width: 50%;">اسم المساق الدراسي</th>
                                            <th class="p-3" style="width: 20%;">التخصص</th>
                                            <th class="p-3" style="width: 20%;">تاريخ ووقت التسجيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $my_courses_query = mysqli_query($conn, "
                                            SELECT sc.registered_at, c.course_name, c.major_type 
                                            FROM student_courses sc 
                                            JOIN courses c ON sc.course_id = c.id 
                                            WHERE sc.student_id = '$user_id'
                                            ORDER BY sc.registered_at DESC
                                        ");

                                        if (mysqli_num_rows($my_courses_query) == 0) {
                                        ?>
                                            <tr>
                                                <td colspan="4" class="text-center p-4 text-muted">لم تقومي بتسجيل أي مساقات دراسية حتى الآن في هذا الفصل الدراسي.</td>
                                            </tr>
                                            <?php
                                        } else {
                                            $counter = 1;
                                            while ($my_course = mysqli_fetch_assoc($my_courses_query)) {
                                            ?>
                                                <tr>
                                                    <td class="p-3 fw-bold text-secondary"><?php echo $counter++; ?></td>
                                                    <td class="p-3 fw-bold text-dark"><?php echo htmlspecialchars($my_course['course_name']); ?></td>
                                                    <td class="p-3"><span class="badge bg-light text-dark border px-3 py-1.5"><?php echo htmlspecialchars($my_course['major_type']); ?></span></td>
                                                    <td class="p-3 text-muted small"><?php echo date('Y-m-d H:i', strtotime($my_course['registered_at'])); ?></td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>