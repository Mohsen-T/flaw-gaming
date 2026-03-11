<?php
/**
 * Partners Archive Template
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-title">Our Partners</h1>
            <p class="archive-description">
                The amazing brands and organizations that support FLAW Gaming.
            </p>
        </div>
    </header>

    <?php
    // Simple flat query — all partners, no tier grouping
    $partners_query = new WP_Query([
        'post_type'      => 'partner',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    if ($partners_query->have_posts()) :
    ?>
        <section class="section section--partners">
            <div class="container">
                <div class="partners-grid">
                    <?php while ($partners_query->have_posts()) : $partners_query->the_post(); ?>
                        <article class="card card--partner">
                            <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'partner_website', true) ?: '#'); ?>"
                               class="card__link"
                               target="_blank"
                               rel="noopener noreferrer">
                                <div class="card__media card__media--logo">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'partner-logo')); ?>"
                                             alt="<?php the_title_attribute(); ?>"
                                             class="card__logo"
                                             loading="lazy">
                                    <?php else : ?>
                                        <div class="card__logo-placeholder">
                                            <?php echo esc_html(substr(get_the_title(), 0, 2)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card__content">
                                    <h3 class="card__title"><?php the_title(); ?></h3>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
    <?php else : ?>
        <section class="section">
            <div class="container">
                <p class="no-results">No partners found.</p>
            </div>
        </section>
    <?php endif; ?>

    <section class="section section--become-partner">
        <div class="container">
            <div class="cta-box">
                <h2>Become a Partner</h2>
                <p>Interested in partnering with FLAW Gaming? We'd love to hear from you.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn--primary">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
