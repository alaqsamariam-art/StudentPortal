<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>بوابة الطالب الذكية</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/favicon.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar navbar-expand-lg shadow-sm">

 <div class="container-fluid px-5">

<a class="navbar-brand d-flex align-items-center" href="index.php">

<img src="images/logo.png" alt="Logo" width="65">

<span class="ms-2">
بوابة الطالب الذكية
</span>

</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">

<span class="navbar-toggler-icon"></span>

</button>

 
<!-- القائمة العلوية بعد تعديل محاذاة العناصر لتناسب اتجاه الـ RTL العربي -->
<div class="collapse navbar-collapse" id="navbar">
    <!-- تم استخدام ms-auto بدلاً من me-auto لتدفع الروابط إلى اليسار تلقائياً في التصميم العربي -->
    <ul class="navbar-nav ms-auto gap-2">
        <li class="nav-item">
            <a class="nav-link active text-white" href="index.php">
                <i class="bi bi-house-door-fill"></i> الرئيسية
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="login.php">
                <i class="bi bi-box-arrow-in-right"></i> تسجيل الدخول
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="register.php">
                <i class="bi bi-person-plus-fill"></i> إنشاء حساب
            </a>
        </li>
    </ul>

    <!-- أزرار تبديل الوضع الليلي التفاعلية (أقصى اليسار تماماً) -->
    <div class="d-flex align-items-center ms-3">
        <!-- زر القمر (يظهر في الوضع الفاتح لتحويله لمظلم) -->
        <button id="darkModeBtn" class="btn btn-link text-white p-2 border-0" title="تفعيل الوضع الليلي">
            <i class="bi bi-moon-stars-fill fs-4 text-warning"></i>
        </button>
        
        <!-- زر الشمس (يظهر في الوضع المظلم لتحويله لفاتح) -->
        <button id="lightModeBtn" class="btn btn-link text-white p-2 border-0 d-none" title="تفعيل الوضع الفاتح">
            <i class="bi bi-sun-fill fs-4 text-warning"></i>
        </button>
    </div>
</div>

</div>

</nav>