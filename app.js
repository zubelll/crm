document.addEventListener('DOMContentLoaded', () => {
    // 1. Dropdown Toggles (Notif & Avatar)
    const toggleDropdown = (buttonSelector, dropdownSelector) => {
        const btn = document.querySelector(buttonSelector);
        const dropdown = document.querySelector(dropdownSelector);
        
        if(btn && dropdown) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if(menu !== dropdown) menu.style.display = 'none';
                });
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });
        }
    };

    toggleDropdown('.topbar-icon-btn', '.notif-dropdown');
    toggleDropdown('.topbar-avatar', '.avatar-dropdown');

    // Close dropdowns on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    });

    // Prevent closing when clicking inside dropdown
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', (e) => e.stopPropagation());
    });

    // 2. Mobile Sidebar Toggle
    const sidebarToggleBtn = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileOverlay = document.querySelector('.mobile-sidebar-overlay');

    if(sidebarToggleBtn && sidebar && mobileOverlay) {
        sidebarToggleBtn.addEventListener('click', () => {
            sidebar.classList.add('show');
            mobileOverlay.classList.add('show');
        });

        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            mobileOverlay.classList.remove('show');
        });
    }

    // 3. Modals
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }
    }

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    }

    // Close modal on clicking outside content
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if(e.target === overlay) {
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });
});
