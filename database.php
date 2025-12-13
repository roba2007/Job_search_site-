<?php
// اتصال قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // غيرها حسب إعداداتك
define('DB_PASS', ''); // غيرها حسب إعداداتك
define('DB_NAME', 'job_search_db');

// إنشاء الاتصال
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// التحقق من الاتصال
if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
