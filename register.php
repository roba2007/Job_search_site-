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

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $company_name = isset($_POST['company_name']) ? mysqli_real_escape_string($conn, $_POST['company_name']) : null;
    
    // التحقق من كلمات المرور
    if($password !== $confirm_password) {
        $error = "كلمتا المرور غير متطابقتين";
    } elseif(strlen($password) < 6) {
        $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    } else {
        $result = registerUser($conn, $username, $email, $password, $full_name, $phone, $user_type, $company_name);
        
        if($result === true) {
            $success = "تم التسجيل بنجاح! يمكنك الآن تسجيل الدخول.";
        } else {
            $error = $result;
        }
    }
}
?>
  

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">إنشاء حساب جديد</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">اسم المستخدم *</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">البريد الإلكتروني *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور *</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small>يجب أن تكون 6 أحرف على الأقل</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">تأكيد كلمة المرور *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">الاسم الكامل *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="phone">رقم الهاتف</label>
                <input type="tel" id="phone" name="phone" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="user_type">نوع الحساب *</label>
                <select id="user_type" name="user_type" class="form-control" required onchange="toggleCompanyField()">
                    <option value="">اختر نوع الحساب</option>
                    <option value="job_seeker">باحث عن عمل</option>
                    <option value="employer">صاحب عمل</option>
                </select>
            </div>
            
            <div class="form-group" id="companyField" style="display: none;">
                <label for="company_name">اسم الشركة</label>
                <input type="text" id="company_name" name="company_name" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-block">تسجيل الحساب</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <p>لديك حساب بالفعل؟ <a href="login.php">سجل الدخول</a></p>
        </div>
    </div>
</div>
</body>
</html>
<script>
function toggleCompanyField() {
    var userType = document.getElementById('user_type').value;
    var companyField = document.getElementById('companyField');
    
    if(userType === 'employer') {
        companyField.style.display = 'block';
    } else {
        companyField.style.display = 'none';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

