<?php
/**
 * Single Creator Template
 *
 * @package FLAW_Gaming
 */

get_header();

$creator_id = get_the_ID();
$pod = flaw_is_pods_active() ? pods('creator', $creator_id) : null;
?>

<main id="main" class="site-main">
    <article id="creator-<?php the_ID(); ?>" <?php post_class('creator-single'); ?>>

        <header class="creator-header">
            <div class="container">
                <div class="creator-header__content">
                    <?php
                    $photo = $pod ? flaw_get_image_url($pod, 'creator_photo') : get_the_post_thumbnail_url($creator_id, 'player-photo');
                    ?>
                    <div class="creator-photo">
                        <?php if ($photo) : ?>
                            <img src="<?php echo esc_url($photo); ?>"
                                 alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <div class="creator-photo__placeholder"></div>
                        <?php endif; ?>

                        <?php
                        $is_featured = $pod ? (bool) $pod->field('creator_is_featured') : false;
                        if ($is_featured) :
                        ?>
                            <span class="creator-badge creator-badge--featured">Featured</span>
                        <?php endif; ?>
                    </div>

                    <div class="creator-info">
                        <?php if ($pod) : ?>
                            <?php
                            $handle = flaw_pick_value($pod->field('creator_handle'));
                            $status = flaw_pick_value($pod->field('creator_status'), 'active');
                            $specialty = $pod->field('creator_specialty'); // intentionally array
                            $platforms = $pod->field('creator_platforms'); // intentionally array
                            $followers_total = (int) flaw_pick_value($pod->field('creator_followers_total'));
                            $followers_twitch = (int) flaw_pick_value($pod->field('creator_followers_twitch'));
                            $followers_youtube = (int) flaw_pick_value($pod->field('creator_followers_youtube'));
                            ?>

                            <h1 class="creator-handle"><?php echo esc_html($handle ?: get_the_title()); ?></h1>

                            <?php if (!empty($specialty) && is_array($specialty)) : ?>
                                <div class="creator-specialty">
                                    <?php foreach ($specialty as $spec) : ?>
                                        <span class="creator-specialty__tag"><?php echo esc_html(ucfirst($spec)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($platforms) && is_array($platforms)) : ?>
                                <div class="creator-platforms">
                                    <?php foreach ($platforms as $plat) : ?>
                                        <span class="creator-platform"><?php echo esc_html($plat); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="creator-meta">
                                <span class="creator-meta__status creator-meta__status--<?php echo esc_attr($status); ?>">
                                    <?php echo esc_html(ucfirst($status)); ?>
                                </span>
                            </div>

                            <?php if ($followers_total || $followers_twitch || $followers_youtube) : ?>
                                <div class="creator-followers">
                                    <?php if ($followers_total) : ?>
                                        <div class="follower-stat">
                                            <span class="follower-stat__count"><?php echo esc_html(number_format($followers_total)); ?></span>
                                            <span class="follower-stat__label">Total Followers</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($followers_twitch) : ?>
                                        <div class="follower-stat follower-stat--twitch">
                                            <span class="follower-stat__count"><?php echo esc_html(number_format($followers_twitch)); ?></span>
                                            <span class="follower-stat__label">Twitch</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($followers_youtube) : ?>
                                        <div class="follower-stat follower-stat--youtube">
                                            <span class="follower-stat__count"><?php echo esc_html(number_format($followers_youtube)); ?></span>
                                            <span class="follower-stat__label">YouTube</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="creator-socials">
                                <?php
                                $socials = [
                                    'twitch'    => flaw_pick_value($pod->field('creator_social_twitch')),
                                    'youtube'   => flaw_pick_value($pod->field('creator_social_youtube')),
                                    'twitter'   => flaw_pick_value($pod->field('creator_social_twitter')),
                                    'tiktok'    => flaw_pick_value($pod->field('creator_social_tiktok')),
                                    'instagram' => flaw_pick_value($pod->field('creator_social_instagram')),
                                    'discord'   => flaw_pick_value($pod->field('creator_social_discord')),
                                    'blaze'     => flaw_pick_value($pod->field('creator_social_blaze')),
                                ];
                                foreach (array_filter($socials) as $platform => $url) :
                                ?>
                                    <a href="<?php echo esc_url($url); ?>"
                                       class="social-link social-link--<?php echo esc_attr($platform); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
                                        <?php echo function_exists('flaw_get_social_icon') ? flaw_get_social_icon($platform) : ''; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <h1 class="creator-handle"><?php the_title(); ?></h1>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="creator-content container">
            <?php
            // Games the creator plays
            if ($pod) :
                $games = $pod->field('creator_games'); // intentionally array
                if (!empty($games) && is_array($games)) :
            ?>
                <section class="section section--games" aria-labelledby="games-heading">
                    <header class="section-header">
                        <h2 id="games-heading" class="section-title">Games</h2>
                    </header>

                    <div class="games-grid">
                        <?php foreach ($games as $game) :
                            $g_id = is_array($game) ? ($game['ID'] ?? 0) : $game;
                            if (!$g_id) continue;
                            $game_pod = pods('game', $g_id);
                            if ($game_pod && $game_pod->exists()) :
                                flaw_render_card_from_pod('game', $game_pod);
                            endif;
                        endforeach; ?>
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
