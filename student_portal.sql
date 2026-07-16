CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- ==========================================
-- 1. جدول المستخدمين المطور (الطلاب والإدارة)
-- ==========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,                           -- إجباري
    email VARCHAR(100) NOT NULL UNIQUE,                        -- إجباري
    password VARCHAR(255) NOT NULL,                            -- إجباري
    phone VARCHAR(10) NOT NULL,                                -- إجباري (رقم الجوال 10 خانات)
    major VARCHAR(50) NULL,                                    -- اختياري (التخصص)
    level INT NULL,                                            -- اختياري (المستوى من 1 إلى 6)
    image VARCHAR(255) NOT NULL DEFAULT 'default-avatar.jpg',  -- إجباري (اسم ملف الصورة الشخصية ولها قيمة افتراضية)
    role TINYINT(1) NOT NULL DEFAULT 0,                        -- 0 للطالب، 1 للأدمن
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. جدول الشكاوى المطور والمدمج مع البوت الذكي
-- ==========================================
CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                                     -- يربط الشكوى بالطالب (users)
    complaint_type VARCHAR(255) NOT NULL,                     -- نوع القسم المختار من أزرار البوت
    complaint_details TEXT NOT NULL,                          -- نص الشكوى المكتوب
    status ENUM('جديدة','قيد المعالجة','مغلقة') DEFAULT 'جديدة', -- حالة الشكوى لمتابعة الإدارة
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- 3. جدول ردود الإدارة على الشكاوى
-- ==========================================
CREATE TABLE complaint_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    admin_id INT NOT NULL,
    reply TEXT NOT NULL,
    reply_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- 4. جدول نظام الإشعارات والتنبيهات
-- ==========================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- 5. جدول المساقات (جديد - الخطوة الثانية)
-- ==========================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,                         -- اسم المساق
    major_type VARCHAR(50) NOT NULL                            -- التخصص التابع له المساق لتصفيتها للطالب
);

-- ==========================================
-- 6. جدول وسيط لتسجيل مساقات الطلاب (علاقة Many-to-Many)
-- ==========================================
CREATE TABLE student_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- ==========================================
-- 7. إدخال مساقات تجريبية للتخصصين (3 مساقات لكل تخصص للاختصار)
-- ==========================================
INSERT INTO courses (course_name, major_type) VALUES 
('هندسة البرمجيات 1', 'هندسة البرمجيات'),
('قواعد البيانات المتقدمة', 'هندسة البرمجيات'),
('تحليل وتصميم النظم', 'هندسة البرمجيات'),
('تصميم واجهات المستخدم UI/UX', 'تطوير المواقع'),
('برمجة الويب المتقدمة PHP', 'تطوير المواقع'),
('تطوير تطبيقات الويب الفرونت إند', 'تطوير المواقع');