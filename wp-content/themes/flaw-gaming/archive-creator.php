<?php
/**
 * Creators Archive Template
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-title">Content Creators</h1>
            <p class="archive-description">
                Streamers, video creators, and community personalities who bring FLAW Gaming to life.
            </p>
        </div>
    </header>

    <?php
    if (function_exists('flaw_get_creators')) :
        $creators = flaw_get_creators();
        if ($creators && $creators->total()) :
    ?>
        <section class="section section--all" aria-labelledby="all-heading">
            <div class="container">
                <div class="creators-grid" id="creators-grid">
                    <?php
                    while ($creators->fetch()) :
                        $creator_id = $creators->field('ID');
                        $followers = get_post_meta($creator_id, 'creator_followers', true);
                        $platform = get_post_meta($creator_id, 'creator_platform', true);
                        $data = [
                            'id' => $creator_id,
                            'handle' => $creators->field('post_title'),
                            'permalink' => get_permalink($creator_id),
                            'photo' => get_the_post_thumbnail_url($creator_id, 'card-thumbnail'),
                            'platforms' => $platform ? [$platform] : [],
                            'specialty' => get_post_meta($creator_id, 'creator_specialty', true),
                            'followers' => $followers ? ['total' => (int)$followers] : null,
                        ];
                        flaw_render_card('creator', $data);
                    endwhile;
                    ?>
                </div>
            </div>
        </section>
    <?php
        else :
    ?>
        <section class="section">
            <div class="container">
                <p class="no-results">No creators found.</p>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>

    <?php get_template_part('template-parts/sections/creator-cta'); ?>
</main>

<?php
get_footer();
