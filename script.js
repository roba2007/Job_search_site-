
// تفعيل القائمة المتنقلة على الجوال
document.addEventListener('DOMContentLoaded', function() {
    // تبديل القائمة المتنقلة
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if(menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // إغلاق القائمة عند النقر على رابط
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
        });
    });
    
    // تأكيد قبل حذف الوظيفة
    const deleteButtons = document.querySelectorAll('.delete-job');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if(!confirm('هل أنت متأكد من حذف هذه الوظيفة؟')) {
                e.preventDefault();
            }
        });
    });
    
    // فلترة الوظائف
    const searchForm = document.getElementById('searchForm');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = document.getElementById('searchInput');
            if(searchInput.value.trim() === '') {
                e.preventDefault();
                alert('الرجاء إدخال كلمة للبحث');
                searchInput.focus();
            }
        });
    }
    
    // محدد التاريخ
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // تعيين الحد الأدنى للتاريخ إلى اليوم
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
    });
    
    // تحميل المزيد من الوظائف (تظبيط بسيط)
    let currentPage = 1;
    const loadMoreBtn = document.getElementById('loadMore');
    
    if(loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            const category = this.getAttribute('data-category') || '';
            const search = document.getElementById('searchInput')?.value || '';
            
            // إرسال طلب AJAX لتحميل المزيد
            fetch(`includes/load_jobs.php?page=${currentPage}&category=${category}&search=${search}`)
                .then(response => response.text())
                .then(data => {
                    if(data.trim() !== '') {
                        document.querySelector('.jobs-grid').innerHTML += data;
                    } else {
                        loadMoreBtn.style.display = 'none';
                        loadMoreBtn.insertAdjacentHTML('afterend', '<p class="no-more">لا توجد المزيد من الوظائف</p>');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }
});
