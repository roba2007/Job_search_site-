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
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $result = loginUser($conn, $username, $password);
    
    if($result === true) {
        $success = "تم تسجيل الدخول بنجاح!";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1500);
              </script>";
    } else {
        $error = $result;
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">تسجيل الدخول</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">اسم المستخدم أو البريد الإلكتروني</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-block">تسجيل الدخول</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <p>ليس لديك حساب؟ <a href="register.php">سجل الآن</a></p>
            <p><a href="#">نسيت كلمة المرور؟</a></p>
        </div>
    </div>
</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
