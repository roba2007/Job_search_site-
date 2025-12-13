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

// التحقق من أن المستخدم صاحب عمل
if(!isLoggedIn() || !isEmployer()) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $requirements = mysqli_real_escape_string($conn, $_POST['requirements']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $job_type = mysqli_real_escape_string($conn, $_POST['job_type']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $employer_id = $_SESSION['user_id'];
    
    // إدخال الوظيفة في قاعدة البيانات
    $sql = "INSERT INTO jobs (title, description, requirements, location, salary, job_type, category_id, employer_id, deadline) 
            VALUES ('$title', '$description', '$requirements', '$location', '$salary', '$job_type', '$category_id', '$employer_id', '$deadline')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "تم نشر الوظيفة بنجاح!";
        // تفريغ الحقول بعد النشر الناجح
        $_POST = array();
    } else {
        $error = "حدث خطأ أثناء نشر الوظيفة: " . mysqli_error($conn);
    }
}

// جلب التصنيفات
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">نشر وظيفة جديدة</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">عنوان الوظيفة *</label>
                <input type="text" id="title" name="title" class="form-control" required 
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="category_id">التصنيف *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">اختر التصنيف</option>
                    <?php while($category = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $category['id']; ?>" 
                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">وصف الوظيفة *</label>
                <textarea id="description" name="description" class="form-control" rows="6" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="requirements">المتطلبات والمؤهلات</label>
                <textarea id="requirements" name="requirements" class="form-control" rows="4"><?php echo isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements']) : ''; ?></textarea>
                <small>أذكر المتطلبات كل واحدة في سطر</small>
            </div>
            
            <div class="form-group">
                <label for="location">موقع العمل *</label>
                <input type="text" id="location" name="location" class="form-control" required 
                       value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="salary">الراتب</label>
                <input type="text" id="salary" name="salary" class="form-control" 
                       value="<?php echo isset($_POST['salary']) ? htmlspecialchars($_POST['salary']) : ''; ?>"
                       placeholder="مثال: 5000-7000 ريال">
            </div>
            
            <div class="form-group">
                <label for="job_type">نوع الوظيفة *</label>
                <select id="job_type" name="job_type" class="form-control" required>
                    <option value="">اختر نوع الوظيفة</option>
                    <option value="دوام كامل" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] == 'دوام كامل') ? 'selected' : ''; ?>>دوام كامل</option>
                    <option value="دوام جزئي" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] == 'دوام جزئي') ? 'selected' : ''; ?>>دوام جزئي</option>
                    <option value="عن بعد" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] == 'عن بعد') ? 'selected' : ''; ?>>عن بعد</option>
                    <option value="عقد" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] == 'عقد') ? 'selected' : ''; ?>>عقد</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deadline">آخر موعد للتقديم</label>
                <input type="date" id="deadline" name="deadline" class="form-control" 
                       value="<?php echo isset($_POST['deadline']) ? htmlspecialchars($_POST['deadline']) : ''; ?>">
            </div>
            
            <button type="submit" class="btn btn-block">
                <i class="fas fa-paper-plane"></i> نشر الوظيفة
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="dashboard.php" class="btn" style="background-color: var(--gray-color);">
                <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
            </a>
        </div>
    </div>
</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
