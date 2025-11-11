/**
 * Hamburger Menu JavaScript
 * Handles mobile menu toggle functionality
 */

(function() {
    'use strict';

    const hamburgerBtn = document.getElementById('hamburger-menu-btn');
    const menuOverlay = document.getElementById('mobile-menu-overlay');
    const menuPanel = document.getElementById('mobile-menu-panel');
    const menuClose = document.getElementById('mobile-menu-close');

    if (!hamburgerBtn || !menuOverlay || !menuPanel || !menuClose) {
        return; // Elements not found, exit early
    }

    // Open menu
    function openMenu() {
        hamburgerBtn.classList.add('active');
        menuOverlay.classList.add('active');
        menuPanel.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling

        // Change hamburger to X
        const hamburgerIcon = hamburgerBtn.querySelector('.hamburger-icon');
        const menuText = hamburgerBtn.querySelector('.mobile-nav-text');
        if (hamburgerIcon) {
            hamburgerIcon.style.display = 'none';
        }
        if (menuText) {
            menuText.textContent = '✕';
            menuText.style.fontSize = '1.5rem';
            menuText.style.marginTop = '0';
        }
    }

    // Close menu
    function closeMenu() {
        hamburgerBtn.classList.remove('active');
        menuOverlay.classList.remove('active');
        menuPanel.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling

        // Change X back to hamburger
        const hamburgerIcon = hamburgerBtn.querySelector('.hamburger-icon');
        const menuText = hamburgerBtn.querySelector('.mobile-nav-text');
        if (hamburgerIcon) {
            hamburgerIcon.style.display = 'flex';
        }
        if (menuText) {
            menuText.textContent = 'MENU';
            menuText.style.fontSize = '';
            menuText.style.marginTop = '';
        }
    }

    // Toggle menu
    function toggleMenu() {
        if (menuPanel.classList.contains('active')) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    // Event listeners
    hamburgerBtn.addEventListener('click', toggleMenu);
    menuClose.addEventListener('click', closeMenu);
    menuOverlay.addEventListener('click', closeMenu);

    // Close menu when clicking on a link (for navigation)
    const menuLinks = menuPanel.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Delay close slightly to allow navigation
            setTimeout(closeMenu, 100);
        });
    });

    // Close menu on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menuPanel.classList.contains('active')) {
            closeMenu();
        }
    });

    // Close menu on window resize if it gets too large
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024 && menuPanel.classList.contains('active')) {
            closeMenu();
        }
    });

})();
