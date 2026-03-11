    <footer id="colophon" class="site-footer">
        <div class="footer-main container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <?php flaw_site_logo(); ?>
                    <p class="footer-tagline">
                        <?php echo esc_html(get_bloginfo('description')); ?>
                    </p>

                    <?php if (has_nav_menu('social')) : ?>
                        <nav class="social-links" aria-label="<?php esc_attr_e('Social Links', 'flaw-gaming'); ?>">
                            <?php
                            wp_nav_menu([
                                'theme_location' => 'social',
                                'menu_class'     => 'social-menu',
                                'container'      => false,
                                'depth'          => 1,
                                'link_before'    => '<span class="screen-reader-text">',
                                'link_after'     => '</span>',
                            ]);
                            ?>
                        </nav>
                    <?php endif; ?>
                </div>

                <div class="footer-widgets">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-1'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-2'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer Menu', 'flaw-gaming'); ?>">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                        ]);
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p class="copyright">
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
                </p>
                <p class="credits">
                    <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>
                    <span class="separator">|</span>
                    <a href="<?php echo esc_url(home_url('/terms')); ?>">Terms of Service</a>
                </p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
