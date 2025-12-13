<?php
// دالة لجلب الوظائف
function getJobs($conn, $search = '', $category = '') {
    $sql = "SELECT jobs.*, categories.name as category_name 
            FROM jobs 
            LEFT JOIN categories ON jobs.category_id = categories.id 
            WHERE jobs.status = 'active'";
    
    if(!empty($search)) {
        $sql .= " AND (jobs.title LIKE '%$search%' OR jobs.description LIKE '%$search%')";
    }
    
    if(!empty($category)) {
        $sql .= " AND jobs.category_id = '$category'";
    }
    
    $sql .= " ORDER BY jobs.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    return $result;
}

// دالة لجلب تفاصيل وظيفة
function getJobDetails($conn, $job_id) {
    $sql = "SELECT jobs.*, categories.name as category_name, 
            users.company_name, users.phone as company_phone
            FROM jobs 
            LEFT JOIN categories ON jobs.category_id = categories.id
            LEFT JOIN users ON jobs.employer_id = users.id
            WHERE jobs.id = '$job_id'";
    
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// دالة لجلب التصنيفات
function getCategories($conn) {
    $sql = "SELECT * FROM categories ORDER BY name";
    return mysqli_query($conn, $sql);
}
?>
