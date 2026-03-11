<?php
/**
 * Event Results Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$placement = (int) flaw_pick_value($pod->field('event_placement_org'));
$winner_team_raw = $pod->field('event_winner_team');
$prize_won = flaw_pick_value($pod->field('event_prize_won'));
$prize_token = flaw_pick_value($pod->field('event_prize_token'));
$bracket_url = flaw_pick_value($pod->field('event_final_bracket_url'));
$recap = flaw_pick_value($pod->field('event_recap_content'));

// Resolve winner team data
$winner_team = null;
if (!empty($winner_team_raw)) {
    $winner_id = is_array($winner_team_raw) ? ($winner_team_raw['ID'] ?? 0) : $winner_team_raw;
    if ($winner_id) {
        $winner_pod = pods('team', $winner_id);
        if ($winner_pod->exists()) {
            $winner_team = [
                'title' => flaw_pick_value($winner_pod->field('post_title')),
                'logo'  => flaw_get_image_url($winner_pod, 'team_logo'),
            ];
        }
    }
}

if (empty($winner_team) && empty($placement)) {
    return;
}
?>

<section class="event-section event-results" aria-labelledby="results-heading">
    <header class="event-section__header">
        <h2 id="results-heading" class="event-section__title">Results</h2>
    </header>

    <div class="event-section__content">
        <?php if (!empty($placement)) : ?>
            <div class="results-placement">
                <span class="results-placement__label">FLAW Placement</span>
                <span class="results-placement__value <?php echo $placement <= 3 ? 'results-placement__value--top' : ''; ?>">
                    <?php flaw_the_placement($placement); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (!empty($winner_team)) : ?>
            <div class="results-winner">
                <span class="results-winner__label">Champion</span>
                <div class="results-winner__team">
                    <?php if (!empty($winner_team['logo'])) : ?>
                        <img src="<?php echo esc_url($winner_team['logo']); ?>"
                             alt=""
                             class="results-winner__logo">
                    <?php endif; ?>
                    <span class="results-winner__name"><?php echo esc_html($winner_team['title']); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($prize_won)) : ?>
            <div class="results-prize">
                <span class="results-prize__label">Prize Won</span>
                <span class="results-prize__value">
                    <?php echo esc_html($prize_won); ?>
                    <?php if (!empty($prize_token)) : ?>
                        <span class="results-prize__token"><?php echo esc_html($prize_token); ?></span>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (!empty($bracket_url)) : ?>
            <div class="results-bracket">
                <a href="<?php echo esc_url($bracket_url); ?>"
                   class="btn btn--outline"
                   target="_blank"
                   rel="noopener noreferrer">
                    View Final Bracket
                </a>
            </div>
        <?php endif; ?>

        <?php if (!empty($recap)) : ?>
            <div class="results-recap prose">
                <h3>Event Recap</h3>
                <?php echo wp_kses_post($recap); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
