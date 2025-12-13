
<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/functions.php';
?>

<!-- قسم البطل -->
<section class="hero">
    <div class="container">
        <h1>ابحث عن وظيفة أحلامك</h1>
        <p>آلاف الوظائف في مختلف المجالات تنتظرك. ابدأ البحث الآن!</p>
        
        <form action="jobs.php" method="GET" class="search-box">
            <input type="text" name="search" placeholder="ابحث عن وظيفة، شركة، أو مهارة..." class="search-input">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> بحث
            </button>
        </form>
    </div>
</section>

<!-- أحدث الوظائف -->
<section class="jobs-container">
    <div class="container">
        <h2 class="section-title">أحدث الوظائف</h2>
        
        <div class="jobs-grid">
            <?php
            $jobs = getJobs($conn, '', '', 6);
            if(mysqli_num_rows($jobs) > 0):
                while($job = mysqli_fetch_assoc($jobs)):
            ?>
            <div class="job-card">
                <h3 class="job-title"><?php echo $job['title']; ?></h3>
                <div class="job-company">
                    <i class="fas fa-building"></i>
                    <?php  'شركة خاصة'; ?>
                </div>
                <p class="job-description"><?php echo substr($job['description'], 0, 100) . '...'; ?></p>
                <div class="job-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo $job['location']; ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo date('Y-m-d', strtotime($job['created_at'])); ?></span>
                </div>
                <a href="job_details.php?id=<?php echo $job['id']; ?>" class="view-btn">عرض التفاصيل</a>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <p>لا توجد وظائف متاحة حالياً.</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center;">
            <a href="jobs.php" class="btn">عرض جميع الوظائف</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
