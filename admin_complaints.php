<?php
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

$query = "SELECT complaints.*, users.full_name FROM complaints 
          JOIN users ON complaints.user_id = users.id 
          ORDER BY complaints.id DESC";

$result = mysqli_query($conn, $query);
?>

<div class="main-content" style="padding: 20px; direction: rtl;">
    <div class="container-fluid mt-4">
        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-danger text-white p-3">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-chat-square-quote-fill me-2"></i> لوحة التحكم - إدارة الشكاوى والاستفسارات</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="p-3">رقم الشكوى</th>
                                <th class="p-3">اسم الطالب</th>
                                <th class="p-3">القسم (النوع)</th>
                                <th class="p-3">تفاصيل الشكوى</th>
                                <th class="p-3">حالة الشكوى</th>
                                <th class="p-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td class="p-3 fw-bold text-secondary"><?php echo $row['id']; ?></td>
                                <td class="p-3 fw-bold text-dark"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td class="p-3"><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['complaint_type']); ?></span></td>
                                <td class="p-3 text-secondary text-wrap" style="max-width: 300px;"><?php echo htmlspecialchars($row['complaint_details']); ?></td>
                                <td class="p-3">
                                    <?php if ($row['status'] == 'جديدة'): ?>
                                        <span class="badge bg-warning text-dark">جديدة</span>
                                    <?php elseif ($row['status'] == 'قيد المعالجة'): ?>
                                        <span class="badge bg-info text-dark">قيد المعالجة</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">مغلقة</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="reply_complaint.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"><i class="bi bi-reply-fill"></i> رد وحل المشكلة</a>
                                        <a href="delete_complaint.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذه الشكوى؟');"><i class="bi bi-trash"></i> حذف</a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-muted p-4'>لا يوجد أي شكاوى مقدمة حالياً.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>