<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=
<?php
require_once '/config/database.php';
session_start();

// التحقق من الصلاحيات
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = mysqli_real_escape_string($conn, $data['application_id']);
    $new_status = mysqli_real_escape_string($conn, $data['new_status']);
    
    // جلب معلومات الطلب والمتقدم
    $sql = "SELECT ja.*, u.email, u.full_name, j.title as job_title 
            FROM job_applications ja 
            JOIN users u ON ja.user_id = u.id 
            JOIN jobs j ON ja.job_id = j.id 
            WHERE ja.id = '$application_id' AND j.employer_id = '{$_SESSION['user_id']}'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        $application = mysqli_fetch_assoc($result);
        
        // هنا يمكنك إضافة كود إرسال البريد الإلكتروني الفعلي
        // مثال باستخدام PHPMailer أو mail() الدالة
        
        $status_text = [
            'pending' => 'قيد المراجعة',
            'reviewed' => 'تمت المراجعة',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض'
        ];
        
        $to = $application['email'];
        $subject = "تحديث حالة طلب التوظيف - " . $application['job_title'];
        $message = "
        <html>
        <head>
            <title>تحديث حالة طلب التوظيف</title>
            <style>
                body { font-family: Arial, sans-serif; direction: rtl; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .footer { background-color: #ecf0f1; padding: 15px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>موقع البحث عن وظيفة</h2>
                </div>
                <div class='content'>
                    <h3>مرحباً {$application['full_name']},</h3>
                    <p>نود إعلامك بأن حالة طلبك للوظيفة <strong>{$application['job_title']}</strong> قد تم تحديثها.</p>
                    <p><strong>الحالة الجديدة:</strong> {$status_text[$new_status]}</p>
                    <p>يمكنك تسجيل الدخول إلى حسابك لمشاهدة التفاصيل الكاملة.</p>
                    <br>
                    <p>مع خالص التقدير,</p>
                    <p>فريق موقع البحث عن وظيفة</p>
                </div>
                <div class='footer'>
                    <p>هذا البريد أرسل تلقائياً، يرجى عدم الرد عليه</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@yourdomain.com" . "\r\n";
        
        // تعليق إرسال البريد الفعلي في بيئة التطوير
        // $mail_sent = mail($to, $subject, $message, $headers);
        $mail_sent = true; // مؤقتاً للاختبار
        
        if($mail_sent) {
            echo json_encode(['success' => true, 'message' => 'تم إرسال الإشعار']);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل إرسال البريد']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'طلب غير موجود']);
    }
}
?>
, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>