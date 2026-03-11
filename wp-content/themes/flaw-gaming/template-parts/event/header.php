<?php
/**
 * Event Header Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;
$state = $args['state'] ?? 'upcoming';
$event_id = $args['event_id'] ?? get_the_ID();

if (!$pod) {
    return;
}
?>

<header class="event-header event-header--<?php echo esc_attr($state); ?>">
    <?php if (has_post_thumbnail($event_id)) : ?>
        <div class="event-header__background">
            <?php echo get_the_post_thumbnail($event_id, 'hero-background', ['class' => 'event-header__bg-image']); ?>
            <div class="event-header__overlay"></div>
        </div>
    <?php endif; ?>

    <div class="event-header__content container">
        <?php
        $game_logo = flaw_pick_value($pod->field('event_game.game_logo._src'));
        $game_title = flaw_pick_value($pod->field('event_game.post_title'));
        ?>

        <?php if ($game_logo) : ?>
            <img src="<?php echo esc_url($game_logo); ?>"
                 alt="<?php echo esc_attr($game_title); ?>"
                 class="event-header__game-logo">
        <?php endif; ?>

        <div class="event-header__badge">
            <?php echo FLAW_Event_State_Manager::get_state_badge($state); ?>
        </div>

        <h1 class="event-header__title"><?php echo get_the_title($event_id); ?></h1>

        <div class="event-header__meta">
            <?php
            $start = flaw_pick_value($pod->field('event_date_start'));
            $end = flaw_pick_value($pod->field('event_date_end'));
            $format = flaw_pick_value($pod->field('event_format'));
            $type = function_exists('flaw_get_event_type') ? flaw_get_event_type(get_the_ID()) : '';
            ?>

            <?php if ($start) : ?>
                <time class="event-header__date" datetime="<?php echo esc_attr($start); ?>">
                    <?php echo esc_html(flaw_format_date_range($start, $end)); ?>
                </time>
            <?php endif; ?>

            <?php if ($format) : ?>
                <span class="event-header__format"><?php echo esc_html($format); ?></span>
            <?php endif; ?>

            <?php if ($type) : ?>
                <span class="event-header__type"><?php echo esc_html($type); ?></span>
            <?php endif; ?>
        </div>

        <?php if ($state === 'upcoming' && $start) : ?>
            <div class="event-header__countdown">
                <?php flaw_the_countdown($start); ?>
            </div>
        <?php endif; ?>
    </div>
</header>
