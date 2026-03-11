<?php
/**
 * Event Card Template
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];

if (empty($data)) {
    return;
}

$status = flaw_pick_value($data['status'] ?? 'upcoming', 'upcoming');
$has_countdown = $status === 'upcoming' && !empty($data['date_start']);
?>

<article class="card card--event card--<?php echo esc_attr($status); ?>">
    <a href="<?php echo esc_url($data['permalink']); ?>" class="card__link">

        <div class="card__media">
            <?php if (!empty($data['thumbnail'])) : ?>
                <img src="<?php echo esc_url($data['thumbnail']); ?>"
                     alt=""
                     class="card__image"
                     loading="lazy">
            <?php else : ?>
                <div class="card__image-placeholder"></div>
            <?php endif; ?>

            <?php if (!empty($data['game']['logo'])) : ?>
                <img src="<?php echo esc_url($data['game']['logo']); ?>"
                     alt="<?php echo esc_attr($data['game']['title']); ?>"
                     class="card__game-badge"
                     loading="lazy">
            <?php endif; ?>

            <span class="card__status card__status--<?php echo esc_attr($status); ?>">
                <?php echo esc_html(ucfirst($status)); ?>
            </span>
        </div>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($data['title']); ?></h3>

            <div class="card__meta">
                <?php if (!empty($data['date_start'])) : ?>
                    <time class="card__date" datetime="<?php echo esc_attr($data['date_start']); ?>">
                        <?php
                        if (function_exists('flaw_format_date_range')) {
                            echo esc_html(flaw_format_date_range($data['date_start'], $data['date_end'] ?? null));
                        } else {
                            echo esc_html(date('M j, Y', strtotime($data['date_start'])));
                        }
                        ?>
                    </time>
                <?php endif; ?>

                <?php if (!empty($data['format'])) : ?>
                    <span class="card__format"><?php echo esc_html($data['format']); ?></span>
                <?php endif; ?>
            </div>

            <?php if ($has_countdown) : ?>
                <div class="card__countdown" <?php echo flaw_countdown_attrs($data['date_start']); ?>>
                    <span class="card__countdown-value" data-unit="days">--</span>d
                    <span class="card__countdown-value" data-unit="hours">--</span>h
                    <span class="card__countdown-value" data-unit="minutes">--</span>m
                </div>
            <?php endif; ?>

            <?php if ($status === 'completed' && !empty($data['results']['placement'])) : ?>
                <div class="card__results">
                    <?php flaw_the_placement($data['results']['placement']); ?>
                    <?php if (!empty($data['results']['prize_won'])) : ?>
                        <span class="card__prize"><?php echo esc_html($data['results']['prize_won']); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['registration']['enabled']) && $status === 'upcoming') : ?>
                <span class="card__cta">Register Now</span>
            <?php endif; ?>
        </div>

    </a>
</article>
