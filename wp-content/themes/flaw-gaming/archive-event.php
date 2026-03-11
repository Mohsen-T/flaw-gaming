<?php
/**
 * Events Archive Template
 *
 * @package FLAW_Gaming
 */

get_header();
?>

<main id="main" class="site-main">
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-title">Events Hub</h1>
            <p class="archive-description">
                Tournaments, scrims, and community events. Watch live, register for upcoming events, or relive past victories.
            </p>
        </div>
    </header>

    <?php
    // Live Events
    if (function_exists('flaw_get_live_events')) :
        $live = flaw_get_live_events();
        if ($live && $live->total()) :
    ?>
        <section class="section section--live" aria-labelledby="live-heading">
            <div class="container">
                <header class="section-header">
                    <h2 id="live-heading" class="section-title">
                        <span class="live-pulse"></span>
                        Happening Now
                    </h2>
                </header>

                <div class="events-grid events-grid--featured">
                    <?php
                    while ($live->fetch()) :
                        $data = flaw_get_event_card_data($live);
                        $data['stream'] = [
                            'platform' => flaw_pick_value($live->field('event_stream_platform')),
                            'channel'  => flaw_pick_value($live->field('event_stream_channel')),
                        ];
                        flaw_render_card('event', $data, ['variant' => 'live']);
                    endwhile;
                    ?>
                </div>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>

    <?php
    // Upcoming Events
    if (function_exists('flaw_get_upcoming_events')) :
        $upcoming = flaw_get_upcoming_events(8);
        if ($upcoming && $upcoming->total()) :
    ?>
        <section class="section section--upcoming" aria-labelledby="upcoming-heading">
            <div class="container">
                <header class="section-header">
                    <h2 id="upcoming-heading" class="section-title">Upcoming Events</h2>
                </header>

                <div class="events-grid">
                    <?php
                    while ($upcoming->fetch()) :
                        flaw_render_card_from_pod('event', $upcoming);
                    endwhile;
                    ?>
                </div>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>

    <?php
    // Past Events with filters
    if (function_exists('flaw_get_past_events')) :
    ?>
        <section class="section section--past" aria-labelledby="past-heading">
            <div class="container">
                <header class="section-header">
                    <h2 id="past-heading" class="section-title">Event Archive</h2>

                    <div class="archive-filters" data-filter-target="past-events-grid">
                        <?php
                        // Year filter
                        $current_year = date('Y');
                        $years = range($current_year, $current_year - 3);
                        ?>
                        <select class="filter-select" data-filter="year" aria-label="Filter by year">
                            <option value="">All Years</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php
                        // Game filter
                        if (function_exists('flaw_get_games_with_active_teams')) :
                            $games = flaw_get_games_with_active_teams();
                            if ($games && $games->total()) :
                        ?>
                            <select class="filter-select" data-filter="game_id" aria-label="Filter by game">
                                <option value="">All Games</option>
                                <?php while ($games->fetch()) : ?>
                                    <option value="<?php echo esc_attr($games->field('ID')); ?>">
                                        <?php echo esc_html(flaw_pick_value($games->field('post_title'))); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        <?php
                            endif;
                        endif;
                        ?>
                    </div>
                </header>

                <?php
                $past = flaw_get_past_events(12);
                if ($past && $past->total()) :
                ?>
                    <div id="past-events-grid" class="events-grid" data-total="<?php echo esc_attr($past->total_found()); ?>">
                        <?php
                        while ($past->fetch()) :
                            flaw_render_card_from_pod('event', $past);
                        endwhile;
                        ?>
                    </div>

                    <?php if ($past->total_found() > 12) : ?>
                        <div class="load-more-wrapper">
                            <button class="btn btn--outline load-more"
                                    data-endpoint="<?php echo esc_url(rest_url('flaw/v1/events/past')); ?>"
                                    data-page="1"
                                    data-limit="12">
                                Load More Events
                            </button>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="no-results">No past events found.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php
get_footer();
