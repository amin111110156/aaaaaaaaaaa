import './bootstrap';

// استيراد jQuery
window.$ = window.jQuery = require('jquery');

// استيراد Bootstrap
import 'bootstrap';

// استيراد Select2
import 'select2/dist/css/select2.min.css';
import 'select2';

// استيراد Font Awesome
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.min.js';

// تفعيل Select2 تلقائياً
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "اختر...",
        allowClear: true,
        width: '100%'
    });
    
    // تفعيل Select2 للبحث في الأدوية
    $('.js-medication-select').select2({
        placeholder: "ابحث عن الدواء...",
        allowClear: true,
        width: '100%'
    });
    
    // تفعيل Select2 للبحث في المواعيد
    $('.js-appointment-select').select2({
        placeholder: "ابحث عن الموعد...",
        allowClear: true,
        width: '100%'
    });
    
    // تفعيل Select2 للبحث في الأطباء
    $('.js-doctor-select').select2({
        placeholder: "ابحث عن الطبيب...",
        allowClear: true,
        width: '100%'
    });
    
    // تفعيل Select2 للبحث في المرضى
    $('.js-patient-select').select2({
        placeholder: "ابحث عن المريض...",
        allowClear: true,
        width: '100%'
    });
    
    // تفعيل Select2 للبحث في الخدمات
    $('.js-service-select').select2({
        placeholder: "ابحث عن الخدمة...",
        allowClear: true,
        width: '100%'
    });
});

// وظائف مساعدة عامة
window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('تم نسخ النص بنجاح!');
    });
};

window.printPage = function() {
    window.print();
};

window.scrollToTop = function() {
    window.scrollTo({top: 0, behavior: 'smooth'});
};