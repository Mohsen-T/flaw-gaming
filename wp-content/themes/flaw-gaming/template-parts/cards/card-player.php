<?php
/**
 * Player Card Template - Merciless-inspired clean design
 *
 * @package FLAW_Gaming
 */

$data = $args['data'] ?? [];

if (empty($data)) {
    return;
}

$status = flaw_pick_value($data['status'] ?? 'active', 'active');
$socials = !empty($data['socials']) ? array_filter($data['socials']) : [];
?>

<article class="card card--player card--roster card--<?php echo esc_attr($status); ?>">
    <a href="<?php echo esc_url($data['permalink']); ?>" class="card__link">

        <div class="card__avatar">
            <?php if (!empty($data['photo'])) : ?>
                <img src="<?php echo esc_url($data['photo']); ?>"
                     alt="<?php echo esc_attr($data['gamertag']); ?>"
                     class="card__avatar-img"
                     loading="lazy">
            <?php else : ?>
                <div class="card__avatar-placeholder">
                    <?php echo esc_html(strtoupper(substr($data['gamertag'], 0, 2))); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($data['gamertag']); ?></h3>

            <?php if (!empty($data['role'])) : ?>
                <p class="card__role-label"><?php echo esc_html($data['role']); ?></p>
            <?php endif; ?>
        </div>

    </a>

    <?php if (!empty($socials)) : ?>
        <div class="card__socials">
            <?php
            $social_order = ['twitter', 'twitch', 'youtube', 'tiktok', 'discord', 'instagram', 'blaze'];
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
