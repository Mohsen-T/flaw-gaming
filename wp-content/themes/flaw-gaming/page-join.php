<?php
/**
 * Join Page Template
 *
 * @package FLAW_Gaming
 */

get_header();

$discord_url = get_theme_mod('flaw_discord_url', 'https://discord.gg/flawgaminghq');
?>

<main id="main" class="site-main">
    <header class="page-header page-header--join">
        <div class="container">
            <h1 class="page-title">Join <span>FLAW Gaming</span></h1>
            <p class="page-subtitle">Be part of something bigger. Join our community of competitive gamers, content creators, and esports enthusiasts.</p>
        </div>
    </header>

    <div class="page-body">
        <div class="container">

            <!-- Join Options -->
            <section class="join-options">
                <div class="join-option">
                    <div class="join-option__icon">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
                            <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                        </svg>
                    </div>
                    <h2 class="join-option__title">Join Our Discord</h2>
                    <p class="join-option__description">The fastest way to connect with us. Join our Discord server to chat with the community, find teammates, and stay updated on events.</p>
                    <a href="<?php echo esc_url($discord_url); ?>" class="btn btn--discord btn--lg" target="_blank" rel="noopener noreferrer">
                        Join Discord Server
                    </a>
                </div>
            </section>

            <!-- Application Form -->
            <section class="join-form-section">
                <h2 class="section-title">Apply to Join</h2>
                <p class="section-description">Want to represent FLAW Gaming as a player, creator, or staff member? Fill out the application below.</p>

                <form class="join-form" id="join-form" method="post">
                    <?php wp_nonce_field('flaw_application_nonce', 'application_nonce'); ?>
                    <input type="hidden" name="action" value="flaw_submit_application">

                    <div id="form-messages"></div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="join-name">Your Name / Gamertag <span class="required">*</span></label>
                            <input type="text" id="join-name" name="name" required placeholder="e.g., Phoenix">
                        </div>
                        <div class="form-group">
                            <label for="join-email">Email Address <span class="required">*</span></label>
                            <input type="email" id="join-email" name="email" required placeholder="your@email.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="join-discord">Discord Username <span class="required">*</span></label>
                            <input type="text" id="join-discord" name="discord" required placeholder="username">
                        </div>
                        <div class="form-group">
                            <label for="join-age">Age <span class="required">*</span></label>
                            <input type="number" id="join-age" name="age" required min="13" max="99" placeholder="18">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="join-role">What role are you applying for? <span class="required">*</span></label>
                        <select id="join-role" name="role" required>
                            <option value="">Select a role...</option>
                            <option value="player">Competitive Player</option>
                            <option value="creator">Content Creator</option>
                            <option value="coach">Coach / Analyst</option>
                            <option value="manager">Team Manager</option>
                            <option value="moderator">Community Moderator</option>
                            <option value="designer">Graphic Designer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="join-game">Primary Game</label>
                        <select id="join-game" name="game">
                            <option value="">Select a game...</option>
                            <option value="off-the-grid">Off The Grid</option>
                            <option value="valorant">Valorant</option>
                            <option value="cs2">Counter-Strike 2</option>
                            <option value="lol">League of Legends</option>
                            <option value="apex">Apex Legends</option>
                            <option value="fortnite">Fortnite</option>
                            <option value="rocket-league">Rocket League</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="join-experience">Tell us about yourself and your experience <span class="required">*</span></label>
                        <textarea id="join-experience" name="experience" required rows="5" placeholder="Share your gaming background, achievements, content creation experience, or why you want to join FLAW Gaming..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="join-links">Social Media / Portfolio Links</label>
                        <textarea id="join-links" name="links" rows="3" placeholder="Twitch, YouTube, Twitter, etc. (one per line)"></textarea>
                    </div>

                    <div class="form-group form-group--checkbox">
                        <label>
                            <input type="checkbox" name="terms" id="join-terms" required>
                            <span>I agree to follow FLAW Gaming's community guidelines and code of conduct <span class="required">*</span></span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary btn--lg" id="submit-btn">
                            <span class="btn-text">Submit Application</span>
                            <span class="btn-loading" style="display:none;">
                                <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>

                    <p class="form-note">Applications are reviewed within 48-72 hours. You'll receive a response via Discord or email.</p>
                </form>
            </section>

            <!-- Requirements -->
            <section class="join-requirements">
                <h2 class="section-title">Requirements</h2>
                <div class="requirements-grid">
                    <div class="requirement-card">
                        <h3>Competitive Players</h3>
                        <ul>
                            <li>Must be at least 16 years old</li>
                            <li>Ranked Diamond+ (or equivalent)</li>
                            <li>Available for team practice</li>
                            <li>Positive attitude & teamwork</li>
                            <li>Working microphone required</li>
                        </ul>
                    </div>
                    <div class="requirement-card">
                        <h3>Content Creators</h3>
                        <ul>
                            <li>Must be at least 16 years old</li>
                            <li>Active on at least one platform</li>
                            <li>Consistent upload schedule</li>
                            <li>Original content only</li>
                            <li>Aligned with our values</li>
                        </ul>
                    </div>
                    <div class="requirement-card">
                        <h3>Staff Members</h3>
                        <ul>
                            <li>Must be at least 18 years old</li>
                            <li>Previous experience preferred</li>
                            <li>Reliable and professional</li>
                            <li>Good communication skills</li>
                            <li>Passion for esports</li>
                        </ul>
                    </div>
                </div>
            </section>

        </div>
    </div>
