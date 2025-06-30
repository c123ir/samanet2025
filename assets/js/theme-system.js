/**
 * SAMANET THEME SYSTEM - ENTERPRISE GRADE
 * نسخه: 3.0 حرفه‌ای
 * تاریخ: 1404/10/17
 * مطابق: استانداردهای MANDATORY Theme Management
 */

if (!window.samanetThemeSystemLoaded) {
(function() {
    'use strict';
    
    const THEME_STORAGE_KEY = 'samanat_theme';
    const THEME_STATS_KEY = 'samanat_theme_stats';
    const DEFAULT_THEME = 'light';
    
    class ThemeSystem {
        constructor() {
            this.currentTheme = this.getSavedTheme();
            this.stats = this.getThemeStats();
            this.init();
        }
        
        /**
         * راه‌اندازی اولیه سیستم تم
         */
        init() {
            this.applyTheme(this.currentTheme);
            this.updateThemeIcon(this.currentTheme);
            this.bindEvents();
            this.setupSystemListeners();
            
            console.log(`✅ Theme System initialized: ${this.currentTheme}`);
        }
        
        /**
         * دریافت تم ذخیره شده
         */
        getSavedTheme() {
            try {
                return localStorage.getItem(THEME_STORAGE_KEY) || DEFAULT_THEME;
            } catch (error) {
                console.warn('localStorage not available, using default theme');
                return DEFAULT_THEME;
            }
        }
        
        /**
         * ذخیره تم
         */
        saveTheme(theme) {
            try {
                localStorage.setItem(THEME_STORAGE_KEY, theme);
                this.updateThemeStats(theme);
            } catch (error) {
                console.warn('Could not save theme to localStorage');
            }
        }
        
        /**
         * اعمال تم
         */
        applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            this.currentTheme = theme;
            
            // بروزرسانی meta theme-color برای موبایل
            this.updateMetaThemeColor(theme);
            
            // ارسال event سفارشی
            this.dispatchThemeChangeEvent(theme);
        }
        
        /**
         * تغییر تم
         */
        toggleTheme() {
            const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
            
            // انیمیشن تبدیل نرم
            this.animateThemeTransition(() => {
                this.applyTheme(newTheme);
                this.saveTheme(newTheme);
                this.updateThemeIcon(newTheme);
            });
        }
        
        /**
         * بروزرسانی آیکون تم
         */
        updateThemeIcon(theme) {
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                // تغییر آیکون با انیمیشن
                themeIcon.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    // Check if using emoji or FontAwesome
                    const isEmoji = themeIcon.tagName === 'SPAN' && !themeIcon.classList.contains('fas');
                    
                    if (isEmoji) {
                        // Emoji mode
                        if (theme === 'dark') {
                            themeIcon.textContent = '☀️';
                        } else {
                            themeIcon.textContent = '🌙';
                        }
                    } else {
                        // FontAwesome mode
                        if (theme === 'dark') {
                            themeIcon.className = 'fas fa-sun';
                            themeIcon.style.color = '#F59E0B';
                        } else {
                            themeIcon.className = 'fas fa-moon';
                            themeIcon.style.color = '#6B7280';
                        }
                    }
                    
                    themeIcon.style.transform = 'scale(1)';
                }, 150);
            }
        }
        
        /**
         * انیمیشن تبدیل تم
         */
        animateThemeTransition(callback) {
            // اضافه کردن کلاس انیمیشن
            document.body.style.transition = 'all 0.3s ease';
            
            // اجرای callback پس از شروع انیمیشن
            setTimeout(callback, 50);
            
            // حذف transition پس از تکمیل انیمیشن
            setTimeout(() => {
                document.body.style.transition = '';
            }, 350);
        }
        
        /**
         * بروزرسانی meta theme-color
         */
        updateMetaThemeColor(theme) {
            let metaThemeColor = document.querySelector('meta[name="theme-color"]');
            
            if (!metaThemeColor) {
                metaThemeColor = document.createElement('meta');
                metaThemeColor.name = 'theme-color';
                document.head.appendChild(metaThemeColor);
            }
            
            metaThemeColor.content = theme === 'dark' ? '#27272A' : '#FFFFFF';
        }
        
        /**
         * ارسال event تغییر تم
         */
        dispatchThemeChangeEvent(theme) {
            const event = new CustomEvent('themeChanged', {
                detail: { theme, timestamp: Date.now() }
            });
            document.dispatchEvent(event);
        }
        
        /**
         * اتصال event listener ها
         */
        bindEvents() {
            // کلیک روی دکمه تم
            document.addEventListener('click', (e) => {
                if (e.target.closest('.theme-toggle')) {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
            
            // کلید میانبر (Ctrl/Cmd + D)
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
        }
        
        /**
         * شنودهای سیستم
         */
        setupSystemListeners() {
            // شناسایی تغییر ترجیح سیستم
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                
                mediaQuery.addEventListener('change', (e) => {
                    // فقط در صورتی که کاربر تم خاصی انتخاب نکرده
                    if (!localStorage.getItem(THEME_STORAGE_KEY)) {
                        const systemTheme = e.matches ? 'dark' : 'light';
                        this.applyTheme(systemTheme);
                        this.updateThemeIcon(systemTheme);
                    }
                });
            }
            
            // listener برای visibility change
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    // بررسی همگام‌سازی تم هنگام بازگشت به صفحه
                    const savedTheme = this.getSavedTheme();
                    if (savedTheme !== this.currentTheme) {
                        this.applyTheme(savedTheme);
                        this.updateThemeIcon(savedTheme);
                    }
                }
            });
        }
        
        /**
         * دریافت آمار استفاده از تم
         */
        getThemeStats() {
            try {
                const stats = localStorage.getItem(THEME_STATS_KEY);
                return stats ? JSON.parse(stats) : {
                    light: 0,
                    dark: 0,
                    lastChanged: Date.now()
                };
            } catch (error) {
                return { light: 0, dark: 0, lastChanged: Date.now() };
            }
        }
        
        /**
         * بروزرسانی آمار تم
         */
        updateThemeStats(theme) {
            try {
                this.stats[theme] = (this.stats[theme] || 0) + 1;
                this.stats.lastChanged = Date.now();
                
                localStorage.setItem(THEME_STATS_KEY, JSON.stringify(this.stats));
            } catch (error) {
                console.warn('Could not update theme stats');
            }
        }
        
        /**
         * دریافت تم فعلی
         */
        getCurrentTheme() {
            return this.currentTheme;
        }
        
        /**
         * تنظیم تم خاص
         */
        setTheme(theme) {
            if (['light', 'dark'].includes(theme)) {
                this.applyTheme(theme);
                this.saveTheme(theme);
                this.updateThemeIcon(theme);
            }
        }
        
        /**
         * بازنشانی تم به پیش‌فرض سیستم
         */
        resetToSystemTheme() {
            try {
                localStorage.removeItem(THEME_STORAGE_KEY);
                
                const systemTheme = window.matchMedia && 
                    window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                
                this.applyTheme(systemTheme);
                this.updateThemeIcon(systemTheme);
            } catch (error) {
                this.setTheme(DEFAULT_THEME);
            }
        }
    }
    
    // راه‌اندازی سیستم تم
    let themeSystem;
    
    // اطمینان از بارگذاری DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeThemeSystem);
    } else {
        initializeThemeSystem();
    }
    
    function initializeThemeSystem() {
        themeSystem = new ThemeSystem();
        
        // اضافه کردن به window برای دسترسی global
        window.themeSystem = themeSystem;
        
        // تابع global برای سازگاری
        window.toggleTheme = function() {
            themeSystem.toggleTheme();
        };
        
        // تابع global برای تنظیم تم
        window.setTheme = function(theme) {
            themeSystem.setTheme(theme);
        };
        
        // تابع global برای دریافت تم فعلی
        window.getCurrentTheme = function() {
            return themeSystem.getCurrentTheme();
        };
    }
    
    // Export برای استفاده در modules
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = ThemeSystem;
    }
    
})();

/**
 * CSS برای انیمیشن‌های تم
 */
const themeTransitionCSS = `
    .theme-transition {
        transition: background-color 0.3s ease,
                    color 0.3s ease,
                    border-color 0.3s ease,
                    box-shadow 0.3s ease !important;
    }
    
    .theme-toggle {
        transition: all 0.2s ease !important;
    }
    
    .theme-toggle:active {
        transform: scale(0.95) !important;
    }
    
    #theme-icon {
        transition: all 0.15s ease !important;
    }
`;

// اضافه کردن CSS انیمیشن به head
if (document.head && !document.getElementById('theme-transition-styles')) {
    const style = document.createElement('style');
    style.id = 'theme-transition-styles';
    style.textContent = themeTransitionCSS;
    document.head.appendChild(style);
}

window.samanetThemeSystemLoaded = true;
} 