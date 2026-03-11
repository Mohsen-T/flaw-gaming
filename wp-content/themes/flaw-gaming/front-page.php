<?php
/**
 * Front Page Template
 * Inspired by flawgaming.com and mercilesshub.com design
 *
 * @package FLAW_Gaming
 */

get_header();

// Check if placeholder demo data should be shown for empty sections
$show_demo = function_exists('flaw_show_demo_placeholders') ? flaw_show_demo_placeholders() : true;
$is_admin_user = current_user_can('manage_options');
?>

<main id="main" class="site-main">
    <?php get_template_part('template-parts/sections/hero'); ?>

    <?php
    // Partner Marquee (like Merciless Hub)
    get_template_part('template-parts/sections/partner-marquee');
    ?>

    <?php
    // Live Events Section
    if (function_exists('flaw_get_live_events')) :
        $live_events = flaw_get_live_events();
        if ($live_events && $live_events->total()) :
    ?>
        <section class="section section--live-events" aria-labelledby="live-events-heading">
            <div class="container">
                <header class="section-header">
                    <h2 id="live-events-heading" class="section-title">
                        <span class="live-pulse"></span>
                        Live Now
                    </h2>
                </header>

                <div class="events-grid events-grid--live">
                    <?php
                    while ($live_events->fetch()) :
                        $data = flaw_get_event_card_data($live_events);
                        $data['stream'] = [
                            'platform' => flaw_pick_value($live_events->field('event_stream_platform')),
                            'channel'  => flaw_pick_value($live_events->field('event_stream_channel')),
                        ];
                        flaw_render_card('event', $data, ['variant' => 'live']);
                    endwhile;
                    ?>
                </div>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>

    <?php
    // Discord CTA Section (like Merciless Hub)
    get_template_part('template-parts/sections/discord-cta');
    ?>

    <?php
    // Vision/What We Offer Section (from original FLAW)
    ?>
    <section class="section section--vision" aria-labelledby="vision-heading">
        <div class="container">
            <header class="section-header section-header--center">
                <p class="section-tagline">Our Vision</p>
                <h2 id="vision-heading" class="section-title">What We Offer</h2>
            </header>

            <div class="vision-grid">
                <div class="vision-card">
                    <div class="vision-card__icon">🌐</div>
                    <h3 class="vision-card__title">Bridging Worlds</h3>
                    <p class="vision-card__text">Connecting Web2 and Web3 gaming communities through competitive play and collaboration.</p>
                </div>
                <div class="vision-card">
                    <div class="vision-card__icon">🎮</div>
                    <h3 class="vision-card__title">Game On</h3>
                    <p class="vision-card__text">Early access to emerging titles, beta testing opportunities, and exclusive game partnerships.</p>
                </div>
                <div class="vision-card">
                    <div class="vision-card__icon">🤝</div>
                    <h3 class="vision-card__title">Level Up Together</h3>
                    <p class="vision-card__text">Community-driven growth with mentorship, scrims, and collaborative learning.</p>
                </div>
                <div class="vision-card">
                    <div class="vision-card__icon">🏆</div>
                    <h3 class="vision-card__title">From Casual to Competitive</h3>
                    <p class="vision-card__text">Supporting players at every level, from first-time gamers to professional esports athletes.</p>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Achievements/Stats Section (like FLAW Gaming)
    get_template_part('template-parts/sections/achievements');
    ?>

    <?php
    // Featured Games Section
    $has_games = false;
    if (function_exists('flaw_get_featured_games')) :
        $games = flaw_get_featured_games(6);
        $has_games = $games && $games->total();
    endif;

    // Show section if has real data OR placeholders are enabled
    if ($has_games || $show_demo) :

    // Demo games data
    $demo_games = [
        ['title' => 'Off The Grid', 'genre' => 'Battle Royale', 'color' => '#D4A843'],
        ['title' => 'Valorant', 'genre' => 'Tactical FPS', 'color' => '#FF4655'],
        ['title' => 'Fortnite', 'genre' => 'Battle Royale', 'color' => '#9D4DFF'],
        ['title' => 'Apex Legends', 'genre' => 'Battle Royale', 'color' => '#DA292A'],
        ['title' => 'Rocket League', 'genre' => 'Sports', 'color' => '#0096FF'],
        ['title' => 'League of Legends', 'genre' => 'MOBA', 'color' => '#C89B3C'],
    ];
    ?>
    <section class="section section--games" aria-labelledby="games-heading">
        <div class="container">
            <header class="section-header">
                <div>
                    <p class="section-tagline">What We Play</p>
                    <h2 id="games-heading" class="section-title">Our Games</h2>
                </div>
                <a href="<?php echo esc_url(get_post_type_archive_link('game') ?: '#'); ?>" class="section-link">
                    View All Games &rarr;
                </a>
            </header>

            <div class="games-grid">
                <?php if ($has_games) : ?>
                    <?php while ($games->fetch()) : flaw_render_card_from_pod('game', $games); endwhile; ?>
                <?php else : ?>
                    <?php if ($is_admin_user) : ?><div class="demo-badge">Demo Data</div><?php endif; ?>
                    <?php foreach ($demo_games as $game) : ?>
                        <div class="card card--game">
                            <div class="card__image" style="background: linear-gradient(135deg, <?php echo esc_attr($game['color']); ?>22 0%, var(--color-bg-tertiary) 100%);">
                                <div class="card__game-icon" style="color: <?php echo esc_attr($game['color']); ?>;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4-3c-.83 0-1.5-.67-1.5-1.5S18.67 9 19.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                                </div>
                            </div>
                            <div class="card__content">
                                <span class="card__meta"><?php echo esc_html($game['genre']); ?></span>
                                <h3 class="card__title"><?php echo esc_html($game['title']); ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Teams Section
    $has_teams = false;
    if (function_exists('flaw_get_active_teams')) :
        $teams = flaw_get_active_teams();
        $has_teams = $teams && $teams->total();
    endif;

    if ($has_teams || $show_demo) :

    // Demo teams data
    $demo_teams = [
        ['name' => 'FLAW Alpha', 'game' => 'Off The Grid', 'players' => 5, 'status' => 'Active'],
        ['name' => 'FLAW Bravo', 'game' => 'Valorant', 'players' => 5, 'status' => 'Active'],
        ['name' => 'FLAW Charlie', 'game' => 'Fortnite', 'players' => 4, 'status' => 'Active'],
        ['name' => 'FLAW Delta', 'game' => 'Apex Legends', 'players' => 3, 'status' => 'Active'],
    ];
    ?>
    <section class="section section--teams" aria-labelledby="teams-heading">
        <div class="container">
            <header class="section-header">
                <div>
                    <p class="section-tagline">Meet the Squad</p>
                    <h2 id="teams-heading" class="section-title">Our Rosters</h2>
                </div>
                <a href="<?php echo esc_url(get_post_type_archive_link('team') ?: '#'); ?>" class="section-link">
                    View All Teams &rarr;
                </a>
            </header>

            <div class="teams-grid">
                <?php if ($has_teams) : ?>
                    <?php $count = 0; while ($teams->fetch() && $count < 6) : flaw_render_card_from_pod('team', $teams); $count++; endwhile; ?>
                <?php else : ?>
                    <?php if ($is_admin_user) : ?><div class="demo-badge">Demo Data</div><?php endif; ?>
                    <?php foreach ($demo_teams as $team) : ?>
                        <div class="card card--team">
                            <div class="card__image">
                                <div class="card__team-logo">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="color: var(--color-primary);"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            </div>
                            <div class="card__content">
                                <span class="card__meta"><?php echo esc_html($team['game']); ?></span>
                                <h3 class="card__title"><?php echo esc_html($team['name']); ?></h3>
                                <div class="card__footer">
                                    <span class="badge badge--success"><?php echo esc_html($team['status']); ?></span>
                                    <span class="card__stat"><?php echo esc_html($team['players']); ?> Players</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Upcoming Events Section
    $has_events = false;
    if (function_exists('flaw_get_upcoming_events')) :
        $upcoming = flaw_get_upcoming_events(4);
        $has_events = $upcoming && $upcoming->total();
    endif;

    if ($has_events || $show_demo) :

    // Demo events data
    $demo_events = [
        ['title' => 'FLAW Championship 2025', 'date' => 'Mar 15-17, 2025', 'game' => 'Off The Grid', 'prize' => '$10,000', 'status' => 'upcoming'],
        ['title' => 'Weekly Scrims', 'date' => 'Every Saturday', 'game' => 'Valorant', 'prize' => 'Practice', 'status' => 'upcoming'],
        ['title' => 'Community Tournament', 'date' => 'Feb 28, 2025', 'game' => 'Fortnite', 'prize' => '$500', 'status' => 'upcoming'],
        ['title' => 'Pro League Qualifier', 'date' => 'Mar 1, 2025', 'game' => 'Apex Legends', 'prize' => 'Qualification', 'status' => 'upcoming'],
    ];
    ?>
    <section class="section section--upcoming-events" aria-labelledby="upcoming-heading">
        <div class="container">
            <header class="section-header">
                <div>
                    <p class="section-tagline">Mark Your Calendar</p>
                    <h2 id="upcoming-heading" class="section-title">Upcoming Events</h2>
                </div>
                <a href="<?php echo esc_url(get_post_type_archive_link('event') ?: '#'); ?>" class="section-link">
                    View All Events &rarr;
                </a>
            </header>

            <div class="events-grid">
                <?php if ($has_events) : ?>
                    <?php while ($upcoming->fetch()) : flaw_render_card_from_pod('event', $upcoming); endwhile; ?>
                <?php else : ?>
                    <?php if ($is_admin_user) : ?><div class="demo-badge">Demo Data</div><?php endif; ?>
                    <?php foreach ($demo_events as $event) : ?>
                        <div class="card card--event">
                            <div class="card__image">
                                <span class="event-badge event-badge--<?php echo esc_attr($event['status']); ?>">Upcoming</span>
                            </div>
                            <div class="card__content">
                                <span class="card__meta"><?php echo esc_html($event['game']); ?></span>
                                <h3 class="card__title"><?php echo esc_html($event['title']); ?></h3>
                                <div class="card__details">
                                    <span class="card__date"><?php echo esc_html($event['date']); ?></span>
                                    <span class="card__prize"><?php echo esc_html($event['prize']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Featured Creators Section
    $has_creators = false;
    if (function_exists('flaw_get_featured_creators')) :
        $creators = flaw_get_featured_creators(4);
        $has_creators = $creators && $creators->total();
    endif;

    if ($has_creators || $show_demo) :

    // Demo creators data
    $demo_creators = [
        ['name' => 'StreamKing', 'platform' => 'Twitch', 'followers' => '125K', 'game' => 'Off The Grid'],
        ['name' => 'ProGamerGirl', 'platform' => 'YouTube', 'followers' => '89K', 'game' => 'Valorant'],
        ['name' => 'ESportsLegend', 'platform' => 'Twitch', 'followers' => '200K', 'game' => 'Fortnite'],
        ['name' => 'GamingWizard', 'platform' => 'TikTok', 'followers' => '1.2M', 'game' => 'Apex Legends'],
    ];
    ?>
    <section class="section section--creators" aria-labelledby="creators-heading">
        <div class="container">
            <header class="section-header">
                <div>
                    <p class="section-tagline">Content Creators</p>
                    <h2 id="creators-heading" class="section-title">FLAW Creators</h2>
                </div>
                <a href="<?php echo esc_url(get_post_type_archive_link('creator') ?: '#'); ?>" class="section-link">
                    View All Creators &rarr;
                </a>
            </header>

            <div class="creators-grid">
                <?php if ($has_creators) : ?>
                    <?php while ($creators->fetch()) : flaw_render_card_from_pod('creator', $creators); endwhile; ?>
                <?php else : ?>
                    <?php if ($is_admin_user) : ?><div class="demo-badge">Demo Data</div><?php endif; ?>
                    <?php foreach ($demo_creators as $creator) : ?>
                        <div class="card card--creator">
                            <div class="card__image">
                                <div class="card__avatar">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" style="color: var(--color-text-tertiary);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                                </div>
                            </div>
                            <div class="card__content">
                                <span class="card__meta"><?php echo esc_html($creator['platform']); ?></span>
                                <h3 class="card__title"><?php echo esc_html($creator['name']); ?></h3>
                                <div class="card__footer">
                                    <span class="card__followers"><?php echo esc_html($creator['followers']); ?> followers</span>
                                    <span class="card__game"><?php echo esc_html($creator['game']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Members Gallery (like FLAW Gaming)
    get_template_part('template-parts/sections/members');
    ?>

    <?php get_template_part('template-parts/sections/cta'); ?>
</main>

<?php if ($is_admin_user && $show_demo) : ?>
<!-- Admin-only: Demo data management bar -->
<div class="flaw-demo-admin-bar" id="flawDemoBar">
    <span>Placeholder demo data is active on empty sections.</span>
    <a href="<?php echo esc_url(admin_url('edit.php?post_type=event&page=flaw-demo-data')); ?>" class="flaw-demo-admin-bar__btn">Manage Demo Data</a>
</div>
<?php endif; ?>

<style>
/* Vision Section Styles */
.section-header--center {
    flex-direction: column;
    text-align: center;
}

.section-tagline {
    font-family: var(--font-display);
    font-size: var(--text-sm);
    font-weight: var(--font-bold);
    text-transform: uppercase;
    letter-spacing: var(--tracking-widest);
    color: var(--color-primary);
    margin-bottom: var(--space-2);
}

.vision-grid {
    display: grid;
    gap: var(--space-6);
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}

.vision-card {
    padding: var(--space-8);
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    text-align: center;
    transition: all var(--transition-normal);
}

.vision-card:hover {
    border-color: var(--color-primary);
    transform: translateY(-4px);
    box-shadow: 0 0 30px rgba(212, 168, 67, 0.1);
}

.vision-card__icon {
    font-size: 3rem;
    margin-bottom: var(--space-4);
}

.vision-card__title {
    font-family: var(--font-display);
    font-size: var(--text-lg);
    font-weight: var(--font-bold);
    text-transform: uppercase;
    margin: 0 0 var(--space-3);
}

.vision-card__text {
    color: var(--color-text-secondary);
    font-size: var(--text-sm);
    margin: 0;
    line-height: var(--leading-relaxed);
}

/* Section Header with tagline */
.section-header > div {
    display: flex;
    flex-direction: column;
}

/* Demo Card Styles */
.card {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition-normal);
    position: relative;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
    transform: scaleX(0);
    transition: transform var(--transition-normal);
    z-index: 10;
}

.card:hover {
    border-color: var(--color-primary);
    transform: translateY(-8px);
    box-shadow:
        0 20px 40px rgba(0, 0, 0, 0.3),
        0 0 30px rgba(212, 168, 67, 0.15);
}

.card:hover::before {
    transform: scaleX(1);
}

.card__image {
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, var(--color-bg-tertiary) 0%, var(--color-bg-elevated) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.card__image::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 30%, rgba(212, 168, 67, 0.1) 0%, transparent 50%);
    opacity: 0;
    transition: opacity var(--transition-normal);
}

