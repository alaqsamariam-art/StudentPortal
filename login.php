<?php
session_start();

require_once "includes/config.php";
require_once "includes/functions.php";

$message = "";

// رسالة نجاح بعد إنشاء الحساب
if (isset($_SESSION['success'])) {
    $message = showMessage($_SESSION['success'], "success");
    unset($_SESSION['success']);
}

if (isset($_POST['login'])) {
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = showMessage("يرجى تعبئة جميع الحقول.", "danger");
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // سيقوم بتخزين 0 أو 1 تلقائياً بناءً على القيمة القادمة من الداتابيز

                header("Location: index.php");
                exit();
            } else {
                $message = showMessage("كلمة المرور غير صحيحة.", "danger");
            }
        } else {
            $message = showMessage("البريد الإلكتروني غير موجود.", "danger");
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
                    <h3 class="mb-0 fw-bold">
                        <i class="bi bi-box-arrow-in-right"></i>
                        تسجيل الدخول
                    </h3>
                </div>
                <div class="card-body p-4">
                    <?php echo $message; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">البريد الإلكتروني</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="example@email.com"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">كلمة المرور</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="********"
                                required>
                        </div>

                        <button
                            type="submit"
                            name="login"
                            class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            تسجيل الدخول
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <p class="mb-0 text-muted">
                            ليس لديك حساب؟
                            <a href="register.php" class="fw-bold text-primary">
                                إنشاء حساب جديد
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>