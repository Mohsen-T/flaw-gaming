<?php
/**
 * Single Player Template
 *
 * @package FLAW_Gaming
 */

get_header();

$player_id = get_the_ID();
$pod = flaw_is_pods_active() ? pods('player', $player_id) : null;
?>

<main id="main" class="site-main">
    <article id="player-<?php the_ID(); ?>" <?php post_class('player-single'); ?>>

        <header class="player-header">
            <div class="container">
                <div class="player-header__content">
                    <?php
                    $photo = $pod ? flaw_get_image_url($pod, 'player_photo') : get_the_post_thumbnail_url($player_id, 'player-photo');
                    ?>
                    <div class="player-photo">
                        <?php if ($photo) : ?>
                            <img src="<?php echo esc_url($photo); ?>"
                                 alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <div class="player-photo__placeholder"></div>
                        <?php endif; ?>
                    </div>

                    <div class="player-info">
                        <?php if ($pod) : ?>
                            <?php
                            $gamertag = flaw_pick_value($pod->field('player_gamertag'));
                            $real_name = flaw_pick_value($pod->field('player_real_name'));
                            $role = function_exists('flaw_get_player_role') ? flaw_get_player_role(get_the_ID()) : '';
                            $nationality = flaw_pick_value($pod->field('player_nationality'));
                            $jersey = flaw_pick_value($pod->field('player_jersey_number'));
                            $team_title = flaw_pick_value($pod->field('player_team.post_title'));
                            $team_id = $pod->field('player_team.ID');
                            $team_logo = flaw_pick_value($pod->field('player_team.team_logo._src'));
                            ?>

                            <h1 class="player-gamertag"><?php echo esc_html($gamertag ?: get_the_title()); ?></h1>

                            <?php if ($real_name) : ?>
                                <p class="player-realname"><?php echo esc_html($real_name); ?></p>
                            <?php endif; ?>

                            <div class="player-meta">
                                <?php if ($role) : ?>
                                    <span class="player-role"><?php echo esc_html($role); ?></span>
                                <?php endif; ?>

                                <?php if ($nationality) : ?>
                                    <span class="player-nationality">
                                        <?php
                                        $flag_url = flaw_get_flag_url($nationality);
                                        if ($flag_url) :
                                        ?>
                                            <img src="<?php echo esc_url($flag_url); ?>"
                                                 alt=""
                                                 class="flag-icon">
                                        <?php endif; ?>
                                        <?php echo esc_html($nationality); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($jersey) : ?>
                                    <span class="player-jersey">#<?php echo esc_html($jersey); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($team_id) : ?>
                                <a href="<?php echo esc_url(get_permalink($team_id)); ?>" class="player-team">
                                    <?php if ($team_logo) : ?>
                                        <img src="<?php echo esc_url($team_logo); ?>"
                                             alt=""
                                             class="player-team__logo">
                                    <?php endif; ?>
                                    <span class="player-team__name"><?php echo esc_html($team_title); ?></span>
                                </a>
                            <?php endif; ?>

                            <div class="player-socials">
                                <?php
                                $socials = [
                                    'twitter'   => flaw_pick_value($pod->field('player_social_twitter')),
                                    'twitch'    => flaw_pick_value($pod->field('player_social_twitch')),
                                    'youtube'   => flaw_pick_value($pod->field('player_social_youtube')),
                                    'tiktok'    => flaw_pick_value($pod->field('player_social_tiktok')),
                                    'discord'   => flaw_pick_value($pod->field('player_social_discord')),
                                    'instagram' => flaw_pick_value($pod->field('player_social_instagram')),
                                    'blaze'     => flaw_pick_value($pod->field('player_social_blaze')),
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
                            <h1 class="player-gamertag"><?php the_title(); ?></h1>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="player-content container">
            <?php
            $player_bio = $pod ? flaw_pick_value($pod->field('player_bio')) : '';
            $stats_embed = $pod ? flaw_pick_value($pod->field('player_stats_embed')) : '';
            ?>
            <?php if (has_post_thumbnail() || $player_bio) : ?>
                <section class="player-bio">
                    <?php if ($player_bio) { echo wp_kses_post($player_bio); } ?>
                </section>
            <?php endif; ?>

            <?php if ($stats_embed) : ?>
                <section class="section section--stats" aria-labelledby="stats-heading">
                    <header class="section-header">
                        <h2 id="stats-heading" class="section-title">Statistics</h2>
                    </header>
                    <div class="stats-embed">
                        <?php echo $stats_embed; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>

    </article>
</main>

<?php
get_footer();
