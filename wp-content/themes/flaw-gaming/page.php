<?php
/**
 * Page Template
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <article <?php post_class('page-content'); ?>>
        <header class="page-header">
            <div class="container">
                <h1 class="page-title"><?php the_title(); ?></h1>
            </div>
        </header>

        <div class="page-body">
            <div class="container">
                <?php
                the_content();

                wp_link_pages([
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'flaw-gaming'),
                    'after'  => '</div>',
                ]);
                ?>
            </div>
        </div>
    </article>
</main>

<?php
get_footer();
