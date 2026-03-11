<?php
/**
 * Event Statistics Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$matches_played = (int) flaw_pick_value($pod->field('event_stats_matches_played'));
$matches_won = (int) flaw_pick_value($pod->field('event_stats_matches_won'));
$maps_played = (int) flaw_pick_value($pod->field('event_stats_maps_played'));
$maps_won = (int) flaw_pick_value($pod->field('event_stats_maps_won'));
$highlights = flaw_pick_value($pod->field('event_stats_highlights'));

// Resolve MVP player
$mvp_raw = $pod->field('event_stats_mvp');
$mvp = null;
if (!empty($mvp_raw)) {
    $mvp_id = is_array($mvp_raw) ? ($mvp_raw['ID'] ?? 0) : $mvp_raw;
    if ($mvp_id) {
        $mvp_pod = pods('player', $mvp_id);
        if ($mvp_pod->exists()) {
            $mvp = [
                'gamertag' => flaw_pick_value($mvp_pod->field('player_gamertag')) ?: flaw_pick_value($mvp_pod->field('post_title')),
            ];
        }
    }
}

if (empty($matches_played) && empty($pod->field('event_stats_json'))) {
    return;
}
?>

<section class="event-section event-statistics" aria-labelledby="stats-heading">
    <header class="event-section__header">
        <h2 id="stats-heading" class="event-section__title">Statistics</h2>
    </header>

    <div class="event-section__content">
        <div class="stats-grid">
            <?php if (!empty($matches_played)) : ?>
                <div class="stat-card">
                    <span class="stat-card__value"><?php echo esc_html($matches_played); ?></span>
                    <span class="stat-card__label">Matches Played</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($matches_won)) : ?>
                <div class="stat-card">
                    <span class="stat-card__value"><?php echo esc_html($matches_won); ?></span>
                    <span class="stat-card__label">Matches Won</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($maps_played)) : ?>
                <div class="stat-card">
                    <span class="stat-card__value"><?php echo esc_html($maps_played); ?></span>
                    <span class="stat-card__label">Maps Played</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($maps_won)) : ?>
                <div class="stat-card">
                    <span class="stat-card__value"><?php echo esc_html($maps_won); ?></span>
                    <span class="stat-card__label">Maps Won</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($matches_played) && !empty($matches_won)) : ?>
                <div class="stat-card stat-card--highlight">
                    <?php $winrate = round(($matches_won / $matches_played) * 100); ?>
                    <span class="stat-card__value"><?php echo esc_html($winrate); ?>%</span>
                    <span class="stat-card__label">Win Rate</span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($mvp)) : ?>
            <div class="stats-mvp">
                <span class="stats-mvp__label">Event MVP</span>
                <span class="stats-mvp__player"><?php echo esc_html($mvp['gamertag']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($highlights)) : ?>
            <div class="stats-highlights">
                <h3>Highlights</h3>
                <div class="prose">
                    <?php echo wp_kses_post($highlights); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