.card:hover .card__image::after {
    opacity: 1;
}

.card--game .card__image {
    aspect-ratio: 4/3;
}

.card--creator .card__image {
    aspect-ratio: 1;
    background: radial-gradient(circle at center, var(--color-bg-elevated) 0%, var(--color-bg-tertiary) 100%);
}

.card__game-icon,
.card__team-logo,
.card__avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform var(--transition-normal);
}

.card:hover .card__game-icon,
.card:hover .card__team-logo,
.card:hover .card__avatar {
    transform: scale(1.1);
}

.card__content {
    padding: var(--space-4);
}

.card__meta {
    display: block;
    font-size: var(--text-xs);
    color: var(--color-primary);
    text-transform: uppercase;
    letter-spacing: var(--tracking-wide);
    font-weight: var(--font-semibold);
    margin-bottom: var(--space-1);
}

.card__title {
    font-family: var(--font-display);
    font-size: var(--text-lg);
    font-weight: var(--font-bold);
    margin: 0 0 var(--space-2);
    color: var(--color-text-primary);
}

.card__footer,
.card__details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: var(--space-3);
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
}

.card__date,
.card__prize,
.card__stat,
.card__followers,
.card__game {
    font-size: var(--text-sm);
    color: var(--color-text-tertiary);
}

