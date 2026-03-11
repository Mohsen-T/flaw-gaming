<?php
/**
 * Achievements/Stats Section Template
 * Editable via Customizer: Appearance > Customize > FLAW Gaming Options > Statistics
 *
 * @package FLAW_Gaming
 */

// SVG icons for each stat type
$stat_icons = [
    'members' => '<svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
    'trophy'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>',
    'events'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/></svg>',
    'games'   => '<svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4-3c-.83 0-1.5-.67-1.5-1.5S18.67 9 19.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>',
];

$stat_colors = [
    'primary'   => 'var(--color-primary)',
    'gold'      => 'var(--color-gold)',
    'secondary' => 'var(--color-secondary)',
    'accent'    => 'var(--color-accent)',
];

// Get section header from customizer
$section_tagline = get_theme_mod('flaw_stats_tagline', 'Our Impact');
$section_title = get_theme_mod('flaw_stats_title', 'By The Numbers');

// Get stats from customizer (editable in admin)
$achievements = [
    [
        'value' => get_theme_mod('flaw_stat_1_value', '50+'),
        'label' => get_theme_mod('flaw_stat_1_label', 'Active Members'),
        'icon'  => $stat_icons[get_theme_mod('flaw_stat_1_icon', 'members')],
        'color' => $stat_colors[get_theme_mod('flaw_stat_1_color', 'primary')],
    ],
    [
        'value' => get_theme_mod('flaw_stat_2_value', '#1'),
        'label' => get_theme_mod('flaw_stat_2_label', 'World Record Kills (OTG)'),
        'icon'  => $stat_icons[get_theme_mod('flaw_stat_2_icon', 'trophy')],
        'color' => $stat_colors[get_theme_mod('flaw_stat_2_color', 'gold')],
    ],
    [
        'value' => get_theme_mod('flaw_stat_3_value', '100+'),
        'label' => get_theme_mod('flaw_stat_3_label', 'Events Competed'),
        'icon'  => $stat_icons[get_theme_mod('flaw_stat_3_icon', 'events')],
        'color' => $stat_colors[get_theme_mod('flaw_stat_3_color', 'secondary')],
    ],
    [
        'value' => get_theme_mod('flaw_stat_4_value', '6+'),
        'label' => get_theme_mod('flaw_stat_4_label', 'Games We Play'),
        'icon'  => $stat_icons[get_theme_mod('flaw_stat_4_icon', 'games')],
        'color' => $stat_colors[get_theme_mod('flaw_stat_4_color', 'accent')],
    ],
];

// Filter out empty stats
$achievements = array_filter($achievements, function($stat) {
    return !empty($stat['value']) && !empty($stat['label']);
});
?>

<section class="achievements-section" aria-labelledby="achievements-heading">
    <div class="container">
        <header class="section-header section-header--center">
            <p class="section-tagline"><?php echo esc_html($section_tagline); ?></p>
            <h2 id="achievements-heading" class="section-title"><?php echo esc_html($section_title); ?></h2>
        </header>

        <div class="achievements-grid">
            <?php foreach ($achievements as $achievement) : ?>
                <div class="achievement-card" style="--accent-color: <?php echo esc_attr($achievement['color']); ?>">
                    <div class="achievement-card__icon">
                        <?php echo $achievement['icon']; ?>
                    </div>
                    <div class="achievement-card__value" data-count="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $achievement['value'])); ?>">
                        <?php echo esc_html($achievement['value']); ?>
                    </div>
                    <div class="achievement-card__label">
                        <?php echo esc_html($achievement['label']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.achievements-section {
    padding: var(--space-20) 0;
    background: linear-gradient(180deg, var(--color-bg-primary) 0%, var(--color-bg-secondary) 100%);
    position: relative;
    overflow: hidden;
}

.achievements-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--color-primary), transparent);
}

.achievements-grid {
    display: grid;
    gap: var(--space-6);
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

.achievement-card {
    text-align: center;
    padding: var(--space-8);
    background: var(--color-bg-tertiary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.achievement-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-color);
    transform: scaleX(0);
    transition: transform var(--transition-normal);
}

.achievement-card:hover {
    border-color: var(--accent-color);
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.achievement-card:hover::before {
    transform: scaleX(1);
}

.achievement-card__icon {
    color: var(--accent-color);
    margin-bottom: var(--space-4);
    filter: drop-shadow(0 0 10px var(--accent-color));
}

.achievement-card__value {
    font-family: var(--font-display);
    font-size: var(--text-5xl);
    font-weight: var(--font-extrabold);
    color: var(--color-text-primary);
    line-height: 1;
    margin-bottom: var(--space-2);
    background: linear-gradient(135deg, var(--color-text-primary) 0%, var(--accent-color) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.achievement-card__label {
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
    text-transform: uppercase;
    letter-spacing: var(--tracking-wide);
}
</style>
