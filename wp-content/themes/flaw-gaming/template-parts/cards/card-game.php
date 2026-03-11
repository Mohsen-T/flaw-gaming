<?php
/**
 * Game Card Template
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];

if (empty($data)) {
    return;
}

$flaw_status = flaw_pick_value($data['flaw_status'] ?? 'active', 'active');
?>

<article class="card card--game card--<?php echo esc_attr($flaw_status); ?>">
    <a href="<?php echo esc_url($data['permalink']); ?>" class="card__link">

        <div class="card__media">
            <?php if (!empty($data['cover'])) : ?>
                <img src="<?php echo esc_url($data['cover']); ?>"
                     alt=""
                     class="card__cover"
                     loading="lazy">
            <?php elseif (!empty($data['logo'])) : ?>
                <div class="card__cover-fallback">
                    <img src="<?php echo esc_url($data['logo']); ?>"
                         alt=""
                         class="card__logo-centered"
                         loading="lazy">
                </div>
            <?php else : ?>
                <div class="card__cover-placeholder"></div>
            <?php endif; ?>

            <?php if (!empty($data['logo'])) : ?>
                <img src="<?php echo esc_url($data['logo']); ?>"
                     alt=""
                     class="card__game-logo"
                     loading="lazy">
            <?php endif; ?>

            <?php if ($flaw_status === 'recruiting') : ?>
                <span class="card__badge card__badge--recruiting">Recruiting</span>
            <?php endif; ?>
        </div>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($data['title']); ?></h3>

            <div class="card__meta">
                <?php if (!empty($data['genre'])) : ?>
                    <span class="card__genre"><?php echo esc_html($data['genre']); ?></span>
                <?php endif; ?>

                <?php if (!empty($data['stage'])) : ?>
                    <span class="card__stage"><?php echo esc_html(flaw_pick_value($data['stage'])); ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($data['platforms'])) : ?>
                <div class="card__platforms">
                    <?php
                    $platforms = is_array($data['platforms']) ? $data['platforms'] : [$data['platforms']];
                    foreach (array_slice($platforms, 0, 3) as $platform) :
                    ?>
                        <span class="card__platform"><?php echo esc_html($platform); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['blockchain'])) : ?>
                <span class="card__blockchain">
                    <?php echo esc_html(flaw_pick_value($data['blockchain'])); ?>
                </span>
            <?php endif; ?>
        </div>

    </a>
</article>
