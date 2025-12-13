<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/auth_functions.php';

// التحقق من تسجيل الدخول وأن المستخدم صاحب عمل
if(!isLoggedIn() || !isEmployer()) {
    header("Location: login.php");
    exit();
}

// التحقق من وجود المعرف والحالة
if(!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: dashboard.php");
    exit();
}

$application_id = mysqli_real_escape_string($conn, $_GET['id']);
$new_status = mysqli_real_escape_string($conn, $_GET['status']);

// الحالات المسموح بها
$allowed_statuses = ['pending', 'reviewed', 'accepted', 'rejected'];
if(!in_array($new_status, $allowed_statuses)) {
    header("Location: dashboard.php");
    exit();
}

// التحقق من أن طلب التوظيف ينتمي لصاحب العمل الحالي
$check_sql = "SELECT ja.*, j.title as job_title, j.employer_id, u.full_name, u.email 
              FROM job_applications ja 
              JOIN jobs j ON ja.job_id = j.id 
              JOIN users u ON ja.user_id = u.id 
              WHERE ja.id = '$application_id' AND j.employer_id = '{$_SESSION['user_id']}'";
$check_result = mysqli_query($conn, $check_sql);

if(mysqli_num_rows($check_result) == 0) {
    // ليس لديك صلاحية لتعديل هذا الطلب
    header("Location: dashboard.php");
    exit();
}

$application = mysqli_fetch_assoc($check_result);

// تحديث حالة طلب التوظيف
$update_sql = "UPDATE job_applications SET status = '$new_status' WHERE id = '$application_id'";
$update_result = mysqli_query($conn, $update_sql);

if($update_result) {
    // جلب معلومات إضافية للإشعارات
    $job_sql = "SELECT title FROM jobs WHERE id = '{$application['job_id']}'";
    $job_result = mysqli_query($conn, $job_sql);
    $job = mysqli_fetch_assoc($job_result);
    
    // حفظ إشعار (يمكن تطويره لإرسال بريد إلكتروني)
    $notification_sql = "INSERT INTO notifications (user_id, title, message, type, is_read) 
                         VALUES ('{$application['user_id']}', 
                                 'تحديث حالة طلب التوظيف', 
                                 'تم تحديث حالة طلبك للوظيفة \"{$job['title']}\" إلى: {$new_status}', 
                                 'application_update', 
                                 0)";
    mysqli_query($conn, $notification_sql);
    
    $success_message = "تم تحديث حالة طلب التوظيف بنجاح!";
} else {
    $error_message = "حدث خطأ أثناء تحديث حالة الطلب: " . mysqli_error($conn);
}
?>

<div class="container">
    <div class="form-container" style="max-width: 600px; margin: 3rem auto; text-align: center;">
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h3>تم التحديث بنجاح!</h3>
                <p><?php echo $success_message; ?></p>
                
                <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem; text-align: right;">
                    <h4>تفاصيل الطلب:</h4>
                    <p><strong>المتقدم:</strong> <?php echo htmlspecialchars($application['full_name']); ?></p>
                    <p><strong>الوظيفة:</strong> <?php echo htmlspecialchars($application['job_title']); ?></p>
                    <p><strong>الحالة الجديدة:</strong> 
                        <?php 
                        $status_text = [
                            'pending' => 'قيد المراجعة',
                            'reviewed' => 'تمت المراجعة',
                            'accepted' => 'مقبول',
                            'rejected' => 'مرفوض'
                        ];
                        echo $status_text[$new_status] ?? $new_status;
                        ?>
                    </p>
                    <p><strong>البريد الإلكتروني للمتقدم:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                </div>
                
                <div style="margin-top: 2rem;">
                    <a href="dashboard.php" class="btn">
                        <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
                    </a>
                    <a href="#" onclick="sendEmailNotification()" class="btn" style="background-color: #27ae60;">
                        <i class="fas fa-envelope"></i> إرسال إشعار بالبريد
                    </a>
                </div>
            </div>
            
        <?php elseif(isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h3>حدث خطأ!</h3>
                <p><?php echo $error_message; ?></p>
                
                <div style="margin-top: 1.5rem;">
                    <a href="dashboard.php" class="btn">
                        <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
                    </a>
                    <a href="javascript:history.back()" class="btn" style="background-color: var(--gray-color); margin-right: 1rem;">
                        <i class="fas fa-redo"></i> المحاولة مرة أخرى
                    </a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- نموذج تأكيد التحديث (يمكن استخدامه لصفحة منفصلة للتأكيد) -->
            <div class="confirmation-form">
                <h3>تأكيد تحديث حالة الطلب</h3>
                <p>هل أنت متأكد من تغيير حالة طلب التوظيف هذا؟</p>
                
                <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; text-align: right;">
                    <h4>تفاصيل الطلب:</h4>
                    <p><strong>المتقدم:</strong> <?php echo htmlspecialchars($application['full_name']); ?></p>
                    <p><strong>الوظيفة:</strong> <?php echo htmlspecialchars($application['job_title']); ?></p>
                    <p><strong>الحالة الحالية:</strong> 
                        <?php 
                        $current_status_text = [
                            'pending' => 'قيد المراجعة',
                            'reviewed' => 'تمت المراجعة',
                            'accepted' => 'مقبول',
                            'rejected' => 'مرفوض'
                        ];
                        echo $current_status_text[$application['status']] ?? $application['status'];
                        ?>
                    </p>
                    <p><strong>الحالة الجديدة:</strong> 
                        <?php 
                        echo $status_text[$new_status] ?? $new_status;
                        ?>
                    </p>
                </div>
                
                <form method="GET" action="">
                    <input type="hidden" name="id" value="<?php echo $application_id; ?>">
                    <input type="hidden" name="status" value="<?php echo $new_status; ?>">
                    
                    <div class="form-group" style="text-align: right;">
                        <label for="notification_message">رسالة إضافية (اختياري)</label>
                        <textarea id="notification_message" name="notification_message" 
                                  class="form-control" rows="3" 
                                  placeholder="يمكنك إضافة رسالة شخصية للمتقدم..."></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                        <button type="submit" name="confirm" value="yes" class="btn" style="background-color: #27ae60;">
                            <i class="fas fa-check"></i> نعم، قم بالتحديث
                        </button>
                        <a href="dashboard.php" class="btn" style="background-color: #e74c3c;">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function sendEmailNotification() {
    // يمكن تطوير هذه الدالة لإرسال بريد إلكتروني فعلي
    alert('سيتم تطوير هذه الميزة قريباً لإرسال بريد إلكتروني للمتقدم');
    
    // مثال باستخدام AJAX:
    /*
    fetch('send_notification_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            application_id: '<?php echo $application_id; ?>',
            new_status: '<?php echo $new_status; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('تم إرسال الإشعار بالبريد الإلكتروني');
        } else {
            alert('حدث خطأ في إرسال البريد');
        }
    });
    */
}
</script>

<?php require_once 'includes/footer.php'; ?>


</body>
</html>