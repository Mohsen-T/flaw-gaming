<?php
/**
 * Live Event Card Template
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];

if (empty($data)) {
    return;
}

$stream = $data['stream'] ?? [];
$channel = $stream['channel'] ?? '';
?>

<article class="card card--event card--live">
    <div class="card__live-indicator">
        <span class="live-pulse"></span>
        <span class="live-text">LIVE</span>
    </div>

    <div class="card__media">
        <?php if ($channel) : ?>
            <div class="card__stream-preview"
                 data-twitch-channel="<?php echo esc_attr($channel); ?>"
                 data-twitch-preview="true">
                <?php if (!empty($data['thumbnail'])) : ?>
                    <img src="<?php echo esc_url($data['thumbnail']); ?>"
                         alt=""
                         class="card__image card__image--fallback"
                         loading="lazy">
                <?php endif; ?>
            </div>
        <?php elseif (!empty($data['thumbnail'])) : ?>
            <img src="<?php echo esc_url($data['thumbnail']); ?>"
                 alt=""
                 class="card__image"
                 loading="lazy">
        <?php endif; ?>

        <?php if (!empty($data['game']['logo'])) : ?>
            <img src="<?php echo esc_url($data['game']['logo']); ?>"
                 alt="<?php echo esc_attr($data['game']['title']); ?>"
                 class="card__game-badge"
                 loading="lazy">
        <?php endif; ?>
    </div>

    <div class="card__content">
        <h3 class="card__title">
            <a href="<?php echo esc_url($data['permalink']); ?>">
                <?php echo esc_html($data['title']); ?>
            </a>
        </h3>

        <?php if (!empty($data['game']['title'])) : ?>
            <span class="card__game-name"><?php echo esc_html($data['game']['title']); ?></span>
        <?php endif; ?>

        <?php if ($channel) : ?>
            <div class="card__stream-info" data-twitch-channel="<?php echo esc_attr($channel); ?>">
                <span class="card__viewers">
                    <span class="viewers-count">--</span> viewers
                </span>
            </div>
        <?php endif; ?>

        <div class="card__actions">
            <a href="<?php echo esc_url($data['permalink']); ?>" class="btn btn--primary btn--sm">
                Watch Now
            </a>
        </div>
    </div>
</article>
