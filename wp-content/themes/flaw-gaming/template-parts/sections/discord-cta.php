<?php
/**
 * Discord CTA Section Template
 * Community-centric call-to-action inspired by Merciless Hub
 *
 * @package FLAW_Gaming
 */

$discord_url = get_theme_mod('flaw_discord_url', 'https://discord.gg/flawgaminghq');
$discord_title = get_theme_mod('flaw_discord_title', 'Join the FLAW Community');
$discord_description = get_theme_mod('flaw_discord_description', 'Connect with fellow gamers, find teammates, participate in events, and stay updated on everything FLAW Gaming.');

// Get Discord URL from social links if set
$social_discord = get_theme_mod('flaw_social_discord', '');
if (!empty($social_discord)) {
    $discord_url = $social_discord;
}
?>

<section class="discord-cta" aria-labelledby="discord-heading">
    <div class="container">
        <div class="discord-cta__card">
            <div class="discord-cta__icon">
                <svg viewBox="0 0 24 24" fill="currentColor" width="64" height="64">
                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/>
                </svg>
            </div>

            <div class="discord-cta__content">
                <h2 id="discord-heading" class="discord-cta__title"><?php echo esc_html($discord_title); ?></h2>
                <p class="discord-cta__description">
                    <?php echo esc_html($discord_description); ?>
                </p>

                <ul class="discord-cta__features">
                    <li>Voice Channels & LFG</li>
                    <li>Exclusive Giveaways</li>
                    <li>Scrim Coordination</li>
                    <li>Web3 Game Alpha</li>
                    <li>Community Events</li>
                    <li>Direct Team Access</li>
                </ul>

                <a href="<?php echo esc_url($discord_url); ?>" class="btn btn--discord btn--lg" target="_blank" rel="noopener noreferrer">
                    Join Our Discord
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.discord-cta {
    padding: var(--space-20) 0;
    background: var(--color-bg-primary);
}

.discord-cta__card {
    display: grid;
    gap: var(--space-8);
    align-items: center;
    padding: var(--space-12);
    background: linear-gradient(135deg, rgba(88, 101, 242, 0.1) 0%, rgba(88, 101, 242, 0.05) 100%);
    border: 1px solid rgba(88, 101, 242, 0.3);
    border-radius: var(--radius-xl);
    text-align: center;
}

@media (min-width: 768px) {
    .discord-cta__card {
        grid-template-columns: auto 1fr;
        text-align: left;
    }
}

.discord-cta__icon {
    color: #5865F2;
    filter: drop-shadow(0 0 20px rgba(88, 101, 242, 0.5));
}

.discord-cta__title {
    font-family: var(--font-display);
    font-size: var(--text-3xl);
    font-weight: var(--font-extrabold);
    text-transform: uppercase;
    margin: 0 0 var(--space-4);
}

.discord-cta__description {
    color: var(--color-text-secondary);
    margin: 0 0 var(--space-6);
    max-width: 500px;
}

.discord-cta__features {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-3);
    list-style: none;
    padding: 0;
    margin: 0 0 var(--space-8);
}

.discord-cta__features li {
    padding: var(--space-2) var(--space-4);
    background: rgba(88, 101, 242, 0.2);
    border-radius: var(--radius-full);
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
}
</style>
