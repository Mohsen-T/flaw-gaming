/**
 * Mobile Menu Toggle & Dropdown Support
 *
 * @package FLAW_Gaming
 */
(function() {
    'use strict';

    var menuToggle = document.querySelector('.menu-toggle');
    var nav = document.getElementById('site-navigation');
    var body = document.body;

    if (!menuToggle || !nav) return;

    // Toggle mobile menu
    menuToggle.addEventListener('click', function() {
        var isOpen = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isOpen);
        nav.classList.toggle('is-open');
        body.classList.toggle('menu-open');

        if (!isOpen) {
            body.style.overflow = 'hidden';
        } else {
            body.style.overflow = '';
        }
    });

    // Handle dropdown sub-menus on mobile (tap to toggle)
    var dropdownParents = nav.querySelectorAll('.menu-item-has-children');
    dropdownParents.forEach(function(item) {
        var link = item.querySelector(':scope > a');
        if (!link) return;

        link.addEventListener('click', function(e) {
            // Only intercept on mobile (when hamburger is visible)
            if (window.innerWidth < 1024) {
                e.preventDefault();
                item.classList.toggle('is-open');

                // Close other open dropdowns
                dropdownParents.forEach(function(other) {
                    if (other !== item) {
                        other.classList.remove('is-open');
                    }
                });
            }
        });
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && nav.classList.contains('is-open')) {
            menuToggle.setAttribute('aria-expanded', 'false');
            nav.classList.remove('is-open');
            body.classList.remove('menu-open');
            body.style.overflow = '';
            menuToggle.focus();
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (nav.classList.contains('is-open') &&
            !nav.contains(e.target) &&
            !menuToggle.contains(e.target)) {
            menuToggle.setAttribute('aria-expanded', 'false');
            nav.classList.remove('is-open');
            body.classList.remove('menu-open');
            body.style.overflow = '';
        }
    });

    // Handle resize: close mobile menu if screen grows past breakpoint
    var resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth >= 1024 && nav.classList.contains('is-open')) {
                menuToggle.setAttribute('aria-expanded', 'false');
                nav.classList.remove('is-open');
                body.classList.remove('menu-open');
                body.style.overflow = '';
            }
        }, 150);
    });
})();
