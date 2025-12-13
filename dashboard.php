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

// التحقق من تسجيل الدخول
if(!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// جلب بيانات المستخدم
$user_sql = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// جلب الوظائف المقدمة عليها (للباحث عن عمل)
if($user_type == 'job_seeker') {
    $applications_sql = "SELECT ja.*, j.title as job_title, j.location 
                        FROM job_applications ja 
                        JOIN jobs j ON ja.job_id = j.id 
                        WHERE ja.user_id = '$user_id' 
                        ORDER BY ja.applied_at DESC";
    $applications = mysqli_query($conn, $applications_sql);
    
// جلب الوظائف المنشورة (لصاحب العمل)
} else {
    $jobs_sql = "SELECT j.*, COUNT(ja.id) as application_count 
                FROM jobs j 
                LEFT JOIN job_applications ja ON j.id = ja.job_id 
                WHERE j.employer_id = '$user_id' 
                GROUP BY j.id 
                ORDER BY j.created_at DESC";
    $jobs = mysqli_query($conn, $jobs_sql);
    
    // جلب طلبات التوظيف للوظائف المنشورة
    $applications_sql = "SELECT ja.*, j.title as job_title, u.full_name, u.email, u.phone 
                        FROM job_applications ja 
                        JOIN jobs j ON ja.job_id = j.id 
                        JOIN users u ON ja.user_id = u.id 
                        WHERE j.employer_id = '$user_id' 
                        ORDER BY ja.applied_at DESC 
                        LIMIT 10";
    $recent_applications = mysqli_query($conn, $applications_sql);
}
?>

<div class="container">
    <div class="dashboard">
        <!-- القائمة الجانبية -->
        <div class="dashboard-sidebar">
            <div class="user-info" style="text-align: center; margin-bottom: 2rem;">
                <div style="background-color: var(--secondary-color); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="user-type-badge" style="background-color: var(--accent-color); color: white; padding: 0.3rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                    <?php echo ($user_type == 'employer') ? 'صاحب عمل' : 'باحث عن عمل'; ?>
                </span>
            </div>
            
            <ul class="dashboard-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> الرئيسية</a></li>
                
                <?php if($user_type == 'job_seeker'): ?>
                    <li><a href="#applications"><i class="fas fa-file-alt"></i> طلباتي</a></li>
                    <li><a href="#"><i class="fas fa-heart"></i> الوظائف المفضلة</a></li>
                    <li><a href="#"><i class="fas fa-edit"></i> تعديل الملف الشخصي</a></li>
                    <li><a href="#"><i class="fas fa-file-pdf"></i> سيرتي الذاتية</a></li>
                <?php else: ?>
                    <li><a href="post_job.php"><i class="fas fa-plus"></i> نشر وظيفة جديدة</a></li>
                    <li><a href="#jobs"><i class="fas fa-briefcase"></i> وظائفي المنشورة</a></li>
                    <li><a href="#applications"><i class="fas fa-users"></i> طلبات التوظيف</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> الإحصائيات</a></li>
                <?php endif; ?>
                
                <li><a href="includes/logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="dashboard-content">
            <h2 style="margin-bottom: 2rem;">مرحباً <?php echo htmlspecialchars($user['full_name']); ?></h2>
            
            <!-- إحصائيات سريعة -->
            <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <?php if($user_type == 'job_seeker'): ?>
                    <?php
                    $total_applications = mysqli_num_rows($applications);
                    $pending_apps = mysqli_query($conn, "SELECT COUNT(*) as count FROM job_applications WHERE user_id = '$user_id' AND status = 'pending'");
                    $pending_count = mysqli_fetch_assoc($pending_apps)['count'];
                    ?>
                    <div style="background: linear-gradient(to right, var(--secondary-color), #2980b9); color: white; padding: 1.5rem; border-radius: 10px;">
                        <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $total_applications; ?></h3>
                        <p>إجمالي طلبات التقديم</p>
                    </div>
                    <div style="background: linear-gradient(to right, #f39c12, #e67e22); color: white; padding: 1.5rem; border-radius: 10px;">
                        <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $pending_count; ?></h3>
                        <p>قيد المراجعة</p>
                    </div>
                    <div style="background: linear-gradient(to right, #27ae60, #219653); color: white; padding: 1.5rem; border-radius: 10px;">
                        <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">0</h3>
                        <p>مقبولة</p>
                    </div>
                    
                <?php else: ?>
                    <?php
                    $total_jobs = mysqli_num_rows($jobs);
                    $total_apps = mysqli_query($conn, "SELECT COUNT(*) as count FROM job_applications ja JOIN jobs j ON ja.job_id = j.id WHERE j.employer_id = '$user_id'");
                    $total_apps_count = mysqli_fetch_assoc($total_apps)['count'];
                    ?>
                    <div style="background: linear-gradient(to right, var(--secondary-color), #2980b9); color: white; padding: 1.5rem; border-radius: 10px;">
                        <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $total_jobs; ?></h3>
                        <p>الوظائف المنشورة</p>
                    </div>
                    <div style="background: linear-gradient(to right, #9b59b6, #8e44ad); color: white; padding: 1.5rem; border-radius: 10px;">
                        <h3 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $total_apps_count; ?></h3>
                        <p>طلبات التوظيف</p>
                    </div>
                    <div style="background: linear-gradient(to right, #27ae60, #219653); color: white; padding: 1.5rem; border-radius: 10px;">
                        <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">0</h3>
                        <p>وظائف مغلقة</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- قسم حسب نوع المستخدم -->
            <?php if($user_type == 'job_seeker'): ?>
                <!-- طلبات الباحث عن عمل -->
                <section id="applications" style="margin-top: 2rem;">
                    <h3 style="margin-bottom: 1rem;">طلبات التقديم الأخيرة</h3>
                    
                    <?php if(mysqli_num_rows($applications) > 0): ?>
                        <div class="applications-list">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="padding: 1rem; text-align: right;">الوظيفة</th>
                                        <th style="padding: 1rem; text-align: center;">التاريخ</th>
                                        <th style="padding: 1rem; text-align: center;">الحالة</th>
                                        <th style="padding: 1rem; text-align: center;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($app = mysqli_fetch_assoc($applications)): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 1rem;">
                                            <strong><?php echo htmlspecialchars($app['job_title']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($app['location']); ?></small>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <?php echo date('Y-m-d', strtotime($app['applied_at'])); ?>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <?php
                                            $status_color = '';
                                            switch($app['status']) {
                                                case 'pending': $status_color = '#f39c12'; break;
                                                case 'reviewed': $status_color = '#3498db'; break;
                                                case 'accepted': $status_color = '#27ae60'; break;
                                                case 'rejected': $status_color = '#e74c3c'; break;
                                                default: $status_color = '#95a5a6';
                                            }
                                            ?>
                                            <span style="background-color: <?php echo $status_color; ?>; color: white; padding: 0.3rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                                                <?php 
                                                $status_text = [
                                                    'pending' => 'قيد المراجعة',
                                                    'reviewed' => 'تمت المراجعة',
                                                    'accepted' => 'مقبول',
                                                    'rejected' => 'مرفوض'
                                                ];
                                                echo $status_text[$app['status']] ?? $app['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <a href="job_details.php?id=<?php echo $app['job_id']; ?>" class="btn" style="padding: 0.3rem 1rem; font-size: 0.9rem;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; background-color: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-file-alt" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                            <h4>لا توجد طلبات تقديم</h4>
                            <p>ابدأ بالتقديم على الوظائف الآن</p>
                            <a href="jobs.php" class="btn" style="margin-top: 1rem;">تصفح الوظائف</a>
                        </div>
                    <?php endif; ?>
                </section>
                
            <?php else: ?>
                <!-- لوحة صاحب العمل -->
                <div class="employer-dashboard">
                    <!-- الوظائف المنشورة -->
                    <section id="jobs" style="margin-bottom: 3rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3>الوظائف المنشورة</h3>
                            <a href="post_job.php" class="btn">
                                <i class="fas fa-plus"></i> نشر وظيفة جديدة
                            </a>
                        </div>
                        
                        <?php if(mysqli_num_rows($jobs) > 0): ?>
                            <div class="jobs-list">
                                <?php while($job = mysqli_fetch_assoc($jobs)): ?>
                                <div class="job-card" style="margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                        <div>
                                            <h4 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h4>
                                            <div class="job-meta">
                                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                                <span><i class="fas fa-clock"></i> <?php echo date('Y-m-d', strtotime($job['created_at'])); ?></span>
                                                <span><i class="fas fa-users"></i> <?php echo $job['application_count']; ?> طلب</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="job-status" style="background-color: <?php echo ($job['status'] == 'active') ? '#27ae60' : '#e74c3c'; ?>; color: white; padding: 0.3rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                                                <?php echo ($job['status'] == 'active') ? 'نشط' : 'مغلق'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                        <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn" style="background-color: #f39c12; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <a href="delete_job.php?id=<?php echo $job['id']; ?>" class="btn delete-job" style="background-color: #e74c3c; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-trash"></i> حذف
                                        </a>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 3rem; background-color: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-briefcase" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                                <h4>لا توجد وظائف منشورة</h4>
                                <p>ابدأ بنشر أول وظيفة لك الآن</p>
                                <a href="post_job.php" class="btn" style="margin-top: 1rem;">نشر وظيفة جديدة</a>
                            </div>
                        <?php endif; ?>
                    </section>
                    
                    <!-- طلبات التوظيف الأخيرة -->
                    <section id="applications">
                        <h3 style="margin-bottom: 1rem;">طلبات التوظيف الأخيرة</h3>
                        
                        <?php if(mysqli_num_rows($recent_applications) > 0): ?>
                            <div class="applications-list">
                                <?php while($app = mysqli_fetch_assoc($recent_applications)): ?>
                                <div class="application-card" style="background: white; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; border-right: 4px solid var(--secondary-color);">
                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                        <div>
                                            <h4><?php echo htmlspecialchars($app['full_name']); ?></h4>
                                            <p style="color: var(--gray-color); margin-bottom: 0.5rem;">
                                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($app['email']); ?>
                                                <?php if($app['phone']): ?>
                                                <br><i class="fas fa-phone"></i> <?php echo htmlspecialchars($app['phone']); ?>
                                                <?php endif; ?>
                                            </p>
                                            <p><strong>الوظيفة:</strong> <?php echo htmlspecialchars($app['job_title']); ?></p>
                                        </div>
                                        <div>
                                            <span style="background-color: <?php 
                                                $status_color = [
                                                    'pending' => '#f39c12',
                                                    'reviewed' => '#3498db',
                                                    'accepted' => '#27ae60',
                                                    'rejected' => '#e74c3c'
                                                ][$app['status']] ?? '#95a5a6'; 
                                            ?>; color: white; padding: 0.3rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                                                <?php echo ($app['status'] == 'pending') ? 'قيد المراجعة' : $app['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($app['cover_letter'])): ?>
                                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                                        <p><strong>رسالة التغطية:</strong></p>
                                        <p><?php echo substr(htmlspecialchars($app['cover_letter']), 0, 200); ?>...</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                        <a href="#" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                        <?php if($app['status'] == 'pending'): ?>
                                        <a href="update_application.php?id=<?php echo $app['id']; ?>&status=reviewed" class="btn" style="background-color: #3498db; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-check"></i> مراجعة
                                        </a>
                                        <a href="update_application.php?id=<?php echo $app['id']; ?>&status=accepted" class="btn" style="background-color: #27ae60; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-thumbs-up"></i> قبول
                                        </a>
                                        <a href="update_application.php?id=<?php echo $app['id']; ?>&status=rejected" class="btn" style="background-color: #e74c3c; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-times"></i> رفض
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <div style="text-align: center; margin-top: 1rem;">
                                <a href="#" class="btn">عرض جميع الطلبات</a>
                            </div>
                            
                        <?php else: ?>
                            <div style="text-align: center; padding: 3rem; background-color: #f8f9fa; border-radius: 10px;">
                                <i class="fas fa-users" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                                <h4>لا توجد طلبات توظيف</h4>
                                <p>ستظهر هنا طلبات التوظيف للوظائف المنشورة</p>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
