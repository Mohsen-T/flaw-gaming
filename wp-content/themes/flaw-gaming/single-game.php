<?php
/**
 * Single Game Template
 *
 * @package FLAW_Gaming
 */

get_header();

$game_id = get_the_ID();
$pod = flaw_is_pods_active() ? pods('game', $game_id) : null;
?>

<main id="main" class="site-main">
    <article id="game-<?php the_ID(); ?>" <?php post_class('game-single'); ?>>

        <header class="game-header">
            <div class="container">
                <?php
                $cover = $pod ? flaw_get_image_url($pod, 'game_cover') : get_the_post_thumbnail_url($game_id, 'hero-background');
                if ($cover) :
                ?>
                    <div class="game-header__cover">
                        <img src="<?php echo esc_url($cover); ?>"
                             alt=""
                             class="game-header__cover-img">
                    </div>
                <?php endif; ?>

                <div class="game-header__content">
                    <?php
                    $logo = $pod ? flaw_get_image_url($pod, 'game_logo') : null;
                    if ($logo) :
                    ?>
                        <div class="game-logo">
                            <img src="<?php echo esc_url($logo); ?>"
                                 alt="<?php the_title_attribute(); ?> logo">
                        </div>
                    <?php endif; ?>

                    <div class="game-info">
                        <h1 class="game-title"><?php the_title(); ?></h1>

                        <?php if ($pod) : ?>
                            <div class="game-meta">
                                <?php
                                $studio = flaw_pick_value($pod->field('game_studio'));
                                $genre = function_exists('flaw_get_game_genre') ? flaw_get_game_genre($game_id) : '';
                                $stage = flaw_pick_value($pod->field('game_stage'));
                                $blockchain = flaw_pick_value($pod->field('game_blockchain'));
                                $flaw_status = flaw_pick_value($pod->field('game_flaw_status'), 'watching');
                                $url = flaw_pick_value($pod->field('game_url'));
                                $platforms = $pod->field('game_platforms'); // intentionally array
                                ?>

                                <?php if ($studio) : ?>
                                    <span class="game-meta__studio"><?php echo esc_html($studio); ?></span>
                                <?php endif; ?>

                                <?php if ($genre) : ?>
                                    <span class="game-meta__genre"><?php echo esc_html($genre); ?></span>
                                <?php endif; ?>

                                <?php if ($stage) : ?>
                                    <span class="game-meta__stage"><?php echo esc_html($stage); ?></span>
                                <?php endif; ?>

                                <?php if ($blockchain) : ?>
                                    <span class="game-meta__blockchain"><?php echo esc_html($blockchain); ?></span>
                                <?php endif; ?>

                                <span class="game-meta__status game-meta__status--<?php echo esc_attr($flaw_status); ?>">
                                    <?php echo esc_html(ucfirst($flaw_status)); ?>
                                </span>
                            </div>

                            <?php if (!empty($platforms) && is_array($platforms)) : ?>
                                <div class="game-platforms">
                                    <?php foreach ($platforms as $platform) : ?>
                                        <span class="game-platform"><?php echo esc_html($platform); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($url) : ?>
                                <a href="<?php echo esc_url($url); ?>"
                                   class="btn btn--outline btn--sm"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    Official Website
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="game-content container">
            <?php
            $needs = $pod ? flaw_pick_value($pod->field('game_flaw_needs')) : '';
            if ($needs) :
            ?>
                <section class="game-needs">
                    <h2>What We're Looking For</h2>
                    <div class="prose">
                        <?php echo wp_kses_post($needs); ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (has_excerpt()) : ?>
                <section class="game-description">
                    <div class="prose">
                        <?php the_excerpt(); ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php
            // Teams for this game
            if (function_exists('flaw_get_active_teams')) :
                $teams = flaw_get_active_teams($game_id);
                if ($teams && $teams->total()) :
            ?>
                <section class="section section--teams" aria-labelledby="teams-heading">
                    <header class="section-header">
                        <h2 id="teams-heading" class="section-title">Our Teams</h2>
                    </header>

                    <div class="teams-grid">
                        <?php
                        while ($teams->fetch()) :
                            flaw_render_card_from_pod('team', $teams);
                        endwhile;
                        ?>
                    </div>
                </section>
            <?php
                endif;
            endif;
            ?>
        </div>

    </article>
</main>

<?php
get_footer();
