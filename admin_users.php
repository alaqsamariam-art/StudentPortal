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

// تجهيز متغير البحث
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

// تعديل الاستعلام ليدعم البحث باسم الطالب إذا تم إدخاله
if (!empty($search_query)) {
    $query = "SELECT * FROM users WHERE full_name LIKE '%$search_query%' ORDER BY role DESC, id DESC";
} else {
    $query = "SELECT * FROM users ORDER BY role DESC, id DESC";
}

$result = mysqli_query($conn, $query);
?>

<div class="main-content" style="padding: 20px; direction: rtl;">
    <div class="container-fluid mt-4">
        
        <!-- قسم البحث الجديد -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="admin_users.php" class="d-flex gap-2">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="ابحث عن الطالب بالاسم الكامل..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">بحث</button>
                    <?php if (!empty($search_query)): ?>
                        <a href="admin_users.php" class="btn btn-outline-secondary px-3 shadow-sm d-flex align-items-center">إلغاء</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-primary text-white p-3">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-people-fill me-2"></i> لوحة التحكم - إدارة المستخدمين الشاملة</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="p-3">المعرف</th>
                                <th class="p-3">الصورة</th>
                                <th class="p-3">الاسم الكامل</th>
                                <th class="p-3">البريد الإلكتروني</th>
                                <th class="p-3">رقم الجوال</th>
                                <th class="p-3">التخصص</th>
                                <th class="p-3">المستوى</th>
                                <th class="p-3">الصلاحية</th>
                                <th class="p-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $user_image = (!empty($row['image'])) ? $row['image'] : 'default-avatar.jpg';
                                    $image_path = "uploads/" . $user_image;
                            ?>
                            <tr>
                                <td class="p-3 fw-bold text-secondary"><?php echo $row['id']; ?></td>
                                <td class="p-3">
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                         alt="Avatar" 
                                         class="rounded-circle border" 
                                         style="width: 45px; height: 45px; object-fit: cover;">
                                </td>
                                <td class="p-3 fw-bold text-dark"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td class="p-3 text-muted"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="p-3 fw-semibold"><?php echo (!empty($row['phone'])) ? htmlspecialchars($row['phone']) : '-'; ?></td>
                                <td class="p-3">
                                    <?php if ($row['role'] == 1): ?>
                                        <span class="text-muted small">-</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border px-2 py-1.5">
                                            <?php echo (!empty($row['major'])) ? htmlspecialchars($row['major']) : 'لم يحدد بعد'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <?php if ($row['role'] == 1): ?>
                                        <span class="text-muted small">-</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-primary border px-2 py-1.5">
                                            <?php echo (!empty($row['level'])) ? "مستوى " . htmlspecialchars($row['level']) : 'لم يحدد'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <?php if ($row['role'] == 1): ?>
                                        <span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-shield-lock-fill"></i> مسؤول</span>
                                    <?php else: ?>
                                        <span class="badge bg-success rounded-pill px-2.5 py-1.5"><i class="bi bi-mortarboard-fill"></i> طالب</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white"><i class="bi bi-pencil-square"></i></a>
                                        <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم نهائياً؟');"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-muted p-4'>لا توجد نتائج مطابقة لبحثك.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>