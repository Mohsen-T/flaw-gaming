<?php
/**
 * Template Functions
 *
 * @package FLAW_Gaming
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom classes to navigation menu items
 *
 * @param array $classes Existing classes
 * @param WP_Post $item Menu item
 * @return array
 */
function flaw_nav_menu_classes(array $classes, $item): array {
    if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
        $classes[] = 'is-active';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'flaw_nav_menu_classes', 10, 2);

/**
 * Custom walker for social menu
 */
class FLAW_Social_Menu_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $url = $item->url;
        $title = $item->title;

        // Detect platform from URL
        $platform = 'link';
        $platforms = ['twitter', 'discord', 'twitch', 'youtube', 'instagram', 'tiktok', 'facebook'];

        foreach ($platforms as $p) {
            if (stripos($url, $p) !== false) {
                $platform = $p;
                break;
            }
        }

        $output .= sprintf(
            '<li class="social-menu-item"><a href="%s" class="social-link social-link--%s" target="_blank" rel="noopener noreferrer"><span class="screen-reader-text">%s</span></a></li>',
            esc_url($url),
            esc_attr($platform),
            esc_html($title)
        );
    }
}

/**
 * Get event countdown data attributes
 *
 * @param string $date_string Date string
 * @return string
 */
function flaw_countdown_attrs(string $date_string): string {
    $timestamp = strtotime($date_string);

    if (!$timestamp) {
        return '';
    }

    return sprintf(
        'data-countdown="%s" data-countdown-timestamp="%d"',
        esc_attr($date_string),
        $timestamp
    );
}

/**
 * Get Twitch embed data attributes
 *
 * @param string $channel Twitch channel name
 * @param bool $chat Include chat
 * @return string
 */
function flaw_twitch_embed_attrs(string $channel, bool $chat = true): string {
    return sprintf(
        'data-twitch-channel="%s" data-twitch-chat="%s"',
        esc_attr($channel),
        $chat ? 'true' : 'false'
    );
}

/**
 * Output responsive image with lazy loading
 *
 * @param string $url Image URL
 * @param string $alt Alt text
 * @param string $class CSS class
 * @param array $sizes Sizes array
 */
function flaw_responsive_image(string $url, string $alt = '', string $class = '', array $sizes = []): void {
    if (empty($url)) {
        return;
    }

    $attrs = [
        'src'     => esc_url($url),
        'alt'     => esc_attr($alt),
        'loading' => 'lazy',
        'decoding' => 'async',
    ];

    if ($class) {
        $attrs['class'] = esc_attr($class);
    }

    if (!empty($sizes)) {
        $srcset = [];
        foreach ($sizes as $width => $src) {
            $srcset[] = esc_url($src) . ' ' . intval($width) . 'w';
        }
        $attrs['srcset'] = implode(', ', $srcset);
    }

    $attr_string = '';
    foreach ($attrs as $name => $value) {
        $attr_string .= sprintf(' %s="%s"', $name, $value);
    }

    echo '<img' . $attr_string . '>';
}

/**
 * Get pagination HTML
 *
 * @param int $total Total pages
 * @param int $current Current page
 * @return string
 */
function flaw_get_pagination(int $total, int $current = 1): string {
    if ($total <= 1) {
        return '';
    }

    $output = '<nav class="pagination" aria-label="Page navigation">';
    $output .= '<ul class="pagination__list">';

    // Previous
    if ($current > 1) {
        $output .= sprintf(
            '<li><a href="%s" class="pagination__link pagination__link--prev">&larr; Previous</a></li>',
            esc_url(add_query_arg('page', $current - 1))
        );
    }

    // Page numbers
    for ($i = 1; $i <= $total; $i++) {
        if ($i === $current) {
            $output .= sprintf(
                '<li><span class="pagination__link pagination__link--current" aria-current="page">%d</span></li>',
                $i
            );
        } elseif ($i === 1 || $i === $total || abs($i - $current) <= 2) {
            $output .= sprintf(
                '<li><a href="%s" class="pagination__link">%d</a></li>',
                esc_url(add_query_arg('page', $i)),
                $i
            );
        } elseif (abs($i - $current) === 3) {
            $output .= '<li><span class="pagination__ellipsis">&hellip;</span></li>';
        }
    }

    // Next
    if ($current < $total) {
        $output .= sprintf(
            '<li><a href="%s" class="pagination__link pagination__link--next">Next &rarr;</a></li>',
            esc_url(add_query_arg('page', $current + 1))
        );
    }

    $output .= '</ul>';
    $output .= '</nav>';

    return $output;
}

/**
 * Determine grid column class based on item count
 *
 * @param int $count Item count
 * @return string
 */
function flaw_auto_grid_class(int $count): string {
    if ($count === 1) {
        return 'grid--1';
    } elseif ($count === 2) {
        return 'grid--2';
    } elseif ($count === 3) {
        return 'grid--3';
    } else {
        return 'grid--4';
    }
}

/**
 * Dynamically add game sub-menu items under "Roster" nav item
 *
 * @param array $items Menu items
 * @param object $args Menu arguments
 * @return array
 */
function flaw_dynamic_roster_submenu($items, $args) {
    if ($args->theme_location !== 'primary') {
        return $items;
    }

    // Find the "Roster" menu item
    $roster_item_id = null;
    foreach ($items as $item) {
        if (strtolower(trim($item->title)) === 'roster' && $item->menu_item_parent == 0) {
            $roster_item_id = $item->ID;
            if (!in_array('menu-item-has-children', $item->classes)) {
                $item->classes[] = 'menu-item-has-children';
            }
            break;
        }
    }

    if (!$roster_item_id) {
        return $items;
    }

    if (!function_exists('flaw_get_games_with_active_teams')) {
        return $items;
    }

    $games = flaw_get_games_with_active_teams();
    if (!$games || !$games->total()) {
        return $items;
    }

    $order = count($items) + 1;
    $fake_id = 900000; // high number to avoid conflicts with real post IDs
    while ($games->fetch()) {
        $game_id = $games->field('ID');
        $game_title = flaw_pick_value($games->field('post_title'));
        $fake_id++;

        $game_item = new stdClass();
        $game_item->ID = $fake_id;
        $game_item->db_id = $fake_id;
        $game_item->title = $game_title;
        $game_item->url = get_post_type_archive_link('team') . '?game=' . $game_id;
        $game_item->menu_item_parent = $roster_item_id;
        $game_item->menu_order = $order++;
        $game_item->type = 'custom';
        $game_item->type_label = '';
        $game_item->object = 'custom';
        $game_item->object_id = $fake_id;
        $game_item->target = '';
        $game_item->attr_title = '';
        $game_item->description = '';
        $game_item->classes = ['menu-item', 'menu-item-game'];
        $game_item->xfn = '';
        $game_item->current = false;
        $game_item->current_item_ancestor = false;
        $game_item->current_item_parent = false;
        $game_item->post_parent = 0;

        $items[] = $game_item;
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'flaw_dynamic_roster_submenu', 10, 2);
