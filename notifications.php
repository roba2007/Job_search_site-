<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=
<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/auth_functions.php';

if(!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// تحديث الإشعارات كمقروءة عند زيارة الصفحة
if(isset($_GET['mark_all_read'])) {
    $update_sql = "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id' AND is_read = 0";
    mysqli_query($conn, $update_sql);
}

// حذف الإشعارات المقروءة
if(isset($_GET['delete_read'])) {
    $delete_sql = "DELETE FROM notifications WHERE user_id = '$user_id' AND is_read = 1";
    mysqli_query($conn, $delete_sql);
}

// جلب الإشعارات
$sql = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container">
    <div class="dashboard">
        <!-- القائمة الجانبية -->
        <div class="dashboard-sidebar">
            <!-- نفس القائمة من dashboard.php -->
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="dashboard-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2>الإشعارات</h2>
                <div>
                    <a href="?mark_all_read" class="btn" style="background-color: #3498db;">
                        <i class="fas fa-check-double"></i> تعليم الكل كمقروء
                    </a>
                    <a href="?delete_read" class="btn" style="background-color: #e74c3c;">
                        <i class="fas fa-trash"></i> حذف المقروء
                    </a>
                </div>
            </div>
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="notifications-list">
                    <?php while($notification = mysqli_fetch_assoc($result)): ?>
                    <div class="notification-item" style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; 
                         border-right: 4px solid <?php echo $notification['is_read'] ? '#95a5a6' : '#3498db'; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h4 style="margin-bottom: 0.5rem; color: <?php echo $notification['is_read'] ? '#7f8c8d' : '#2c3e50'; ?>;">
                                    <?php echo htmlspecialchars($notification['title']); ?>
                                </h4>
                                <p style="color: #555;"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <small style="color: #95a5a6;">
                                    <i class="fas fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?>
                                </small>
                            </div>
                            <div>
                                <?php if(!$notification['is_read']): ?>
                                <span class="unread-badge" style="background-color: #e74c3c; color: white; padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem;">
                                    جديد
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; background-color: #f8f9fa; border-radius: 10px;">
                    <i class="fas fa-bell-slash" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                    <h4>لا توجد إشعارات</h4>
                    <p>ستظهر هنا الإشعارات الخاصة بك</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>