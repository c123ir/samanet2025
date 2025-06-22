/**
 * Debug Helper برای سامانت
 * نمایش اطلاعات مفصل در Console
 */

// اضافه کردن styles به console logs
const consoleStyles = {
    success: 'color: #28a745; font-weight: bold;',
    error: 'color: #dc3545; font-weight: bold;',
    warning: 'color: #ffc107; font-weight: bold;',
    info: 'color: #17a2b8; font-weight: bold;',
    debug: 'color: #6c757d; font-style: italic;'
};

// تابع logging بهتر
window.SamanetDebug = {
    log: function(message, type = 'info', data = null) {
        const timestamp = new Date().toLocaleTimeString('fa-IR');
        const style = consoleStyles[type] || consoleStyles.info;
        
        console.groupCollapsed(`%c[${timestamp}] ${message}`, style);
        if (data) {
            console.table(data);
        }
        console.trace();
        console.groupEnd();
    },
    
    success: function(message, data = null) {
        this.log(`✅ ${message}`, 'success', data);
    },
    
    error: function(message, data = null) {
        this.log(`❌ ${message}`, 'error', data);
    },
    
    warning: function(message, data = null) {
        this.log(`⚠️ ${message}`, 'warning', data);
    },
    
    info: function(message, data = null) {
        this.log(`ℹ️ ${message}`, 'info', data);
    }
};

// نمایش اطلاعات صفحه فعلی
window.SamanetDebug.info('Debug Helper Loaded', {
    'URL': window.location.href,
    'User Agent': navigator.userAgent,
    'Screen': `${screen.width}x${screen.height}`,
    'Viewport': `${window.innerWidth}x${window.innerHeight}`
});

// گوش دادن به خطاهای JavaScript
window.addEventListener('error', function(e) {
    window.SamanetDebug.error('JavaScript Error', {
        'Message': e.message,
        'File': e.filename,
        'Line': e.lineno,
        'Column': e.colno,
        'Stack': e.error ? e.error.stack : 'No stack trace'
    });
});

// گوش دادن به Promise rejections
window.addEventListener('unhandledrejection', function(e) {
    window.SamanetDebug.error('Unhandled Promise Rejection', {
        'Reason': e.reason,
        'Promise': e.promise
    });
});

console.log('%c🔧 سامانت Debug Helper آماده است!', 'color: #667eea; font-size: 16px; font-weight: bold;'); 