.card__prize {
    color: var(--color-accent);
    font-weight: var(--font-semibold);
}

.badge {
    display: inline-block;
    padding: var(--space-1) var(--space-2);
    font-size: var(--text-xs);
    font-weight: var(--font-semibold);
    text-transform: uppercase;
    border-radius: var(--radius-sm);
}

.badge--success {
    background: var(--color-success);
    color: white;
}

.event-badge {
    position: absolute;
    top: var(--space-3);
    left: var(--space-3);
}

/* Demo data badge (admin-only) */
.demo-badge {
    grid-column: 1 / -1;
    text-align: center;
    padding: 6px 16px;
    background: rgba(212, 168, 67, 0.15);
    border: 1px dashed var(--color-primary);
    border-radius: var(--radius-sm);
    color: var(--color-primary);
    font-size: var(--text-xs);
    font-weight: var(--font-bold);
    text-transform: uppercase;
    letter-spacing: var(--tracking-wide);
}

/* Admin demo bar (fixed bottom) */
.flaw-demo-admin-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 10px 20px;
    background: rgba(30, 30, 30, 0.95);
    border-top: 2px solid var(--color-primary);
    backdrop-filter: blur(8px);
    font-size: 13px;
    color: #ccc;
}

.flaw-demo-admin-bar__btn {
    display: inline-block;
    padding: 6px 16px;
    background: var(--color-primary);
    color: #000 !important;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-radius: 4px;
    text-decoration: none;
    transition: opacity 0.2s;
}

.flaw-demo-admin-bar__btn:hover {
    opacity: 0.85;
}
</style>

<?php
get_footer();
