<?php
/**
 * Event Details Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$description = flaw_pick_value($pod->field('event_description'));
$rules = flaw_pick_value($pod->field('event_rules'));
$format = flaw_pick_value($pod->field('event_format'));
$location = flaw_pick_value($pod->field('event_location'));
$organizer = flaw_pick_value($pod->field('event_organizer'));
?>

<section class="event-section event-details" aria-labelledby="details-heading">
    <header class="event-section__header">
        <h2 id="details-heading" class="event-section__title">Event Details</h2>
    </header>

    <div class="event-section__content">
        <?php if ($description) : ?>
            <div class="event-description prose">
                <?php echo wp_kses_post($description); ?>
            </div>
        <?php endif; ?>

        <dl class="event-info-list">
            <?php if ($format) : ?>
                <div class="event-info-item">
                    <dt>Format</dt>
                    <dd><?php echo esc_html($format); ?></dd>
                </div>
            <?php endif; ?>

            <?php if ($location) : ?>
                <div class="event-info-item">
                    <dt>Location</dt>
                    <dd><?php echo esc_html($location); ?></dd>
                </div>
            <?php endif; ?>

            <?php if ($organizer) : ?>
                <div class="event-info-item">
                    <dt>Organizer</dt>
                    <dd><?php echo esc_html($organizer); ?></dd>
                </div>
            <?php endif; ?>

            <?php
            $prize_pool = flaw_pick_value($pod->field('event_prize_pool_total'));
            if ($prize_pool) :
            ?>
                <div class="event-info-item">
                    <dt>Prize Pool</dt>
                    <dd><?php echo esc_html($prize_pool); ?></dd>
                </div>
            <?php endif; ?>
        </dl>

        <?php if ($rules) : ?>
            <details class="event-rules">
                <summary>View Rules</summary>
                <div class="event-rules__content prose">
                    <?php echo wp_kses_post($rules); ?>
                </div>
            </details>
        <?php endif; ?>
    </div>
</section>
