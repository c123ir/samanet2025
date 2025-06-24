/*
 * نام فایل: theme-system.js
 * مسیر فایل: /assets/js/theme-system.js
 * توضیح: سیستم جامع تم (شب/روز) سامانت
 * تاریخ ایجاد: 1404/03/31
 */

(function() {
    'use strict';

    /**
     * کلاس مدیریت تم
     */
    class ThemeManager {
        constructor() {
            this.currentTheme = 'light';
            this.themes = {
                light: {
                    name: 'روز',
                    icon: 'fas fa-moon'
                },
                dark: {
                    name: 'شب', 
                    icon: 'fas fa-sun'
                }
            };
            
            this.init();
        }

        /**
         * راه‌اندازی اولیه
         */
        init() {
            this.loadTheme();
            this.setupToggleButton();
            this.setupSystemThemeDetection();
            this.setupKeyboardShortcut();
            
            console.log('🎨 Theme System initialized');
        }

        /**
         * بارگذاری تم ذخیره شده
         */
        loadTheme() {
            const savedTheme = localStorage.getItem('samanat_theme');
            const systemTheme = this.getSystemTheme();
            
            // اگر تمی ذخیره نشده، از تم سیستم استفاده کن
            const themeToApply = savedTheme || systemTheme;
            
            this.setTheme(themeToApply, false);
            
            console.log(`📱 Theme loaded: ${themeToApply}`);
        }

        /**
         * دریافت تم سیستم
         */
        getSystemTheme() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            return 'light';
        }

        /**
         * تنظیم تم
         */
        setTheme(theme, saveToStorage = true) {
            if (!this.themes[theme]) {
                console.warn(`❌ Invalid theme: ${theme}`);
                return;
            }

            this.currentTheme = theme;
            
            // اعمال تم به HTML element
            document.documentElement.setAttribute('data-theme', theme);
            
            // به‌روزرسانی آیکون
            this.updateThemeIcon();
            
            // ذخیره در localStorage
            if (saveToStorage) {
                localStorage.setItem('samanat_theme', theme);
            }
            
            // انیمیشن transition
            this.addThemeTransition();
            
            // ارسال event
            document.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme: theme, previousTheme: this.currentTheme }
            }));
            
            console.log(`🎨 Theme changed to: ${theme}`);
        }

        /**
         * تغییر تم
         */
        toggleTheme() {
            const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
            
            // انیمیشن چرخش برای button
            this.animateToggleButton();
            
            // تغییر تم با تاخیر برای انیمیشن
            setTimeout(() => {
                this.setTheme(newTheme);
            }, 150);
        }

        /**
         * به‌روزرسانی آیکون تم
         */
        updateThemeIcon() {
            const themeIcon = document.getElementById('theme-icon');
            const themeButtons = document.querySelectorAll('.theme-toggle');
            
            if (themeIcon) {
                themeIcon.className = this.themes[this.currentTheme].icon;
            }
            
            // به‌روزرسانی تمام دکمه‌های theme toggle
            themeButtons.forEach(button => {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.className = this.themes[this.currentTheme].icon;
                }
                
                const title = `تغییر به حالت ${this.currentTheme === 'light' ? 'شب' : 'روز'}`;
                button.setAttribute('title', title);
                button.setAttribute('aria-label', title);
            });
        }

        /**
         * انیمیشن دکمه toggle
         */
        animateToggleButton() {
            const themeButtons = document.querySelectorAll('.theme-toggle');
            
            themeButtons.forEach(button => {
                button.classList.add('rotating');
                
                setTimeout(() => {
                    button.classList.remove('rotating');
                }, 600);
            });
        }

        /**
         * تنظیم دکمه toggle
         */
        setupToggleButton() {
            // حذف event listener های قبلی
            const existingButtons = document.querySelectorAll('.theme-toggle');
            existingButtons.forEach(button => {
                button.removeEventListener('click', this.handleToggleClick);
            });
            
            // اضافه کردن event listener جدید
            document.addEventListener('click', (e) => {
                if (e.target.closest('.theme-toggle')) {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
            
            // تنظیم آیکون اولیه
            this.updateThemeIcon();
        }

        /**
         * تشخیص تغییرات تم سیستم
         */
        setupSystemThemeDetection() {
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                
                mediaQuery.addEventListener('change', (e) => {
                    // فقط اگر کاربر تم خاصی انتخاب نکرده باشد
                    const savedTheme = localStorage.getItem('samanat_theme');
                    if (!savedTheme) {
                        this.setTheme(e.matches ? 'dark' : 'light', false);
                    }
                });
            }
        }

        /**
         * میانبر کیبورد برای تغییر تم
         */
        setupKeyboardShortcut() {
            document.addEventListener('keydown', (e) => {
                // Ctrl + Shift + T برای تغییر تم
                if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 't') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
        }

        /**
         * اضافه کردن انیمیشن transition
         */
        addThemeTransition() {
            document.documentElement.style.transition = 'all 0.3s ease';
            
            // حذف transition پس از اتمام انیمیشن
            setTimeout(() => {
                document.documentElement.style.transition = '';
            }, 300);
        }

        /**
         * دریافت تم فعلی
         */
        getCurrentTheme() {
            return this.currentTheme;
        }

        /**
         * بررسی حالت dark mode
         */
        isDarkMode() {
            return this.currentTheme === 'dark';
        }

        /**
         * ری‌ست کردن تم به حالت سیستم
         */
        resetToSystemTheme() {
            localStorage.removeItem('samanat_theme');
            this.setTheme(this.getSystemTheme(), false);
        }

        /**
         * دریافت آمار استفاده از تم
         */
        getThemeStats() {
            const themeUsage = JSON.parse(localStorage.getItem('samanat_theme_stats') || '{}');
            const currentTheme = this.currentTheme;
            
            if (!themeUsage[currentTheme]) {
                themeUsage[currentTheme] = 0;
            }
            
            themeUsage[currentTheme]++;
            localStorage.setItem('samanat_theme_stats', JSON.stringify(themeUsage));
            
            return themeUsage;
        }
    }

    /**
     * نمونه سراسری Theme Manager
     */
    window.SamanetTheme = new ThemeManager();

    // Export برای compatibility
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = ThemeManager;
    }

    /**
     * تابع های سراسری برای backward compatibility
     */
    window.toggleTheme = function() {
        window.SamanetTheme.toggleTheme();
    };

    window.setTheme = function(theme) {
        window.SamanetTheme.setTheme(theme);
    };

    window.getCurrentTheme = function() {
        return window.SamanetTheme.getCurrentTheme();
    };

    window.isDarkMode = function() {
        return window.SamanetTheme.isDarkMode();
    };

    /**
     * فوری اعمال کردن تم برای جلوگیری از flash
     */
    (function() {
        try {
            const savedTheme = localStorage.getItem('samanat_theme') || window.SamanetTheme.getSystemTheme();
            document.documentElement.setAttribute('data-theme', savedTheme);
            console.log('⚡ Theme applied immediately:', savedTheme);
        } catch (error) {
            console.error('❌ Error applying immediate theme:', error);
            document.documentElement.setAttribute('data-theme', 'light');
        }
    })();

    /**
     * Event listeners برای DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Theme System ready');
        });
    } else {
        console.log('🚀 Theme System ready');
    }

    /**
     * پشتیبانی از Auto Theme Detection
     */
    if (window.matchMedia && !localStorage.getItem('samanat_theme')) {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        window.SamanetTheme.setTheme(prefersDark.matches ? 'dark' : 'light', false);
    }

})(); 