<?php
/**
 * Contact Page Template
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <header class="page-header page-header--contact">
        <div class="container">
            <p class="page-tagline">Get in Touch</p>
            <h1 class="page-title">Contact <span class="text-accent--gold">Us</span></h1>
            <p class="page-subtitle">Questions, partnerships, or just want to say hello? We'd love to hear from you.</p>
        </div>
    </header>

    <div class="page-body">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Methods -->
                <div class="contact-methods">
                    <div class="contact-card">
                        <div class="contact-card__icon">
                            <?php echo function_exists('flaw_get_social_icon') ? flaw_get_social_icon('discord') : ''; ?>
                        </div>
                        <h2>Discord</h2>
                        <p>The fastest way to reach us. Join our server and open a ticket.</p>
                        <a href="https://discord.gg/flawgaminghq"
                           class="btn btn--discord"
                           target="_blank"
                           rel="noopener noreferrer">
                            Join Discord
                        </a>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <h2>Email</h2>
                        <p>For business inquiries, partnerships, and sponsorships.</p>
                        <a href="mailto:contact@flawgaming.com" class="btn btn--outline btn--sm">
                            contact@flawgaming.com
                        </a>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon">
                            <?php echo function_exists('flaw_get_social_icon') ? flaw_get_social_icon('twitter') : ''; ?>
                        </div>
                        <h2>Social Media</h2>
                        <p>Follow us for the latest news, highlights, and updates.</p>
                        <?php if (has_nav_menu('social')) : ?>
                            <nav class="contact-socials" aria-label="Social Links">
                                <?php
                                wp_nav_menu([
                                    'theme_location' => 'social',
                                    'menu_class'     => 'social-menu',
                                    'container'      => false,
                                    'depth'          => 1,
                                    'link_before'    => '<span class="screen-reader-text">',
                                    'link_after'     => '</span>',
                                ]);
                                ?>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-area">
                    <h2>Send a Message</h2>
                    <?php
                    // Check if page has content (e.g., a Contact Form 7 shortcode)
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                            $content = get_the_content();
                            if (!empty(trim($content))) :
                                the_content();
                            else :
                    ?>
                        <form class="contact-form" id="contact-form">
                            <?php wp_nonce_field('flaw_contact_nonce', 'contact_nonce'); ?>

                            <div class="form-group">
                                <label for="contact-name">Name <span class="required">*</span></label>
                                <input type="text" id="contact-name" name="name" required placeholder="Your name">
                            </div>

                            <div class="form-group">
                                <label for="contact-email">Email <span class="required">*</span></label>
                                <input type="email" id="contact-email" name="email" required placeholder="your@email.com">
                            </div>

                            <div class="form-group">
                                <label for="contact-subject">Subject</label>
                                <select id="contact-subject" name="subject">
                                    <option value="general">General Inquiry</option>
                                    <option value="partnership">Partnership / Sponsorship</option>
                                    <option value="media">Media / Press</option>
                                    <option value="recruitment">Recruitment</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="contact-message">Message <span class="required">*</span></label>
                                <textarea id="contact-message" name="message" required rows="6" placeholder="Your message..."></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn--primary btn--lg">Send Message</button>
                            </div>
                        </form>
                    <?php
                            endif;
                        endwhile;
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.page-header--contact {
    background:
        radial-gradient(ellipse at 50% 0%, rgba(201, 40, 45, 0.1) 0%, transparent 50%),
        var(--color-bg-secondary);
    padding: calc(var(--header-height) + var(--space-16)) 0 var(--space-16);
    text-align: center;
    border-bottom: 1px solid var(--color-border);
}

.page-tagline {
    font-family: var(--font-display);
    font-size: var(--text-sm);
    font-weight: var(--font-bold);
    text-transform: uppercase;
    letter-spacing: var(--tracking-widest);
    color: var(--color-primary);
    margin-bottom: var(--space-2);
}

.page-subtitle {
    font-size: var(--text-lg);
    color: var(--color-text-secondary);
    max-width: 500px;
    margin: var(--space-4) auto 0;
}

.text-accent--gold {
    color: var(--color-secondary);
}

.contact-grid {
    display: grid;
    gap: var(--space-12);
    padding: var(--space-12) 0;
}

@media (min-width: 1024px) {
    .contact-grid {
        grid-template-columns: 1fr 1fr;
        align-items: start;
    }
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

.contact-card {
    padding: var(--space-6);
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    transition: border-color var(--transition-fast);
}

.contact-card:hover {
    border-color: rgba(255, 255, 255, 0.1);
}

.contact-card__icon {
    color: var(--color-primary);
    margin-bottom: var(--space-3);
}

.contact-card__icon svg {
    width: 32px;
    height: 32px;
}

.contact-card h2 {
    font-family: var(--font-display);
    font-size: var(--text-lg);
    font-weight: var(--font-bold);
    margin: 0 0 var(--space-2);
}

.contact-card p {
    color: var(--color-text-secondary);
    font-size: var(--text-sm);
    margin: 0 0 var(--space-4);
}

.contact-socials .social-menu {
    display: flex;
    gap: var(--space-2);
    list-style: none;
    margin: 0;
    padding: 0;
}

.contact-form-area h2 {
    font-family: var(--font-display);
    font-size: var(--text-2xl);
    font-weight: var(--font-bold);
    margin: 0 0 var(--space-6);
}

.contact-form {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-8);
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

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-group select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right var(--space-4) center;
    padding-right: var(--space-10);
}

.form-actions {
    margin-top: var(--space-6);
}
</style>

<?php
get_footer();
