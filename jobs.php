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

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
?>

<div class="container">
    <div class="jobs-container">
        <h2 class="section-title">الوظائف المتاحة</h2>
        
        <!-- نموذج البحث المتقدم -->
        <div class="search-box" style="margin-bottom: 2rem;">
            <form method="GET" action="" class="search-form" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="ابحث عن وظيفة..." 
                       class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category" class="form-control" style="flex: 1;">
                    <option value="">جميع التصنيفات</option>
                    <?php
                    $categories = getCategories($conn);
                    while($cat = mysqli_fetch_assoc($categories)) {
                        $selected = ($category == $cat['id']) ? 'selected' : '';
                        echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                    }
                    ?>
                </select>
                
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> بحث
                </button>
                
                <a href="jobs.php" class="btn" style="background-color: var(--gray-color);">
                    <i class="fas fa-redo"></i> إعادة تعيين
                </a>
            </form>
        </div>
        
        <!-- عرض الوظائف -->
        <div class="jobs-grid">
            <?php
            $jobs = getJobs($conn, $search, $category);
            
            if(mysqli_num_rows($jobs) > 0):
                while($job = mysqli_fetch_assoc($jobs)):
            ?>
            <div class="job-card">
                <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                <div class="job-company">
                    <i class="fas fa-building"></i>
                    <?php echo htmlspecialchars($job['company_name'] ?: 'شركة خاصة'); ?>
                </div>
                <p class="job-description"><?php echo substr(htmlspecialchars($job['description']), 0, 150) . '...'; ?></p>
                
                <div class="job-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($job['category_name']); ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo date('Y-m-d', strtotime($job['created_at'])); ?></span>
                </div>
                
                <?php if(isset($job['salary']) && !empty($job['salary'])): ?>
                <div class="job-salary" style="margin-bottom: 1rem;">
                    <strong>الراتب:</strong> <?php echo htmlspecialchars($job['salary']); ?>
                </div>
                <?php endif; ?>
                
                <a href="job_details.php?id=<?php echo $job['id']; ?>" class="view-btn">عرض التفاصيل والتقديم</a>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: white; border-radius: 10px;">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--gray-color); margin-bottom: 1rem;"></i>
                <h3>لا توجد وظائف متطابقة مع بحثك</h3>
                <p>حاول تغيير كلمات البحث أو التصنيف</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- زر تحميل المزيد (يمكن تطويره لاستخدام AJAX) -->
        <div style="text-align: center; margin-top: 2rem;">
            <button class="btn" id="loadMore" style="display: none;">
                <i class="fas fa-spinner"></i> تحميل المزيد
            </button>
        </div>
    </div>
</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
