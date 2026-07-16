<!-- زر لفتح القائمة الجانبية (يظهر فقط في الشاشات الصغيرة والموبايل) -->
<button class="btn btn-primary d-lg-none m-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
    <i class="bi bi-list"></i> القائمة
</button>

<?php
require_once 'includes/config.php';

$unread_count = 0;
$session_user_image = 'default-avatar.jpg';

if (isset($_SESSION['user_id'])) {
    $current_session_id = intval($_SESSION['user_id']);
    
    $sidebar_user_query = "SELECT image FROM users WHERE id = $current_session_id";
    $sidebar_user_result = mysqli_query($conn, $sidebar_user_query);
    if ($sidebar_user_result && mysqli_num_rows($sidebar_user_result) > 0) {
        $sidebar_user_data = mysqli_fetch_assoc($sidebar_user_result);
        if (!empty($sidebar_user_data['image'])) {
            $session_user_image = $sidebar_user_data['image'];
        }
    }

    if (isset($_SESSION['role']) && $_SESSION['role'] == 0) {
        $count_query = "SELECT COUNT(*) as unread_total FROM notifications WHERE user_id = $current_session_id AND is_read = 0";
        $count_result = mysqli_query($conn, $count_query);
        if ($count_result) {
            $unread_count = mysqli_fetch_assoc($count_result)['unread_total'];
        }
    }
}

$sidebar_image_path = "uploads/" . $session_user_image;
?>

<div class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebarMenu">
    
    <div class="sidebar-profile text-center mb-4">
        <img src="<?php echo htmlspecialchars($sidebar_image_path); ?>" alt="User Avatar" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
        <h5 class="text-white mb-2"><?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'المستخدم'; ?></h5>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
            <span class="badge bg-danger text-white px-3 py-1.5"><i class="bi bi-shield-lock-fill"></i> مسؤول النظام</span>
        <?php } else { ?>
            <span class="badge bg-light text-primary px-3 py-1.5"><i class="bi bi-mortarboard-fill"></i> طالب</span>
        <?php } ?>
    </div>
    
    <hr class="text-white-50">

    <!-- 🟢 أزرار تبديل الوضع الليلي التفاعلية داخل السايدبار -->
    <div class="text-center mb-3">
        <button id="sidebarDarkModeBtn" class="btn btn-outline-light btn-sm w-100 rounded-pill py-2" title="تفعيل الوضع الليلي">
            <i class="bi bi-moon-stars-fill text-warning me-1"></i> الوضع الليلي
        </button>
        <button id="sidebarLightModeBtn" class="btn btn-outline-light btn-sm w-100 rounded-pill py-2 d-none" title="تفعيل الوضع الفاتح">
            <i class="bi bi-sun-fill text-warning me-1"></i> الوضع الفاتح
        </button>
    </div>

    <hr class="text-white-50">
    
    <ul class="sidebar-menu list-unstyled">

        <li class="mb-2">
            <a href="index.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-primary rounded' : ''; ?>">
                <i class="bi bi-speedometer2 me-2"></i> لوحة التحكم
            </a>
        </li>

        <li class="mb-2">
            <a href="profile.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'bg-primary rounded' : ''; ?>">
                <i class="bi bi-person-circle me-2"></i> الملف الشخصي
            </a>
        </li>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0) { ?>
            <li class="mb-2">
                <a href="courses.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'courses.php') ? 'bg-primary rounded' : ''; ?>">
                    <i class="bi bi-book-half me-2"></i> المساقات الدراسية
                </a>
            </li>
            <li class="mb-2">
                <a href="complaints.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'complaints.php') ? 'bg-primary rounded' : ''; ?>">
                    <i class="bi bi-file-earmark-plus-fill me-2"></i> تقديم شكوى
                </a>
            </li>
            <li class="mb-2">
                <a href="complaint_replies.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'complaint_replies.php') ? 'bg-primary rounded' : ''; ?>">
                    <i class="bi bi-list-check me-2"></i> سجل الشكاوي
                </a>
            </li>
            <li class="mb-2">
                <a href="notifications.php" class="text-white text-decoration-none d-flex justify-content-between align-items-center p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'notifications.php') ? 'bg-primary rounded' : ''; ?>">
                    <span>
                        <i class="bi bi-bell-fill me-2"></i> الإشعارات
                    </span>
                    <?php if ($unread_count > 0) { ?>
                        <span class="badge bg-danger rounded-pill pulse-badge" style="font-size: 11px; padding: 4px 8px;">
                            <?php echo $unread_count; ?>
                        </span>
                    <?php } ?>
                </a>
            </li>
        <?php } ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
            <li class="mb-2">
                <a href="admin_users.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_users.php') ? 'bg-primary rounded' : ''; ?>">
                    <i class="bi bi-people-fill me-2"></i> إدارة المستخدمين
                </a>
            </li>
            <li class="mb-2">
                <a href="admin_complaints.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_complaints.php') ? 'bg-primary rounded' : ''; ?>">
                    <i class="bi bi-chat-left-text-fill me-2"></i> إدارة الشكاوى
                </a>
            </li>
            <li class="mb-2">
                <a href="admin_reports.php" class="text-white text-decoration-none d-block p-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_reports.php') ? 'bg-primary rounded' : ''; ?>">
                    <i class="bi bi-bar-chart-fill me-2"></i> التقارير
                </a>
            </li>
        <?php } ?>
        
        <li class="mt-4">
            <a href="logout.php" class="text-danger text-decoration-none d-block p-2">
                <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
            </a>
        </li>

    </ul>
</div>