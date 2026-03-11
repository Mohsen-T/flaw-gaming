<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main">
        <?php esc_html_e('Skip to content', 'flaw-gaming'); ?>
    </a>

    <header id="masthead" class="site-header">
        <div class="header-inner container">
            <div class="site-branding">
                <?php flaw_site_logo(); ?>
            </div>

            <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Primary Menu', 'flaw-gaming'); ?>">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                    <span class="menu-toggle__bar"></span>
                    <span class="screen-reader-text"><?php esc_html_e('Menu', 'flaw-gaming'); ?></span>
                </button>

                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'nav-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

            <div class="header-actions">
                <?php if (function_exists('flaw_get_live_events')) :
                    $live = flaw_get_live_events();
                    if ($live && $live->total()) : ?>
                        <a href="<?php echo esc_url(get_post_type_archive_link('event')); ?>" class="live-indicator">
                            <span class="live-indicator__dot"></span>
                            <span class="live-indicator__text">LIVE</span>
                        </a>
                    <?php endif;
                endif; ?>

                <a href="<?php echo esc_url(home_url('/join')); ?>" class="btn btn--primary btn--sm">
                    Join FLAW
                </a>
            </div>
        </div>
    </header>
