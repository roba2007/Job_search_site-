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
require_once 'includes/functions.php';

if(!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit();
}

$job_id = mysqli_real_escape_string($conn, $_GET['id']);
$job = getJobDetails($conn, $job_id);

if(!$job) {
    echo "<div class='container'><div class='alert alert-error'>الوظيفة غير موجودة</div></div>";
    require_once 'includes/footer.php';
    exit();
}

$application_success = '';
$application_error = '';

// معالجة طلب التقديم
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cover_letter = mysqli_real_escape_string($conn, $_POST['cover_letter']);
    
    // رفع السيرة الذاتية
    $resume_path = '';
    if(isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if(in_array($_FILES['resume']['type'], $allowed_types) && $_FILES['resume']['size'] <= $max_size) {
            $upload_dir = 'uploads/resumes/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $file_name = 'resume_' . $user_id . '_' . time() . '.' . $file_extension;
            $resume_path = $upload_dir . $file_name;
            
            if(move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
                $resume_path = 'uploads/resumes/' . $file_name;
            }
        }
    }
    
    // حفظ طلب التقديم
    $sql = "INSERT INTO job_applications (job_id, user_id, cover_letter, resume_path) 
            VALUES ('$job_id', '$user_id', '$cover_letter', '$resume_path')";
    
    if(mysqli_query($conn, $sql)) {
        $application_success = "تم تقديم طلبك بنجاح!";
    } else {
        $application_error = "حدث خطأ أثناء التقديم: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <div class="job-details">
        <!-- زر العودة -->
        <a href="jobs.php" style="display: inline-block; margin-bottom: 1rem; color: var(--secondary-color);">
            <i class="fas fa-arrow-right"></i> العودة للوظائف
        </a>
        
        <!-- تفاصيل الوظيفة -->
        <div class="job-header">
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                <div>
                    <p style="color: var(--gray-color);">
                        <i class="fas fa-building"></i> 
                        <?php echo htmlspecialchars($job['company_name'] ?: 'شركة خاصة'); ?>
                    </p>
                    <p style="color: var(--gray-color);">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?php echo htmlspecialchars($job['location']); ?>
                    </p>
                </div>
                
                <div>
                    <span class="job-type" style="background-color: var(--secondary-color); color: white; padding: 0.5rem 1rem; border-radius: 5px;">
                        <?php echo htmlspecialchars($job['job_type']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- معلومات الوظيفة -->
        <div class="job-info">
            <h3 style="margin-bottom: 1rem; color: var(--primary-color);">وصف الوظيفة</h3>
            <p style="margin-bottom: 2rem;"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
            
            <?php if(!empty($job['requirements'])): ?>
            <h3 style="margin-bottom: 1rem; color: var(--primary-color);">المتطلبات</h3>
            <div style="margin-bottom: 2rem;"><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></div>
            <?php endif; ?>
            
            <div class="job-meta-details" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <?php if(!empty($job['salary'])): ?>
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                    <strong><i class="fas fa-money-bill-wave"></i> الراتب:</strong>
                    <p><?php echo htmlspecialchars($job['salary']); ?></p>
                </div>
                <?php endif; ?>
                
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                    <strong><i class="fas fa-tag"></i> التصنيف:</strong>
                    <p><?php echo htmlspecialchars($job['category_name']); ?></p>
                </div>
                
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                    <strong><i class="fas fa-calendar-alt"></i> تاريخ النشر:</strong>
                    <p><?php echo date('Y-m-d', strtotime($job['created_at'])); ?></p>
                </div>
                
                <?php if(!empty($job['deadline'])): ?>
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                    <strong><i class="fas fa-clock"></i> آخر موعد للتقديم:</strong>
                    <p><?php echo htmlspecialchars($job['deadline']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- معلومات جهة العمل -->
        <?php if(!empty($job['company_name'])): ?>
        <div class="company-info" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #eee;">
            <h3 style="margin-bottom: 1rem; color: var(--primary-color);">معلومات جهة العمل</h3>
            <p><strong>اسم الشركة:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
            <?php if(!empty($job['company_phone'])): ?>
            <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($job['company_phone']); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- نموذج التقديم -->
        <div class="application-form" style="margin-top: 3rem;">
            <h3 style="margin-bottom: 1rem; color: var(--primary-color);">التقديم على الوظيفة</h3>
            
            <?php if($application_success): ?>
                <div class="alert alert-success"><?php echo $application_success; ?></div>
            <?php endif; ?>
            
            <?php if($application_error): ?>
                <div class="alert alert-error"><?php echo $application_error; ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['user_type'] == 'job_seeker'): ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="cover_letter">رسالة التغطية</label>
                            <textarea id="cover_letter" name="cover_letter" class="form-control" rows="5" 
                                      placeholder="أكتب رسالتك هنا..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="resume">رفع السيرة الذاتية (PDF أو Word)</label>
                            <input type="file" id="resume" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                            <small>الحد الأقصى للحجم: 5MB</small>
                        </div>
                        
                        <button type="submit" class="apply-btn btn-block">تقديم الطلب</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-error">
                        <p>يجب أن تكون باحثاً عن عمل لتتمكن من التقديم على الوظائف</p>
                        <a href="index.php" class="btn" style="margin-top: 1rem;">العودة للرئيسية</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-error">
                    <p>يجب تسجيل الدخول لتتمكن من التقديم على الوظائف</p>
                    <div style="margin-top: 1rem;">
                        <a href="login.php" class="btn">تسجيل الدخول</a>
                        <a href="register.php" class="btn" style="background-color: var(--gray-color); margin-right: 1rem;">إنشاء حساب</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
