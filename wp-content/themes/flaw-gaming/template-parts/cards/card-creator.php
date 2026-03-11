<?php
/**
 * Creator Card Template - Merciless-inspired clean design
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];
$variant = $args['variant'] ?? 'default';

if (empty($data)) {
    return;
}

$handle = $data['handle'] ?? $data['title'] ?? '';
$photo = $data['photo'] ?? $data['thumbnail'] ?? '';
$socials = !empty($data['socials']) ? array_filter($data['socials']) : [];
?>

<article class="card card--creator card--roster <?php echo $variant === 'featured' ? 'card--featured' : ''; ?>">
    <a href="<?php echo esc_url($data['permalink']); ?>" class="card__link">

        <div class="card__avatar">
            <?php if (!empty($photo)) : ?>
                <img src="<?php echo esc_url($photo); ?>"
                     alt="<?php echo esc_attr($handle); ?>"
                     class="card__avatar-img"
                     loading="lazy">
            <?php else : ?>
                <div class="card__avatar-placeholder">
                    <?php echo esc_html(strtoupper(substr($handle, 0, 2))); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['featured'])) : ?>
                <span class="card__badge card__badge--featured">Featured</span>
            <?php endif; ?>
        </div>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($handle); ?></h3>

            <?php if (!empty($data['specialty'])) : ?>
                <p class="card__role-label">
                    <?php
                    $specialties = is_array($data['specialty']) ? $data['specialty'] : [$data['specialty']];
                    echo esc_html(implode(' / ', $specialties));
                    ?>
                </p>
            <?php endif; ?>
        </div>

    </a>

    <?php if (!empty($socials)) : ?>
        <div class="card__socials">
            <?php
            $social_order = ['twitch', 'youtube', 'twitter', 'tiktok', 'instagram', 'discord', 'blaze'];
            foreach ($social_order as $platform) :
                if (!empty($socials[$platform])) :
            ?>
                <a href="<?php echo esc_url($socials[$platform]); ?>"
                   class="social-link social-link--<?php echo esc_attr($platform); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   onclick="event.stopPropagation();"
                   aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
                    <?php echo function_exists('flaw_get_social_icon') ? flaw_get_social_icon($platform) : ''; ?>
                </a>
            <?php
                endif;
            endforeach;
            ?>
        </div>
    <?php endif; ?>
</article>
