<?php
/**
 * Event Teams Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$teams = $pod->field('event_teams_participating');

if (empty($teams)) {
    return;
}
?>

<section class="event-section event-teams" aria-labelledby="teams-heading">
    <header class="event-section__header">
        <h3 id="teams-heading" class="event-section__title">Participating Teams</h3>
    </header>

    <div class="event-section__content">
        <ul class="teams-list">
            <?php foreach ($teams as $team) : ?>
                <?php
                $team_id = is_array($team) ? ($team['ID'] ?? 0) : $team;
                if (!$team_id) continue;

                $team_pod = pods('team', $team_id);
                if (!$team_pod->exists()) continue;

                $logo = flaw_get_image_url($team_pod, 'team_logo');
                $title = flaw_pick_value($team_pod->field('post_title'));
                ?>
                <li class="teams-list__item">
                    <a href="<?php echo esc_url(get_permalink($team_id)); ?>" class="team-link">
                        <?php if ($logo) : ?>
                            <img src="<?php echo esc_url($logo); ?>"
                                 alt=""
                                 class="team-link__logo"
                                 loading="lazy">
                        <?php endif; ?>
                        <span class="team-link__name"><?php echo esc_html($title); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
