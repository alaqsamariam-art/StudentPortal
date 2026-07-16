<?php
// ==========================================
// 1. إدارة الجلسة والحماية والاتصال
// ==========================================
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

// ==========================================
// 2. جلب الإحصائيات الديناميكية من قاعدة البيانات
// ==========================================

// أ. حساب عدد الطلاب الإجمالي (صلاحية رقم 0)
$students_query = "SELECT COUNT(*) as total FROM users WHERE role = 0";
$students_result = mysqli_query($conn, $students_query);
$total_students = mysqli_fetch_assoc($students_result)['total'];

// ب. حساب عدد الشكاوى الإجمالي
$complaints_query = "SELECT COUNT(*) as total FROM complaints";
$complaints_result = mysqli_query($conn, $complaints_query);
$total_complaints = mysqli_fetch_assoc($complaints_result)['total'];

// ج. حساب الشكاوى الجديدة (قيد الانتظار)
$pending_query = "SELECT COUNT(*) as total FROM complaints WHERE status = 'جديدة' OR status = 'قيد المعالجة'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_complaints = mysqli_fetch_assoc($pending_result)['total'];

// د. حساب الشكاوى المغلقة (تم حلها)
$solved_query = "SELECT COUNT(*) as total FROM complaints WHERE status = 'مغلقة'";
$solved_result = mysqli_query($conn, $solved_query);
$solved_complaints = mysqli_fetch_assoc($solved_result)['total'];

// هـ. إحصائيات التخصصات والمساقات الجديدة
$web_students_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 0 AND major = 'تطوير مواقع'");
$web_students = mysqli_fetch_assoc($web_students_query)['total'];

$software_students_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 0 AND major = 'هندسة برمجيات'");
$software_students = mysqli_fetch_assoc($software_students_query)['total'];

$courses_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM courses");
$total_courses = mysqli_fetch_assoc($courses_count_query)['total'];

$registered_courses_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM student_courses");
$total_registered_entries = mysqli_fetch_assoc($registered_courses_query)['total'];
?>

<div class="main-content" style="padding: 20px; direction: rtl;">
    <div class="container-fluid mt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2"></i> التقارير العامة للنظام الإداري</h2>
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer-fill me-1"></i> طباعة التقرير الشامل</button>
        </div>

        <!-- كروت الإحصائيات السريعة للشكاوى -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-primary text-white p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-white"><?php echo $total_students; ?></h3>
                            <p class="mb-0 small">إجمالي الطلاب</p>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-dark text-white p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-white"><?php echo $total_complaints; ?></h3>
                            <p class="mb-0 small">إجمالي الشكاوى</p>
                        </div>
                        <i class="bi bi-chat-square-text-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-warning text-dark p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-dark"><?php echo $pending_complaints; ?></h3>
                            <p class="mb-0 small">شكاوى قيد الانتظار</p>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-success text-white p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-white"><?php echo $solved_complaints; ?></h3>
                            <p class="mb-0 small">شكاوى تم حلها</p>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- كروت الإحصائيات الأكاديمية والمساقات -->
        <h4 class="fw-bold text-dark mt-5 mb-3"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i> الإحصائيات الأكاديمية ومؤشرات التسجيل</h4>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="card border border-primary bg-white text-dark p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-primary"><?php echo $web_students; ?></h3>
                            <p class="mb-0 text-muted small fw-bold">طلاب تطوير المواقع</p>
                        </div>
                        <i class="bi bi-laptop text-primary fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border border-info bg-white text-dark p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-info"><?php echo $software_students; ?></h3>
                            <p class="mb-0 text-muted small fw-bold">طلاب هندسة البرمجيات</p>
                        </div>
                        <i class="bi bi-code-slash text-info fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border border-secondary bg-white text-dark p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-secondary"><?php echo $total_courses; ?></h3>
                            <p class="mb-0 text-muted small fw-bold">المساقات المتاحة بالنظام</p>
                        </div>
                        <i class="bi bi-book text-secondary fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border border-success bg-white text-dark p-4 shadow-sm rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold text-success"><?php echo $total_registered_entries; ?></h3>
                            <p class="mb-0 text-muted small fw-bold">إجمالي عمليات التسجيل</p>
                        </div>
                        <i class="bi bi-calendar2-check text-success fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول ملخص الشكاوى القديم حسب الأقسام المعتمدة -->
        <div class="card mt-5 shadow-sm border-0 rounded-3">
            <div class="card-header bg-white text-dark py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i> ملخص الشكاوى حسب نوع القسم</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3">اسم القسم</th>
                                <th class="p-3">عدد الشكاوى الكلي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $types_query = "SELECT complaint_type, COUNT(*) as count FROM complaints GROUP BY complaint_type";
                            $types_result = mysqli_query($conn, $types_query);
                            
                            if(mysqli_num_rows($types_result) > 0) {
                                while($row = mysqli_fetch_assoc($types_result)) {
                                    echo "<tr>
                                            <td class='fw-bold text-secondary p-3 text-start' style='padding-right: 30px !important;'><i class='bi bi-folder2-open me-2 text-primary'></i> " . htmlspecialchars($row['complaint_type']) . "</td>
                                            <td class='p-3'><span class='badge bg-light text-dark fs-6 border px-3 py-1.5'>" . $row['count'] . " شكوى</span></td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2' class='text-center py-4 text-muted'>لا توجد شكاوى مسجلة في الأقسام حالياً.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- القسم الجديد: عرض مخطط الـ ER Diagram -->
        <div class="card mt-5 mb-5 shadow-sm border-0 rounded-3 d-print-none">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-diagram-3-fill text-warning me-2"></i> مخطط قاعدة البيانات الهيكلي (ER Diagram)</h5>
            </div>
            <div class="card-body p-3 bg-light text-center">
                <p class="text-muted small mb-3">يوضح هذا المخطط العلاقات والروابط الأساسية بين جداول النظام (المستخدمين، المساقات، الشكاوى، والإشعارات).</p>
                <div class="p-2 border rounded bg-white shadow-inner" style="overflow-x: auto;">
                    <img src="images/ER_Diagram.png" alt="Database ER Diagram" class="img-fluid rounded img-thumbnail" style="max-height: 500px; transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>