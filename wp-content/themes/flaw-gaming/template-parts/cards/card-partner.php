<?php
/**
 * Partner Card Template
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];

if (empty($data)) {
    return;
}

$tier = flaw_pick_value($data['tier'] ?? 'bronze', 'bronze');
$has_promo = !empty($data['promo']['code']);
?>

<article class="card card--partner card--tier-<?php echo esc_attr($tier); ?>">
    <a href="<?php echo esc_url($data['website'] ?? $data['permalink']); ?>"
       class="card__link"
       <?php echo !empty($data['website']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>

        <div class="card__media card__media--logo">
            <?php if (!empty($data['logo'])) : ?>
                <img src="<?php echo esc_url($data['logo']); ?>"
                     alt="<?php echo esc_attr($data['title']); ?>"
                     class="card__logo"
                     loading="lazy">
            <?php else : ?>
                <span class="card__logo-text"><?php echo esc_html($data['title']); ?></span>
            <?php endif; ?>
        </div>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($data['title']); ?></h3>

            <div class="card__meta">
                <?php if (!empty($data['type'])) : ?>
                    <span class="card__type"><?php echo esc_html(flaw_pick_value($data['type'])); ?></span>
                <?php endif; ?>

                <span class="card__tier card__tier--<?php echo esc_attr($tier); ?>">
                    <?php echo esc_html(ucfirst($tier)); ?> Partner
                </span>
            </div>

            <?php if (!empty($data['description'])) : ?>
                <p class="card__description">
                    <?php echo esc_html(wp_trim_words($data['description'], 15)); ?>
                </p>
            <?php endif; ?>
        </div>

    </a>

    <?php if ($has_promo) : ?>
        <div class="card__promo" onclick="event.stopPropagation();">
            <?php if (!empty($data['promo']['url'])) : ?>
                <a href="<?php echo esc_url($data['promo']['url']); ?>"
                   class="promo-code"
                   target="_blank"
                   rel="noopener noreferrer">
            <?php else : ?>
                <div class="promo-code">
            <?php endif; ?>
                <span class="promo-code__label">Use code</span>
                <span class="promo-code__value"><?php echo esc_html($data['promo']['code']); ?></span>
            <?php echo !empty($data['promo']['url']) ? '</a>' : '</div>'; ?>
        </div>
    <?php endif; ?>
</article>
