<?php
/**
 * Single Event Template
 *
 * @package FLAW_Gaming
 */

get_header();

$event_id = get_the_ID();
$pod = function_exists('pods') ? pods('event', $event_id) : null;
$state = class_exists('FLAW_Event_State_Manager')
    ? FLAW_Event_State_Manager::get_state($event_id)
    : 'upcoming';

$template_args = [
    'pod'      => $pod,
    'state'    => $state,
    'event_id' => $event_id,
];
?>

<main id="main" class="site-main">
    <article id="event-<?php the_ID(); ?>" <?php post_class('event-single'); ?> data-event-state="<?php echo esc_attr($state); ?>">

        <?php get_template_part('template-parts/event/header', null, $template_args); ?>

        <div class="event-content container">
            <div class="event-main">
                <?php
                // State-based content rendering
                switch ($state) {
                    case 'upcoming':
                        get_template_part('template-parts/event/details', null, $template_args);
                        get_template_part('template-parts/event/registration', null, $template_args);
                        break;

                    case 'live':
                        get_template_part('template-parts/event/broadcast', null, $template_args);
                        get_template_part('template-parts/event/bracket', null, $template_args);
                        get_template_part('template-parts/event/details', null, $template_args);
                        break;

                    case 'completed':
                        get_template_part('template-parts/event/results', null, $template_args);
                        get_template_part('template-parts/event/bracket', null, $template_args);
                        get_template_part('template-parts/event/statistics', null, $template_args);
                        get_template_part('template-parts/event/media', null, $template_args);
                        get_template_part('template-parts/event/details', null, $template_args);
                        break;

                    case 'cancelled':
                        get_template_part('template-parts/event/details', null, $template_args);
                        break;
                }
                ?>
            </div>

            <aside class="event-sidebar">
                <?php get_template_part('template-parts/event/teams', null, $template_args); ?>
                <?php get_template_part('template-parts/event/partners', null, $template_args); ?>
            </aside>
        </div>

    </article>

    <?php
    // Related Events
    if (function_exists('flaw_get_upcoming_events')) :
        $related = flaw_get_upcoming_events(3);
        if ($related && $related->total()) :
    ?>
        <section class="section section--related" aria-labelledby="related-heading">
            <div class="container">
                <header class="section-header">
                    <h2 id="related-heading" class="section-title">More Events</h2>
                </header>

                <div class="events-grid">
                    <?php
                    while ($related->fetch()) :
                        if ($related->field('ID') !== get_the_ID()) :
                            flaw_render_card_from_pod('event', $related);
                        endif;
                    endwhile;
                    ?>
                </div>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>
</main>

<?php
get_footer();
