<?php
/**
 * Team Card Template
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];
$show_players = $args['show_players'] ?? false;

if (empty($data)) {
    return;
}

$status = flaw_pick_value($data['status'] ?? 'active', 'active');
$logo = $data['logo'] ?? $data['thumbnail'] ?? '';
?>

<article class="card card--team card--<?php echo esc_attr($status); ?>">
    <a href="<?php echo esc_url($data['permalink']); ?>" class="card__link">

        <div class="card__media card__media--logo">
            <?php if (!empty($logo)) : ?>
                <img src="<?php echo esc_url($logo); ?>"
                     alt="<?php echo esc_attr($data['title']); ?> logo"
                     class="card__logo"
                     loading="lazy">
            <?php else : ?>
                <div class="card__logo-placeholder">
                    <?php echo esc_html(substr($data['title'], 0, 2)); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($data['title']); ?></h3>

            <div class="card__meta">
                <?php if (!empty($data['game']['title'])) : ?>
                    <span class="card__game">
                        <?php if (!empty($data['game']['logo'])) : ?>
                            <img src="<?php echo esc_url($data['game']['logo']); ?>"
                                 alt=""
                                 class="card__game-icon"
                                 loading="lazy">
                        <?php endif; ?>
                        <?php echo esc_html($data['game']['title']); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($data['region'])) : ?>
                    <span class="card__region"><?php echo esc_html(flaw_pick_value($data['region'])); ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($data['player_count'])) : ?>
                <span class="card__players">
                    <?php echo esc_html($data['player_count']); ?> players
                </span>
            <?php endif; ?>

            <?php if ($status !== 'active') : ?>
                <span class="card__status-badge card__status-badge--<?php echo esc_attr($status); ?>">
                    <?php echo esc_html(ucfirst($status)); ?>
                </span>
            <?php endif; ?>
        </div>

    </a>

    <?php if (!empty($data['socials'])) : ?>
        <div class="card__socials">
            <?php foreach (array_filter($data['socials']) as $platform => $url) : ?>
                <a href="<?php echo esc_url($url); ?>"
                   class="social-link social-link--<?php echo esc_attr($platform); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   onclick="event.stopPropagation();">
                    <span class="screen-reader-text"><?php echo esc_html(ucfirst($platform)); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>
