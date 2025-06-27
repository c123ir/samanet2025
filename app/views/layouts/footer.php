    <!-- Mobile Bottom Navigation Bar -->
    <nav class="mobile-bottom-nav">
        <a href="<?= url('requests') ?>" class="nav-item" data-page="requests">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>درخواست</span>
        </a>
        <a href="<?= url('documents') ?>" class="nav-item" data-page="documents">
            <i class="fas fa-images"></i>
            <span>اسناد</span>
        </a>
        <a href="<?= url('dashboard') ?>" class="nav-item" data-page="dashboard">
            <i class="fas fa-tachometer-alt"></i>
            <span>داشبورد</span>
        </a>
        <button class="nav-item" id="mobileSearchBtn">
            <i class="fas fa-search"></i>
            <span>جستجو</span>
        </button>
        <a href="<?= url('users/profile') ?>" class="nav-item" data-page="users">
            <i class="fas fa-user-circle"></i>
            <span>پروفایل</span>
        </a>
    </nav>

    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Samanat App Main JS File -->
    <script src="/assets/js/app.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sidebar Toggle for Mobile ---
        const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (sidebarToggleBtn && sidebar && sidebarOverlay) {
            const toggleSidebar = () => {
                const isVisible = sidebar.classList.contains('show');
                sidebar.classList.toggle('show', !isVisible);
                sidebarOverlay.style.display = !isVisible ? 'block' : 'none';
            };

            sidebarToggleBtn.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // --- Theme Toggle from Header ---
        const themeToggleBtn = document.getElementById('theme-toggle-btn');
        if (themeToggleBtn && typeof toggleTheme === 'function') {
            themeToggleBtn.addEventListener('click', toggleTheme);
        }
        // Sync header icon with main theme icon
        const themeIconHeader = document.getElementById('theme-icon-header');
        const themeIconMobile = document.getElementById('theme-icon'); // Assuming this is the old ID
        if(themeIconHeader) {
            // Initial sync
            const currentTheme = document.documentElement.getAttribute('data-theme');
            themeIconHeader.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            // Use MutationObserver to keep them in sync
            const observer = new MutationObserver(() => {
                themeIconHeader.className = themeIconMobile.className;
            });
            if(themeIconMobile) observer.observe(themeIconMobile, { attributes: true });
        }

        // --- Mobile Bottom Nav Logic ---
        try {
            const urlParams = new URLSearchParams(window.location.search);
            let currentPage = urlParams.get('route') || 'dashboard'; // Default to dashboard if no route
            
            // Handle cases like 'users/profile'
            if (currentPage.includes('/')) {
                currentPage = currentPage.split('/')[0];
            }

            console.log('Mobile Nav: Detected page -> "' + currentPage + '"');

            const navItems = document.querySelectorAll('.mobile-bottom-nav .nav-item');
            
            navItems.forEach(item => {
                const pageName = item.dataset.page;
                if (pageName && currentPage.startsWith(pageName)) {
                    item.classList.add('active');
                }
            });
        } catch (e) {
            console.error("Error in mobile nav logic:", e);
        }
        
        // --- Mobile Search Button ---
        const mobileSearchBtn = document.getElementById('mobileSearchBtn');
        if (mobileSearchBtn) {
            mobileSearchBtn.addEventListener('click', () => {
                const searchInput = document.querySelector('#requestAdvancedSearch input, #advancedSearchInput');
                if (searchInput) {
                    searchInput.focus();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    // Fallback if search input is not on the page
                    window.location.href = '<?= url('requests') ?>';
                }
            });
        }
    });
    </script>

    </body>
</html> 