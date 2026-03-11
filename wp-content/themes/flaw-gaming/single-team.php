<?php
/**
 * Single Team Template
 *
 * @package FLAW_Gaming
 */

get_header();

$team_id = get_the_ID();
$pod = flaw_is_pods_active() ? pods('team', $team_id) : null;
?>

<main id="main" class="site-main">
    <article id="team-<?php the_ID(); ?>" <?php post_class('team-single'); ?>>

        <header class="team-header">
            <div class="container">
                <div class="team-header__content">
                    <?php
                    $logo = $pod ? flaw_get_image_url($pod, 'team_logo') : null;
                    if ($logo) :
                    ?>
                        <div class="team-logo">
                            <img src="<?php echo esc_url($logo); ?>"
                                 alt="<?php the_title_attribute(); ?> logo">
                        </div>
                    <?php endif; ?>

                    <div class="team-info">
                        <h1 class="team-title"><?php the_title(); ?></h1>

                        <?php if ($pod) : ?>
                            <div class="team-meta">
                                <?php
                                $game_title = flaw_pick_value($pod->field('team_game.post_title'));
                                $region = flaw_pick_value($pod->field('team_region'));
                                $status = flaw_pick_value($pod->field('team_status'), 'active');
                                ?>

                                <?php if ($game_title) : ?>
                                    <span class="team-meta__game"><?php echo esc_html($game_title); ?></span>
                                <?php endif; ?>

                                <?php if ($region) : ?>
                                    <span class="team-meta__region"><?php echo esc_html($region); ?></span>
                                <?php endif; ?>

                                <span class="team-meta__status team-meta__status--<?php echo esc_attr($status); ?>">
                                    <?php echo esc_html(ucfirst($status)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($pod) : ?>
                            <div class="team-socials">
                                <?php
                                $socials = [
                                    'twitter' => flaw_pick_value($pod->field('team_social_twitter')),
                                    'discord' => flaw_pick_value($pod->field('team_social_discord')),
                                ];
                                foreach (array_filter($socials) as $platform => $url) :
                                ?>
                                    <a href="<?php echo esc_url($url); ?>"
                                       class="social-link social-link--<?php echo esc_attr($platform); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        <span class="screen-reader-text"><?php echo esc_html(ucfirst($platform)); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="team-content container">
            <?php $team_desc = $pod ? flaw_pick_value($pod->field('team_description')) : ''; ?>
            <?php if ($team_desc) : ?>
                <section class="team-description">
                    <?php echo wp_kses_post($team_desc); ?>
                </section>
            <?php endif; ?>

            <?php
            // Team Players
            if (function_exists('flaw_get_team_players')) :
                $players = flaw_get_team_players($team_id);
                if ($players && $players->total()) :
            ?>
                <section class="section section--roster" aria-labelledby="roster-heading">
                    <header class="section-header">
                        <h2 id="roster-heading" class="section-title">Active Roster</h2>
                    </header>

                    <div class="players-grid">
                        <?php
                        while ($players->fetch()) :
                            flaw_render_card_from_pod('player', $players);
                        endwhile;
                        ?>
                    </div>
                </section>
            <?php
                endif;
            endif;
            ?>

            <?php
            // Team Events
            if (function_exists('flaw_get_team_events')) :
                $events = flaw_get_team_events($team_id, 'completed', 6);
                if ($events && $events->total()) :
            ?>
                <section class="section section--events" aria-labelledby="events-heading">
                    <header class="section-header">
                        <h2 id="events-heading" class="section-title">Recent Results</h2>
                    </header>

                    <div class="events-grid">
                        <?php
                        while ($events->fetch()) :
                            flaw_render_card_from_pod('event', $events, ['variant' => 'compact']);
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
