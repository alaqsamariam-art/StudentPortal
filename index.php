<?php
session_start();

require_once "includes/config.php";
require_once "includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$course_count = 0;
if ($_SESSION['role'] == 0) {
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total_courses FROM student_courses WHERE student_id = '$user_id'");
    if ($count_query) {
        $course_count = mysqli_fetch_assoc($count_query)['total_courses'];
    }
}
?>

<?php include "header.php"; ?>

<!-- تم استدعاء كلاس no-sidebar هنا ليعمل التنسيق السحري ويتوسط المحتوى تماماً -->
<div class="main-content no-sidebar" style="padding: 20px; direction: rtl;">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h1 class="page-title mb-1">مرحباً بكِ، <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
                <p class="lead text-muted">أهلاً بكِ في بوابة الطالب الذكية</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if ($_SESSION['role'] == 0) { ?>
            
            <div class="col-md-5">
                <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                    <i class="bi bi-book-half text-primary fs-1 mb-2 d-block"></i>
                    <h3>المساقات الدراسية</h3>
                    <p class="text-muted">يمكنك تسجيل مساقاتك المتاحة ومتابعة خطتك الدراسية الحالية.</p>
                    <div class="badge bg-primary px-3 py-2 mb-3 d-inline-block">المساقات المسجلة: <?php echo $course_count; ?></div>
                    <br>
                    <a href="courses.php" class="btn btn-primary w-50">دخول والتسجيل</a>
                </div>
            </div>

            <div class="col-md-5">
                <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                    <i class="bi bi-file-earmark-plus-fill text-success fs-1 mb-2 d-block"></i>
                    <h3>تقديم شكوى</h3>
                    <p class="text-muted">يمكنك تقديم شكوى جديدة ومتابعة حالتها عبر البوت الذكي المطور.</p>
                    <div class="mb-4"></div>
                    <a href="complaints.php" class="btn btn-success w-50">دخول</a>
                </div>
            </div>

            <div class="col-md-5">
                <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                    <i class="bi bi-list-check text-warning fs-1 mb-2 d-block"></i>
                    <h3>سجل الشكاوي</h3>
                    <p class="text-muted">عرض جميع الشكاوى التي قمت بتقديمها ومتابعة ردود الإدارة عليها.</p>
                    <a href="complaint_replies.php" class="btn btn-warning text-white w-50">عرض السجل</a>
                </div>
            </div>

            <div class="col-md-5">
                <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                    <i class="bi bi-bell-fill text-info fs-1 mb-2 d-block"></i>
                    <h3>الإشعارات</h3>
                    <p class="text-muted">متابعة الردود والتحديثات الفورية والرسائل الخاصة بك داخل النظام.</p>
                    <a href="notifications.php" class="btn btn-info text-white w-50">عرض الإشعارات</a>
                </div>
            </div>

            <?php } else if ($_SESSION['role'] == 1) { ?>
               
               <div class="col-md-4">
                   <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                       <i class="bi bi-people-fill text-primary fs-1 mb-2 d-block"></i>
                       <h3>إدارة المستخدمين</h3>
                       <p class="text-muted">عرض وإدارة حسابات الطلاب وتحديث الصلاحيات المعتمدة.</p>
                       <a href="admin_users.php" class="btn btn-primary w-100">دخول</a>
                   </div>
               </div>

               <div class="col-md-4">
                   <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                       <i class="bi bi-chat-left-text-fill text-danger fs-1 mb-2 d-block"></i>
                       <h3>إدارة الشكاوى</h3>
                       <p class="text-muted">متابعة الشكاوى المرفوعة من الطلاب والرد المباشر عليها.</p>
                       <a href="admin_complaints.php" class="btn btn-danger w-100">دخول</a>
                   </div>
               </div>

               <div class="col-md-4">
                   <div class="dashboard-card text-center p-4 border-0 rounded-3 shadow-sm bg-white h-100">
                       <i class="bi bi-bar-chart-fill text-dark fs-1 mb-2 d-block"></i>
                       <h3>التقارير</h3>
                       <p class="text-muted">عرض إحصائيات النظام العامة والتقارير البيانية للشكاوى والـ ERD.</p>
                       <a href="admin_reports.php" class="btn btn-dark w-100">دخول</a>
                   </div>
               </div>
            
            <?php } ?>
        </div>

        <?php if ($_SESSION['role'] == 0) { ?>
        <div class="row mt-5 justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-light p-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary"></i> آخر المساقات التي قمتِ بتسجيلها مؤخراً</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th class="p-3">اسم المساق</th>
                                        <th class="p-3">التخصص</th>
                                        <th class="p-3">وقت التسجيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_query = mysqli_query($conn, "
                                        SELECT c.course_name, c.major_type, sc.registered_at 
                                        FROM student_courses sc 
                                        JOIN courses c ON sc.course_id = c.id 
                                        WHERE sc.student_id = '$user_id' 
                                        ORDER BY sc.registered_at DESC 
                                        LIMIT 3
                                    ");
                                    if (mysqli_num_rows($recent_query) == 0) {
                                        echo "<tr><td colspan='3' class='text-center p-4 text-muted'>لا توجد مساقات مسجلة حالياً.</td></tr>";
                                    } else {
                                        while ($row = mysqli_fetch_assoc($recent_query)) {
                                            ?>
                                            <tr>
                                                <td class="p-3 fw-bold text-dark"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                                <td class="p-3"><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['major_type']); ?></span></td>
                                                <td class="p-3 text-muted small"><?php echo date('Y-m-d H:i', strtotime($row['registered_at'])); ?></td>
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
            </div>
        </div>
        <?php } ?>

        <div class="text-center mt-5 mb-4">
            <a href="logout.php" class="btn btn-outline-danger px-4 rounded-pill">
                <i class="bi bi-box-arrow-right"></i> تسجيل الخروج من النظام
            </a>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>