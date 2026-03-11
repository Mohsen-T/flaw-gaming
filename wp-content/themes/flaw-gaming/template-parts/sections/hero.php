<?php
/**
 * Hero Section Template
 * With particle effects & entrance animations
 *
 * @package FLAW_Gaming
 */

$hero_tagline = get_theme_mod('flaw_hero_tagline', 'Web3 Gaming Excellence');
$hero_title = get_theme_mod('flaw_hero_title', '<span>FLAW</span> Gaming');
$hero_subtitle = get_theme_mod('flaw_hero_subtitle', 'A global gaming community focused on mastering Web3 games, creating engaging content, and building the next generation of competitive esports athletes.');
$hero_cta_text = get_theme_mod('flaw_hero_cta_text', 'Join Our Discord');
$hero_cta_url = get_theme_mod('flaw_hero_cta_url', 'https://discord.gg/flawgaminghq');
$hero_secondary_text = get_theme_mod('flaw_hero_secondary_text', 'View Roster');
$hero_secondary_url = get_theme_mod('flaw_hero_secondary_url', get_post_type_archive_link('team') ?: home_url('/teams'));
$hero_bg = get_theme_mod('flaw_hero_background', '');
?>

<section class="hero" aria-labelledby="hero-heading">
    <div class="hero__background">
        <?php if ($hero_bg) : ?>
            <img src="<?php echo esc_url($hero_bg); ?>" alt="" loading="eager">
        <?php endif; ?>
        <div class="hero__gradient"></div>
    </div>

    <!-- Particle canvas -->
    <canvas class="hero__particles" id="hero-particles" aria-hidden="true"></canvas>

    <div class="hero__content container">
        <?php if ($hero_tagline) : ?>
            <p class="hero__tagline hero-anim" data-anim-delay="0"><?php echo esc_html($hero_tagline); ?></p>
        <?php endif; ?>

        <h1 id="hero-heading" class="hero__title hero-anim hero-glitch" data-anim-delay="200">
            <?php echo wp_kses($hero_title, ['span' => [], 'strong' => [], 'br' => []]); ?>
        </h1>

        <p class="hero__subtitle hero-anim" data-anim-delay="500">
            <?php echo esc_html($hero_subtitle); ?>
        </p>

        <div class="hero__actions hero-anim" data-anim-delay="700">
            <a href="<?php echo esc_url($hero_cta_url); ?>" class="btn btn--discord btn--lg" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html($hero_cta_text); ?>
            </a>

            <a href="<?php echo esc_url($hero_secondary_url); ?>" class="btn btn--outline btn--lg">
                <?php echo esc_html($hero_secondary_text); ?>
            </a>
        </div>

        <?php
        // Social links below hero
        $socials = [
            'discord'   => get_theme_mod('flaw_social_discord', 'https://discord.gg/flawgaminghq'),
            'twitter'   => get_theme_mod('flaw_social_twitter', 'https://x.com/FlawgamingHQ'),
            'youtube'   => get_theme_mod('flaw_social_youtube', 'https://www.youtube.com/@FlawGaming_HQ'),
            'twitch'    => get_theme_mod('flaw_social_twitch', 'https://www.twitch.tv/flawgaminghq'),
            'tiktok'    => get_theme_mod('flaw_social_tiktok', 'https://www.tiktok.com/@flaw.gaming'),
        ];
        $socials = array_filter($socials);
        if (!empty($socials)) :
        ?>
        <div class="hero__socials hero-anim" data-anim-delay="900">
            <?php foreach ($socials as $platform => $url) : ?>
                <a href="<?php echo esc_url($url); ?>" class="hero__social-link hero__social-link--<?php echo esc_attr($platform); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
                    <?php echo flaw_get_social_icon($platform); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        // Show live event indicator if any events are live
        if (function_exists('flaw_get_live_events')) :
            $live = flaw_get_live_events();
            if ($live && $live->total()) :
                $live->fetch();
        ?>
            <div class="hero__live-event hero-anim" data-anim-delay="1100">
                <a href="<?php echo esc_url(get_permalink($live->field('ID'))); ?>" class="live-event-banner">
                    <span class="live-pulse"></span>
                    <span class="live-event-banner__text">
                        <strong>LIVE NOW:</strong>
                        <?php echo esc_html($live->field('post_title')); ?>
                    </span>
                    <span class="live-event-banner__cta">Watch &rarr;</span>
                </a>
            </div>
        <?php
            endif;
        endif;
        ?>
    </div>

    <div class="hero__scroll-indicator hero-anim" data-anim-delay="1300" aria-hidden="true">
        <span class="scroll-indicator"></span>
    </div>
</section>
