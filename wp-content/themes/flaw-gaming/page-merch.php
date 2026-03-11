<?php
/**
 * Merch Page Template
 *
 * Coming Soon placeholder for FLAW Gaming merchandise.
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <header class="page-header page-header--merch">
        <div class="container">
            <p class="page-tagline">Official Gear</p>
            <h1 class="page-title">FLAW <span class="text-accent">Merch</span></h1>
            <p class="page-subtitle">Rep the squad. Coming soon.</p>
        </div>
    </header>

    <div class="page-body">
        <div class="container">
            <section class="merch-coming-soon">
                <div class="coming-soon-card">
                    <div class="coming-soon-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="80" height="80">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="3" y1="6" x2="21" y2="6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 10a4 4 0 0 1-8 0" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2>Merch Store Coming Soon</h2>
                    <p>We're working on official FLAW Gaming apparel, accessories, and more. Be the first to know when we launch.</p>

                    <div class="coming-soon-actions">
                        <a href="https://discord.gg/flawgaminghq"
                           class="btn btn--discord btn--lg"
                           target="_blank"
                           rel="noopener noreferrer">
                            Get Notified via Discord
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<style>
.page-header--merch {
    background:
        radial-gradient(ellipse at 50% 0%, rgba(212, 168, 67, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 100%, rgba(201, 40, 45, 0.1) 0%, transparent 50%),
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

.text-accent {
    color: var(--color-primary);
}

.merch-coming-soon {
    display: flex;
    justify-content: center;
    padding: var(--space-16) 0;
}

.coming-soon-card {
    text-align: center;
    max-width: 600px;
    padding: var(--space-12);
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    transition: border-color var(--transition-fast);
}

.coming-soon-card:hover {
    border-color: rgba(212, 168, 67, 0.3);
}

.coming-soon-icon {
    color: var(--color-primary);
    margin-bottom: var(--space-6);
    opacity: 0.8;
}

.coming-soon-icon svg {
    display: inline-block;
}

.coming-soon-card h2 {
    font-family: var(--font-display);
    font-size: var(--text-2xl);
    font-weight: var(--font-bold);
    text-transform: uppercase;
    margin: 0 0 var(--space-4);
}

.coming-soon-card p {
    color: var(--color-text-secondary);
    font-size: var(--text-lg);
    line-height: var(--leading-relaxed);
    margin: 0 0 var(--space-8);
}

.coming-soon-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--space-4);
}
</style>

<?php
get_footer();
