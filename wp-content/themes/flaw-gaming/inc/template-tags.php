<?php
/**
 * Template Tags
 *
 * @package FLAW_Gaming
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output event date range
 *
 * @param int|null $event_id Event ID
 */
function flaw_the_event_dates(?int $event_id = null): void {
    if (!function_exists('flaw_format_date_range')) {
        return;
    }

    $event_id = $event_id ?: get_the_ID();
    $pod = flaw_is_pods_active() ? pods('event', $event_id) : null;

    if (!$pod) {
        return;
    }

    $start = flaw_pick_value($pod->field('event_date_start'));
    $end = flaw_pick_value($pod->field('event_date_end'));

    echo '<time class="event-dates" datetime="' . esc_attr($start) . '">';
    echo esc_html(flaw_format_date_range($start, $end));
    echo '</time>';
}

/**
 * Output event status badge
 *
 * @param int|null $event_id Event ID
 */
function flaw_the_event_badge(?int $event_id = null): void {
    if (!function_exists('flaw_event_badge')) {
        return;
    }

    echo flaw_event_badge($event_id);
}

/**
 * Output event game info
 *
 * @param int|null $event_id Event ID
 */
function flaw_the_event_game(?int $event_id = null): void {
    $event_id = $event_id ?: get_the_ID();
    $pod = flaw_is_pods_active() ? pods('event', $event_id) : null;

    if (!$pod) {
        return;
    }

    $game_id = $pod->field('event_game.ID');
    $game_title = flaw_pick_value($pod->field('event_game.post_title'));
    $game_logo = flaw_pick_value($pod->field('event_game.game_logo._src'));

    if (!$game_id) {
        return;
    }

    echo '<a href="' . esc_url(get_permalink($game_id)) . '" class="event-game">';

    if ($game_logo) {
        echo '<img src="' . esc_url($game_logo) . '" alt="" class="event-game__logo" loading="lazy">';
    }

    echo '<span class="event-game__name">' . esc_html($game_title) . '</span>';
    echo '</a>';
}

/**
 * Output placement with ordinal suffix
 *
 * @param int $placement Placement number
 */
function flaw_the_placement(int $placement): void {
    if (!function_exists('flaw_format_placement')) {
        echo esc_html($placement);
        return;
    }

    $formatted = flaw_format_placement($placement);
    $class = 'placement';

    if ($placement === 1) {
        $class .= ' placement--gold';
    } elseif ($placement === 2) {
        $class .= ' placement--silver';
    } elseif ($placement === 3) {
        $class .= ' placement--bronze';
    }

    echo '<span class="' . esc_attr($class) . '">' . esc_html($formatted) . '</span>';
}

/**
 * Output social links for current post type
 *
 * @param array $socials Array of platform => URL
 */
function flaw_the_social_links(array $socials): void {
    $socials = array_filter($socials);

    if (empty($socials)) {
        return;
    }

    echo '<div class="social-links">';

    foreach ($socials as $platform => $url) {
        printf(
            '<a href="%s" class="social-link social-link--%s" target="_blank" rel="noopener noreferrer"><span class="screen-reader-text">%s</span></a>',
            esc_url($url),
            esc_attr($platform),
            esc_html(ucfirst($platform))
        );
    }

    echo '</div>';
}

/**
 * Output formatted follower count
 *
 * @param int $count Follower count
 * @param string $platform Platform name
 */
function flaw_the_followers(int $count, string $platform = ''): void {
    if (!function_exists('flaw_format_number')) {
        echo esc_html(number_format($count));
        return;
    }

    $formatted = flaw_format_number($count);

    if ($platform) {
        echo '<span class="followers followers--' . esc_attr($platform) . '">';
        echo '<span class="followers__count">' . esc_html($formatted) . '</span>';
        echo '<span class="followers__platform">' . esc_html(ucfirst($platform)) . '</span>';
        echo '</span>';
    } else {
        echo '<span class="followers">' . esc_html($formatted) . ' followers</span>';
    }
}

/**
 * Output partner promo code
 *
 * @param string $code Promo code
 * @param string|null $url Promo URL
 */
function flaw_the_promo(string $code, ?string $url = null): void {
    if (empty($code)) {
        return;
    }

    echo '<div class="promo-code">';

    if ($url) {
        echo '<a href="' . esc_url($url) . '" class="promo-code__link" target="_blank" rel="noopener noreferrer">';
    }

    echo '<span class="promo-code__label">Use code:</span>';
    echo '<span class="promo-code__value">' . esc_html($code) . '</span>';

    if ($url) {
        echo '</a>';
    }

    echo '</div>';
}

/**
 * Output countdown timer element
 *
 * @param string $date_string Target date
 * @param string $label Optional label
 */
function flaw_the_countdown(string $date_string, string $label = 'Starts in'): void {
    $timestamp = strtotime($date_string);

    if (!$timestamp || $timestamp < time()) {
        return;
    }

    echo '<div class="countdown" ' . flaw_countdown_attrs($date_string) . '>';

    if ($label) {
        echo '<span class="countdown__label">' . esc_html($label) . '</span>';
    }

    echo '<div class="countdown__timer">';
    echo '<div class="countdown__unit"><span class="countdown__value" data-unit="days">--</span><span class="countdown__name">Days</span></div>';
    echo '<div class="countdown__unit"><span class="countdown__value" data-unit="hours">--</span><span class="countdown__name">Hours</span></div>';
    echo '<div class="countdown__unit"><span class="countdown__value" data-unit="minutes">--</span><span class="countdown__name">Min</span></div>';
    echo '<div class="countdown__unit"><span class="countdown__value" data-unit="seconds">--</span><span class="countdown__name">Sec</span></div>';
    echo '</div>';

    echo '</div>';
}

/**
 * Output Twitch stream status indicator
 *
 * @param string $channel Twitch channel name
 */
function flaw_the_twitch_status(string $channel): void {
    if (empty($channel)) {
        return;
    }

    echo '<div class="twitch-status" data-twitch-channel="' . esc_attr($channel) . '">';
    echo '<span class="twitch-status__indicator"></span>';
    echo '<span class="twitch-status__text">Checking...</span>';
    echo '</div>';
}
