<?php
/**
 * CTA Section Template
 *
 * @package FLAW_Gaming
 */

$discord_url = get_theme_mod('flaw_discord_url', 'https://discord.gg/flawgaminghq');
?>

<section class="cta-section" aria-labelledby="cta-heading">
    <div class="cta-section__bg"></div>
    <div class="container">
        <div class="cta-content">
            <p class="cta-tagline">Join the Movement</p>
            <h2 id="cta-heading" class="cta-title">Ready to Go <span>Flawless</span>?</h2>
            <p class="cta-description">
                Whether you're a competitive player, content creator, or passionate gamer,
                FLAW Gaming has a place for you. Join our community of 50+ members today.
            </p>

            <div class="cta-actions">
                <a href="<?php echo esc_url($discord_url); ?>" class="btn btn--discord btn--lg" target="_blank" rel="noopener noreferrer">
                    Join Discord
                </a>
                <a href="<?php echo esc_url(home_url('/about')); ?>" class="btn btn--outline btn--lg">
                    Learn More
                </a>
            </div>

            <div class="cta-features">
                <div class="cta-feature">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Free to Join</span>
                </div>
                <div class="cta-feature">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Active Community</span>
                </div>
                <div class="cta-feature">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Regular Events</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.cta-section {
    position: relative;
    padding: var(--space-24) 0;
    text-align: center;
    overflow: hidden;
}

.cta-section__bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 50% 0%, rgba(212, 168, 67, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse at 0% 100%, rgba(201, 40, 45, 0.1) 0%, transparent 50%),
        radial-gradient(ellipse at 100% 100%, rgba(227, 252, 2, 0.05) 0%, transparent 50%),
        var(--color-bg-secondary);
    z-index: -1;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--color-primary), transparent);
}

.cta-tagline {
    font-family: var(--font-display);
    font-size: var(--text-sm);
    font-weight: var(--font-bold);
    text-transform: uppercase;
    letter-spacing: var(--tracking-widest);
    color: var(--color-primary);
    margin-bottom: var(--space-4);
}

.cta-title {
    font-family: var(--font-display);
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: var(--font-extrabold);
    text-transform: uppercase;
    margin: 0 0 var(--space-6);
    line-height: 1;
}

.cta-title span {
    color: var(--color-primary);
    text-shadow:
        0 0 10px rgba(212, 168, 67, 0.5),
        0 0 20px rgba(212, 168, 67, 0.3);
}

.cta-description {
    font-size: var(--text-lg);
    color: var(--color-text-secondary);
    max-width: 600px;
    margin: 0 auto var(--space-8);
    line-height: var(--leading-relaxed);
}

.cta-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--space-4);
    margin-bottom: var(--space-10);
}

.cta-features {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--space-6);
}

.cta-feature {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
}

.cta-feature svg {
    color: var(--color-success);
}
</style>
