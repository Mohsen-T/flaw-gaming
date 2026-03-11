<?php
/**
 * Custom Post Types Registration
 */

if (!defined('ABSPATH')) {
    exit;
}

class FLAW_Post_Types {

    public static function register() {
        // Event CPT
        register_post_type('event', [
            'labels' => [
                'name'               => __('Events', 'flaw-gaming-core'),
                'singular_name'      => __('Event', 'flaw-gaming-core'),
                'add_new'            => __('Add New Event', 'flaw-gaming-core'),
                'add_new_item'       => __('Add New Event', 'flaw-gaming-core'),
                'edit_item'          => __('Edit Event', 'flaw-gaming-core'),
                'new_item'           => __('New Event', 'flaw-gaming-core'),
                'view_item'          => __('View Event', 'flaw-gaming-core'),
                'search_items'       => __('Search Events', 'flaw-gaming-core'),
                'not_found'          => __('No events found', 'flaw-gaming-core'),
                'not_found_in_trash' => __('No events found in trash', 'flaw-gaming-core'),
                'menu_name'          => __('Events', 'flaw-gaming-core'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'events'],
            'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'menu_icon'           => 'dashicons-calendar-alt',
            'show_in_rest'        => true,
        ]);

        // Team CPT
        register_post_type('team', [
            'labels' => [
                'name'               => __('Teams', 'flaw-gaming-core'),
                'singular_name'      => __('Team', 'flaw-gaming-core'),
                'add_new'            => __('Add New Team', 'flaw-gaming-core'),
                'add_new_item'       => __('Add New Team', 'flaw-gaming-core'),
                'edit_item'          => __('Edit Team', 'flaw-gaming-core'),
                'menu_name'          => __('Teams', 'flaw-gaming-core'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'teams'],
            'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon'           => 'dashicons-groups',
            'show_in_rest'        => true,
        ]);

        // Player CPT
        register_post_type('player', [
            'labels' => [
                'name'               => __('Players', 'flaw-gaming-core'),
                'singular_name'      => __('Player', 'flaw-gaming-core'),
                'add_new'            => __('Add New Player', 'flaw-gaming-core'),
                'add_new_item'       => __('Add New Player', 'flaw-gaming-core'),
                'edit_item'          => __('Edit Player', 'flaw-gaming-core'),
                'menu_name'          => __('Players', 'flaw-gaming-core'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'players'],
            'supports'            => ['title', 'thumbnail', 'custom-fields'],
            'menu_icon'           => 'dashicons-admin-users',
            'show_in_rest'        => true,
        ]);

        // Creator CPT
        register_post_type('creator', [
            'labels' => [
                'name'               => __('Creators', 'flaw-gaming-core'),
                'singular_name'      => __('Creator', 'flaw-gaming-core'),
                'add_new'            => __('Add New Creator', 'flaw-gaming-core'),
                'add_new_item'       => __('Add New Creator', 'flaw-gaming-core'),
                'edit_item'          => __('Edit Creator', 'flaw-gaming-core'),
                'menu_name'          => __('Creators', 'flaw-gaming-core'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'creators'],
            'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon'           => 'dashicons-video-alt3',
            'show_in_rest'        => true,
        ]);

        // Game CPT
        register_post_type('game', [
            'labels' => [
                'name'               => __('Games', 'flaw-gaming-core'),
                'singular_name'      => __('Game', 'flaw-gaming-core'),
                'add_new'            => __('Add New Game', 'flaw-gaming-core'),
                'add_new_item'       => __('Add New Game', 'flaw-gaming-core'),
                'edit_item'          => __('Edit Game', 'flaw-gaming-core'),
                'menu_name'          => __('Games', 'flaw-gaming-core'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'games'],
            'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon'           => 'dashicons-games',
            'show_in_rest'        => true,
        ]);

        // Partner CPT
        register_post_type('partner', [
            'labels' => [
                'name'               => __('Partners', 'flaw-gaming-core'),
                'singular_name'      => __('Partner', 'flaw-gaming-core'),
                'add_new'            => __('Add New Partner', 'flaw-gaming-core'),
                'add_new_item'       => __('Add New Partner', 'flaw-gaming-core'),
                'edit_item'          => __('Edit Partner', 'flaw-gaming-core'),
                'menu_name'          => __('Partners', 'flaw-gaming-core'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'partners'],
            'supports'            => ['title', 'thumbnail', 'custom-fields'],
            'menu_icon'           => 'dashicons-money-alt',
            'show_in_rest'        => true,
        ]);
    }
}
