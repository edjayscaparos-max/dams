document.addEventListener('DOMContentLoaded', function() {
    // Menu toggle for mobile
    const menuToggle = document.getElementById('menubar-toggle-btn');
    const body = document.body;

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            body.classList.toggle('menubar-unfold');
            
            // Close menu when clicking outside on mobile
            if (body.classList.contains('menubar-unfold')) {
                document.addEventListener('click', function closeMenu(e) {
                    if (!e.target.closest('#menubar') && !e.target.closest('#menubar-toggle-btn')) {
                        body.classList.remove('menubar-unfold');
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }
        });
    }

    // Submenu toggles
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const menuItem = this.closest('.has-submenu');
            
            // Close other open submenus
            document.querySelectorAll('.has-submenu.open').forEach(item => {
                if (item !== menuItem) {
                    item.classList.remove('open');
                }
            });
            
            menuItem.classList.toggle('open');
        });
    });

    // Add active state to current page link
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.menu-link, .submenu-link');
    
    menuLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath.split('/').pop()) {
            link.classList.add('active');
            // Open parent submenu if exists
            const parentSubmenu = link.closest('.submenu');
            if (parentSubmenu) {
                parentSubmenu.parentElement.classList.add('open');
            }
        }
    });

    // Handle dropdown menus in header
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownMenu = this.nextElementSibling;
            
            // Close other open dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                }
            });
            
            dropdownMenu.classList.toggle('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.matches('.dropdown-toggle')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});