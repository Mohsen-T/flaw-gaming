/**
 * Countdown Timer Module
 *
 * Lightweight vanilla JS countdown timer using requestAnimationFrame
 * for efficient updates without setInterval overhead.
 *
 * @package FLAW_Gaming
 */

(function() {
    'use strict';

    /**
     * CountdownTimer Class
     */
    class CountdownTimer {
        /**
         * Initialize countdown timer
         * @param {HTMLElement} element - Container element with data-countdown attribute
         */
        constructor(element) {
            this.element = element;
            this.targetDate = element.dataset.countdown;
            this.targetTimestamp = parseInt(element.dataset.countdownTimestamp, 10) * 1000;

            if (!this.targetTimestamp || isNaN(this.targetTimestamp)) {
                this.targetTimestamp = new Date(this.targetDate).getTime();
            }

            this.units = {
                days: element.querySelector('[data-unit="days"]'),
                hours: element.querySelector('[data-unit="hours"]'),
                minutes: element.querySelector('[data-unit="minutes"]'),
                seconds: element.querySelector('[data-unit="seconds"]')
            };

            this.lastValues = {};
            this.isRunning = false;
            this.rafId = null;
        }

        /**
         * Calculate time remaining
         * @returns {Object|null} Time units or null if expired
         */
        getTimeRemaining() {
            const now = Date.now();
            const diff = this.targetTimestamp - now;

            if (diff <= 0) {
                return null;
            }

            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            return {
                days: days,
                hours: hours % 24,
                minutes: minutes % 60,
                seconds: seconds % 60
            };
        }

        /**
         * Update DOM elements with new values
         * @param {Object} time - Time units object
         */
        updateDisplay(time) {
            if (!time) {
                this.handleExpired();
                return;
            }

            Object.keys(this.units).forEach(unit => {
                const el = this.units[unit];
                if (!el) return;

                const value = time[unit];
                const padded = String(value).padStart(2, '0');

                // Only update DOM if value changed
                if (this.lastValues[unit] !== padded) {
                    this.lastValues[unit] = padded;
                    el.textContent = padded;

                    // Add animation class for visual feedback
                    el.classList.add('countdown__value--updated');
                    setTimeout(() => {
                        el.classList.remove('countdown__value--updated');
                    }, 200);
                }
            });
        }

        /**
         * Handle countdown expiration
         */
        handleExpired() {
            this.stop();
            this.element.classList.add('countdown--expired');

            // Dispatch custom event
            this.element.dispatchEvent(new CustomEvent('countdown:expired', {
                bubbles: true,
                detail: { targetDate: this.targetDate }
            }));

            // Update display to zeros
            Object.keys(this.units).forEach(unit => {
                if (this.units[unit]) {
                    this.units[unit].textContent = '00';
                }
            });

            // Optional: Show expired message
            const label = this.element.querySelector('.countdown__label');
            if (label) {
                label.textContent = 'Event Started';
            }
        }

        /**
         * Animation frame tick
         */
        tick() {
            if (!this.isRunning) return;

            const time = this.getTimeRemaining();
            this.updateDisplay(time);

            if (time) {
                this.rafId = requestAnimationFrame(() => this.tick());
            }
        }

        /**
         * Start the countdown
         */
        start() {
            if (this.isRunning) return;

            // Initial check
            const time = this.getTimeRemaining();
            if (!time) {
                this.handleExpired();
                return;
            }

            this.isRunning = true;
            this.updateDisplay(time);
            this.element.classList.add('countdown--active');

            // Use RAF for smooth updates
            this.rafId = requestAnimationFrame(() => this.tick());
        }

        /**
         * Stop the countdown
         */
        stop() {
            this.isRunning = false;
            if (this.rafId) {
                cancelAnimationFrame(this.rafId);
                this.rafId = null;
            }
            this.element.classList.remove('countdown--active');
        }

        /**
         * Destroy the countdown instance
         */
        destroy() {
            this.stop();
            this.element = null;
            this.units = null;
        }
    }

    /**
     * CountdownManager - Handles multiple countdown instances
     */
    class CountdownManager {
        constructor() {
            this.instances = new Map();
            this.observer = null;
        }

        /**
         * Initialize all countdown elements on page
         */
        init() {
            // Find all countdown elements
            const elements = document.querySelectorAll('[data-countdown]');
            elements.forEach(el => this.add(el));

            // Set up intersection observer for performance
            this.setupObserver();

            // Handle dynamic content
            this.observeDOM();
        }

        /**
         * Add a countdown instance
         * @param {HTMLElement} element
         */
        add(element) {
            if (this.instances.has(element)) return;

            const countdown = new CountdownTimer(element);
            this.instances.set(element, countdown);

            // Start if visible
            if (this.isElementVisible(element)) {
                countdown.start();
            }
        }

        /**
         * Remove a countdown instance
         * @param {HTMLElement} element
         */
        remove(element) {
            const countdown = this.instances.get(element);
            if (countdown) {
                countdown.destroy();
                this.instances.delete(element);
            }
        }

        /**
         * Check if element is visible in viewport
         * @param {HTMLElement} element
         * @returns {boolean}
         */
        isElementVisible(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top < window.innerHeight &&
                rect.bottom > 0 &&
                rect.left < window.innerWidth &&
                rect.right > 0
            );
        }

        /**
         * Set up intersection observer for lazy starting
         */
        setupObserver() {
            if (!('IntersectionObserver' in window)) {
                // Fallback: start all countdowns
                this.instances.forEach(countdown => countdown.start());
                return;
            }

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const countdown = this.instances.get(entry.target);
                    if (!countdown) return;

                    if (entry.isIntersecting) {
                        countdown.start();
                    } else {
                        countdown.stop();
                    }
                });
            }, {
                rootMargin: '100px',
                threshold: 0
            });

            this.instances.forEach((countdown, element) => {
                this.observer.observe(element);
            });
        }

        /**
         * Observe DOM for dynamically added countdown elements
         */
        observeDOM() {
            if (!('MutationObserver' in window)) return;

            const domObserver = new MutationObserver((mutations) => {
                mutations.forEach(mutation => {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType !== 1) return;

                        // Check if added node is a countdown
                        if (node.hasAttribute && node.hasAttribute('data-countdown')) {
                            this.add(node);
                            if (this.observer) {
                                this.observer.observe(node);
                            }
                        }

                        // Check for countdown children
                        if (node.querySelectorAll) {
                            const countdowns = node.querySelectorAll('[data-countdown]');
                            countdowns.forEach(el => {
                                this.add(el);
                                if (this.observer) {
                                    this.observer.observe(el);
                                }
                            });
                        }
                    });

                    // Handle removed nodes
                    mutation.removedNodes.forEach(node => {
                        if (node.nodeType !== 1) return;

                        if (node.hasAttribute && node.hasAttribute('data-countdown')) {
                            this.remove(node);
                        }

                        if (node.querySelectorAll) {
                            const countdowns = node.querySelectorAll('[data-countdown]');
                            countdowns.forEach(el => this.remove(el));
                        }
                    });
                });
            });

            domObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        /**
         * Destroy all instances
         */
        destroy() {
            this.instances.forEach(countdown => countdown.destroy());
            this.instances.clear();

            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
        }
    }

    // Initialize on DOM ready
    const manager = new CountdownManager();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => manager.init());
    } else {
        manager.init();
    }

    // Expose globally for manual control
    window.FlawCountdown = {
        manager: manager,
        Timer: CountdownTimer
    };

})();
