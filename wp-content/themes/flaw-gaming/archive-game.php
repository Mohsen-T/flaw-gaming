<?php
/**
 * Games Archive Template
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-title">Our Games</h1>
            <p class="archive-description">
                The titles we compete in across multiple esports scenes.
            </p>
        </div>
    </header>

    <?php
    if (function_exists('flaw_get_featured_games')) :
        $games = flaw_get_featured_games(-1);
        if ($games && $games->total()) :
    ?>
        <section class="section section--games">
            <div class="container">
                <div class="games-grid">
                    <?php
                    while ($games->fetch()) :
                        $game_id = $games->field('ID');
                        $data = [
                            'id' => $game_id,
                            'title' => $games->field('post_title'),
                            'permalink' => get_permalink($game_id),
                            'cover' => get_the_post_thumbnail_url($game_id, 'card-thumbnail'),
                            'genre' => function_exists('flaw_get_game_genre') ? flaw_get_game_genre($game_id) : '',
                            'developer' => get_post_meta($game_id, 'game_developer', true),
                        ];
                        flaw_render_card('game', $data);
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
                <p class="no-results">No games found.</p>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>
</main>

<?php
get_footer();