</main>

<style>
.page-header--join {
    background:
        radial-gradient(ellipse at 50% 0%, rgba(212, 168, 67, 0.2) 0%, transparent 50%),
        var(--color-bg-secondary);
    padding: calc(var(--header-height) + var(--space-16)) 0 var(--space-12);
}

.page-header--join .page-title span {
    color: var(--color-primary);
}

.page-subtitle {
    font-size: var(--text-xl);
    color: var(--color-text-secondary);
    max-width: 600px;
    margin: var(--space-4) auto 0;
}

/* Join Options */
.join-options {
    display: flex;
    justify-content: center;
    margin-bottom: var(--space-16);
}

.join-option {
    text-align: center;
    padding: var(--space-10);
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    max-width: 500px;
}

.join-option__icon {
    color: #5865F2;
    margin-bottom: var(--space-4);
}

.join-option__title {
    font-family: var(--font-display);
    font-size: var(--text-2xl);
    margin-bottom: var(--space-3);
}

.join-option__description {
    color: var(--color-text-secondary);
    margin-bottom: var(--space-6);
}

/* Form Section */
.join-form-section {
    max-width: 700px;
    margin: 0 auto var(--space-16);
}

.section-description {
    color: var(--color-text-secondary);
    margin-bottom: var(--space-8);
}

.join-form {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-8);
}

.form-row {
    display: grid;
    gap: var(--space-6);
    margin-bottom: var(--space-6);
}

@media (min-width: 640px) {
    .form-row {
        grid-template-columns: 1fr 1fr;
    }
}

.form-group {
    margin-bottom: var(--space-6);
}

.form-group label {
    display: block;
    font-weight: var(--font-medium);
    margin-bottom: var(--space-2);
    color: var(--color-text-primary);
}

.form-group .required {
    color: var(--color-primary);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    background: var(--color-bg-tertiary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    color: var(--color-text-primary);
    font-size: var(--text-base);
    transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.1);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: var(--color-text-muted);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-group--checkbox label {
    display: flex;
    align-items: flex-start;
    gap: var(--space-3);
    cursor: pointer;
}

.form-group--checkbox input[type="checkbox"] {
    width: auto;
    margin-top: 4px;
    accent-color: var(--color-primary);
}

.form-actions {
    margin-top: var(--space-8);
    text-align: center;
}

.form-note {
    text-align: center;
    font-size: var(--text-sm);
    color: var(--color-text-tertiary);
    margin-top: var(--space-4);
}

/* Requirements */
.join-requirements {
    margin-top: var(--space-16);
}

.join-requirements .section-title {
    text-align: center;
    margin-bottom: var(--space-8);
}

.requirements-grid {
    display: grid;
    gap: var(--space-6);
}

@media (min-width: 768px) {
    .requirements-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.requirement-card {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-6);
}

.requirement-card h3 {
    font-family: var(--font-display);
    font-size: var(--text-lg);
    color: var(--color-primary);
    margin-bottom: var(--space-4);
}

.requirement-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirement-card li {
    position: relative;
    padding-left: var(--space-6);
    margin-bottom: var(--space-2);
    color: var(--color-text-secondary);
    font-size: var(--text-sm);
}

.requirement-card li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 8px;
    width: 8px;
    height: 8px;
    background: var(--color-success);
    border-radius: 50%;
}

/* Discord Button */
.btn--discord {
    background: #5865F2;
    color: white;
}

.btn--discord:hover {
    background: #4752C4;
}
</style>

<script>
(function() {
    const form = document.getElementById('join-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const messagesDiv = document.getElementById('form-messages');

    function showMessage(message, type) {
        messagesDiv.innerHTML = `<div class="form-message form-message--${type}">${message}</div>`;
        messagesDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setLoading(loading) {
        submitBtn.disabled = loading;
        btnText.style.display = loading ? 'none' : 'inline';
        btnLoading.style.display = loading ? 'inline-flex' : 'none';
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous messages
        messagesDiv.innerHTML = '';

        // Validate terms checkbox
        if (!document.getElementById('join-terms').checked) {
            showMessage('Please agree to the community guidelines.', 'error');
            return;
        }

        setLoading(true);

        // Prepare form data
        const formData = new FormData(form);
        formData.append('nonce', document.querySelector('[name="application_nonce"]').value);

        // Submit via AJAX
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            setLoading(false);

            if (data.success) {
                showMessage(data.data.message, 'success');
                form.reset();

                // Hide form after success
                setTimeout(() => {
                    form.querySelector('.form-row').closest('form').querySelectorAll('.form-group, .form-row, .form-actions, .form-note').forEach(el => {
                        el.style.display = 'none';
                    });
                }, 100);
            } else {
                showMessage(data.data.message || 'An error occurred. Please try again.', 'error');
            }
        })
        .catch(error => {
            setLoading(false);
            showMessage('Network error. Please check your connection and try again.', 'error');
            console.error('Application submission error:', error);
        });
    });
})();
</script>

<style>
/* Form Messages */
.form-message {
    padding: var(--space-4);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-6);
    font-weight: var(--font-medium);
}

.form-message--success {
    background: rgba(0, 163, 42, 0.15);
    border: 1px solid #00a32a;
    color: #00a32a;
}

.form-message--error {
    background: rgba(214, 54, 56, 0.15);
    border: 1px solid #d63638;
    color: #d63638;
}

/* Loading Spinner */
.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>

<?php
get_footer();
