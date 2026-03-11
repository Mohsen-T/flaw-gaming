/**
 * Twitch Status Checker Module
 *
 * Lightweight vanilla JS for checking Twitch stream status
 * through a WordPress REST API proxy endpoint.
 *
 * @package FLAW_Gaming
 */

(function() {
    'use strict';

    /**
     * TwitchStatus Class
     */
    class TwitchStatus {
        constructor(options = {}) {
            this.apiEndpoint = options.apiEndpoint || (window.flawData?.restUrl + 'twitch/status');
            this.checkInterval = options.checkInterval || 60000; // 1 minute
            this.cache = new Map();
            this.cacheExpiry = options.cacheExpiry || 30000; // 30 seconds
            this.pendingRequests = new Map();
            this.elements = new Map();
            this.intervalId = null;
        }

        /**
         * Initialize status checkers for all elements
         */
        init() {
            // Find all Twitch status elements
            this.findElements();

            // Initial check
            this.checkAll();

            // Set up periodic checks
            this.startInterval();

            // Observe DOM for new elements
            this.observeDOM();

            // Handle visibility change
            this.handleVisibility();
        }

        /**
         * Find all Twitch-related elements
         */
        findElements() {
            // Status indicators
            document.querySelectorAll('[data-twitch-channel]').forEach(el => {
                const channel = el.dataset.twitchChannel.toLowerCase();
                if (!this.elements.has(channel)) {
                    this.elements.set(channel, new Set());
                }
                this.elements.get(channel).add(el);
            });
        }

        /**
         * Check status for all channels
         */
        async checkAll() {
            const channels = Array.from(this.elements.keys());

            if (channels.length === 0) return;

            try {
                const statuses = await this.fetchStatuses(channels);
                this.updateElements(statuses);
            } catch (error) {
                console.error('Twitch status check failed:', error);
            }
        }

        /**
         * Fetch statuses from API
         * @param {string[]} channels - Array of channel names
         * @returns {Promise<Object>} Status data
         */
        async fetchStatuses(channels) {
            // Check cache first
            const now = Date.now();
            const cached = {};
            const uncached = [];

            channels.forEach(channel => {
                const cacheEntry = this.cache.get(channel);
                if (cacheEntry && (now - cacheEntry.timestamp) < this.cacheExpiry) {
                    cached[channel] = cacheEntry.data;
                } else {
                    uncached.push(channel);
                }
            });

            // Return cached if all channels are cached
            if (uncached.length === 0) {
                return cached;
            }

            // Dedupe pending requests
            const requestKey = uncached.sort().join(',');
            if (this.pendingRequests.has(requestKey)) {
                const result = await this.pendingRequests.get(requestKey);
                return { ...cached, ...result };
            }

            // Make API request
            const requestPromise = this.makeRequest(uncached);
            this.pendingRequests.set(requestKey, requestPromise);

            try {
                const result = await requestPromise;

                // Update cache
                Object.entries(result).forEach(([channel, data]) => {
                    this.cache.set(channel, {
                        data: data,
                        timestamp: now
                    });
                });

                return { ...cached, ...result };
            } finally {
                this.pendingRequests.delete(requestKey);
            }
        }

        /**
         * Make API request
         * @param {string[]} channels
         * @returns {Promise<Object>}
         */
        async makeRequest(channels) {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ channels: channels })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            return response.json();
        }

        /**
         * Update DOM elements with status data
         * @param {Object} statuses - Channel status data
         */
        updateElements(statuses) {
            Object.entries(statuses).forEach(([channel, data]) => {
                const elements = this.elements.get(channel);
                if (!elements) return;

                elements.forEach(el => {
                    this.updateElement(el, data);
                });
            });
        }

        /**
         * Update a single element
         * @param {HTMLElement} el
         * @param {Object} data
         */
        updateElement(el, data) {
            const isLive = data?.live === true;

            // Update status classes
            el.classList.toggle('twitch-status--live', isLive);
            el.classList.toggle('twitch-status--offline', !isLive);

            // Update status indicator
            const indicator = el.querySelector('.twitch-status__indicator');
            if (indicator) {
                indicator.style.background = isLive ? 'var(--color-live)' : 'var(--color-text-muted)';
            }

            // Update status text
            const textEl = el.querySelector('.twitch-status__text');
            if (textEl) {
                if (isLive) {
                    textEl.textContent = `Live - ${this.formatViewers(data.viewer_count)} viewers`;
                } else {
                    textEl.textContent = 'Offline';
                }
            }

            // Update viewer count
            const viewersEl = el.querySelector('.viewers-count');
            if (viewersEl) {
                viewersEl.textContent = isLive ? this.formatViewers(data.viewer_count) : '--';
            }

            // Update thumbnail preview
            if (el.dataset.twitchPreview === 'true' && isLive && data.thumbnail_url) {
                this.updatePreview(el, data);
            }

            // Dispatch custom event
            el.dispatchEvent(new CustomEvent('twitch:statusUpdate', {
                bubbles: true,
                detail: { channel: el.dataset.twitchChannel, ...data }
            }));
        }

        /**
         * Update stream preview thumbnail
         * @param {HTMLElement} el
         * @param {Object} data
         */
        updatePreview(el, data) {
            let img = el.querySelector('.twitch-preview-img');

            if (!img) {
                img = document.createElement('img');
                img.className = 'twitch-preview-img';
                img.loading = 'lazy';
                el.prepend(img);
            }

            // Add cache buster to prevent stale previews
            const timestamp = Math.floor(Date.now() / 60000); // Updates every minute
            img.src = data.thumbnail_url + '?t=' + timestamp;
            img.alt = data.title || 'Live stream preview';
        }

        /**
         * Format viewer count
         * @param {number} count
         * @returns {string}
         */
        formatViewers(count) {
            if (!count) return '0';

            if (count >= 1000000) {
                return (count / 1000000).toFixed(1) + 'M';
            }

            if (count >= 1000) {
                return (count / 1000).toFixed(1) + 'K';
            }

            return count.toString();
        }

        /**
         * Start periodic status checks
         */
        startInterval() {
            if (this.intervalId) return;

            this.intervalId = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    this.checkAll();
                }
            }, this.checkInterval);
        }

        /**
         * Stop periodic checks
         */
        stopInterval() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        }

        /**
         * Handle page visibility changes
         */
        handleVisibility() {
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    // Check immediately when page becomes visible
                    this.checkAll();
                }
            });
        }

        /**
         * Observe DOM for dynamically added elements
         */
        observeDOM() {
            if (!('MutationObserver' in window)) return;

            const observer = new MutationObserver((mutations) => {
                let hasNewElements = false;

                mutations.forEach(mutation => {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType !== 1) return;

                        if (node.dataset?.twitchChannel) {
                            const channel = node.dataset.twitchChannel.toLowerCase();
                            if (!this.elements.has(channel)) {
                                this.elements.set(channel, new Set());
                            }
                            this.elements.get(channel).add(node);
                            hasNewElements = true;
                        }

                        if (node.querySelectorAll) {
                            node.querySelectorAll('[data-twitch-channel]').forEach(el => {
                                const channel = el.dataset.twitchChannel.toLowerCase();
                                if (!this.elements.has(channel)) {
                                    this.elements.set(channel, new Set());
                                }
                                this.elements.get(channel).add(el);
                                hasNewElements = true;
                            });
                        }
                    });
                });

                if (hasNewElements) {
                    this.checkAll();
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        /**
         * Manually check a specific channel
         * @param {string} channel
         * @returns {Promise<Object>}
         */
        async check(channel) {
            const statuses = await this.fetchStatuses([channel.toLowerCase()]);
            return statuses[channel.toLowerCase()];
        }

        /**
         * Clear cache
         */
        clearCache() {
            this.cache.clear();
        }

        /**
         * Destroy instance
         */
        destroy() {
            this.stopInterval();
            this.elements.clear();
            this.cache.clear();
            this.pendingRequests.clear();
        }
    }

    /**
     * Twitch Embed Helper
     */
    class TwitchEmbed {
        /**
         * Create Twitch player embed
         * @param {HTMLElement} container
         * @param {Object} options
         */
        static createPlayer(container, options = {}) {
            const channel = container.dataset.twitchChannel;
            if (!channel) return;

            const iframe = document.createElement('iframe');
            iframe.src = `https://player.twitch.tv/?channel=${encodeURIComponent(channel)}&parent=${window.location.hostname}&autoplay=true&muted=false`;
            iframe.allowFullscreen = true;
            iframe.allow = 'autoplay; fullscreen';
            iframe.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;border:0;';

            // Remove placeholder
            const placeholder = container.querySelector('.stream-embed__placeholder');
            if (placeholder) {
                placeholder.remove();
            }

            container.appendChild(iframe);
        }

        /**
         * Create Twitch chat embed
         * @param {HTMLElement} container
         * @param {string} channel
         */
        static createChat(container, channel) {
            const iframe = document.createElement('iframe');
            iframe.src = `https://www.twitch.tv/embed/${encodeURIComponent(channel)}/chat?parent=${window.location.hostname}&darkpopout`;
            iframe.style.cssText = 'width:100%;height:100%;border:0;';

            container.appendChild(iframe);
        }
    }

    // Initialize on DOM ready
    const twitchStatus = new TwitchStatus();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            twitchStatus.init();

            // Auto-embed Twitch players
            document.querySelectorAll('[data-twitch-channel][data-twitch-embed="true"]').forEach(el => {
                TwitchEmbed.createPlayer(el);
            });

            // Auto-embed Twitch chat
            document.querySelectorAll('#twitch-chat[data-twitch-channel]').forEach(el => {
                TwitchEmbed.createChat(el, el.dataset.twitchChannel);
            });
        });
    } else {
        twitchStatus.init();
    }

    // Expose globally
    window.FlawTwitch = {
        status: twitchStatus,
        embed: TwitchEmbed
    };

})();
