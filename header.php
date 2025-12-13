<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موقع البحث عن وظيفة</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- شريط التنقل -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">وظيفة.كوم</a>
            
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> الرئيسية</a>
                <a href="jobs.php"><i class="fas fa-briefcase"></i> الوظائف</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php"><i class="fas fa-user"></i> لوحة التحكم</a>
                    <a href="post_job.php"><i class="fas fa-plus"></i> إضافة وظيفة</a>
                    <a href="includes/logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل خروج</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> تسجيل دخول</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> تسجيل جديد</a>
                <?php endif; ?>
            </div>
            
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
