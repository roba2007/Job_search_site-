
<?php
// دالة تسجيل مستخدم جديد
function registerUser($conn, $username, $email, $password, $full_name, $phone, $user_type, $company_name = null) {
    // التحقق من عدم وجود المستخدم مسبقاً
    $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        return "اسم المستخدم أو البريد الإلكتروني موجود مسبقاً";
    }
    
    // تشفير كلمة المرور
    $hashed_password = md5($password); // في بيئة حقيقية استخدم password_hash()
    
    // إدخال المستخدم الجديد
    $sql = "INSERT INTO users (username, email, password, full_name, phone, user_type, company_name) 
            VALUES ('$username', '$email', '$hashed_password', '$full_name', '$phone', '$user_type', '$company_name')";
    
    if(mysqli_query($conn, $sql)) {
        return true;
    } else {
        return "خطأ في التسجيل: " . mysqli_error($conn);
    }
}

// دالة تسجيل الدخول
function loginUser($conn, $username, $password) {
    $hashed_password = md5($password); // في بيئة حقيقية استخدم password_verify()
    
    $sql = "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND password = '$hashed_password'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // حفظ بيانات الجلسة
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['company_name'] = $user['company_name'];
        
        return true;
    } else {
        return "اسم المستخدم أو كلمة المرور غير صحيحة";
    }
}

// دالة تسجيل الخروج
function logoutUser() {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// التحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// التحقق من نوع المستخدم
function isEmployer() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'employer';
}

function isJobSeeker() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'job_seeker';
}
?>
