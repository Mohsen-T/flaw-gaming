<?php
/**
 * Custom Post Types Registration
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Post Types Class
 */
class FLAW_Post_Types {

    /**
     * Register all custom post types
     */
    public static function register() {
        self::register_games();
        self::register_teams();
        self::register_players();
        self::register_creators();
        self::register_events();
        self::register_partners();
    }

    /**
     * Register Games CPT
     */
    private static function register_games() {
        $labels = [
            'name'               => _x('Games', 'post type general name', 'flaw-gaming'),
            'singular_name'      => _x('Game', 'post type singular name', 'flaw-gaming'),
            'menu_name'          => _x('Games', 'admin menu', 'flaw-gaming'),
            'add_new'            => _x('Add New', 'game', 'flaw-gaming'),
            'add_new_item'       => __('Add New Game', 'flaw-gaming'),
            'edit_item'          => __('Edit Game', 'flaw-gaming'),
            'new_item'           => __('New Game', 'flaw-gaming'),
            'view_item'          => __('View Game', 'flaw-gaming'),
            'search_items'       => __('Search Games', 'flaw-gaming'),
            'not_found'          => __('No games found', 'flaw-gaming'),
            'not_found_in_trash' => __('No games found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'games', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-games',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('game', $args);
    }

    /**
     * Register Teams CPT
     */
    private static function register_teams() {
        $labels = [
            'name'               => _x('Teams', 'post type general name', 'flaw-gaming'),
            'singular_name'      => _x('Team', 'post type singular name', 'flaw-gaming'),
            'menu_name'          => _x('Teams', 'admin menu', 'flaw-gaming'),
            'add_new'            => _x('Add New', 'team', 'flaw-gaming'),
            'add_new_item'       => __('Add New Team', 'flaw-gaming'),
            'edit_item'          => __('Edit Team', 'flaw-gaming'),
            'new_item'           => __('New Team', 'flaw-gaming'),
            'view_item'          => __('View Team', 'flaw-gaming'),
            'search_items'       => __('Search Teams', 'flaw-gaming'),
            'not_found'          => __('No teams found', 'flaw-gaming'),
            'not_found_in_trash' => __('No teams found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'teams', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('team', $args);
    }

    /**
     * Register Players CPT
     */
    private static function register_players() {
        $labels = [
            'name'               => _x('Players', 'post type general name', 'flaw-gaming'),
            'singular_name'      => _x('Player', 'post type singular name', 'flaw-gaming'),
            'menu_name'          => _x('Players', 'admin menu', 'flaw-gaming'),
            'add_new'            => _x('Add New', 'player', 'flaw-gaming'),
            'add_new_item'       => __('Add New Player', 'flaw-gaming'),
            'edit_item'          => __('Edit Player', 'flaw-gaming'),
            'new_item'           => __('New Player', 'flaw-gaming'),
            'view_item'          => __('View Player', 'flaw-gaming'),
            'search_items'       => __('Search Players', 'flaw-gaming'),
            'not_found'          => __('No players found', 'flaw-gaming'),
            'not_found_in_trash' => __('No players found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'players', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-admin-users',
            'supports'           => ['title', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('player', $args);
    }

    /**
     * Register Creators CPT
     */
    private static function register_creators() {
        $labels = [
            'name'               => _x('Creators', 'post type general name', 'flaw-gaming'),
            'singular_name'      => _x('Creator', 'post type singular name', 'flaw-gaming'),
            'menu_name'          => _x('Creators', 'admin menu', 'flaw-gaming'),
            'add_new'            => _x('Add New', 'creator', 'flaw-gaming'),
            'add_new_item'       => __('Add New Creator', 'flaw-gaming'),
            'edit_item'          => __('Edit Creator', 'flaw-gaming'),
            'new_item'           => __('New Creator', 'flaw-gaming'),
            'view_item'          => __('View Creator', 'flaw-gaming'),
            'search_items'       => __('Search Creators', 'flaw-gaming'),
            'not_found'          => __('No creators found', 'flaw-gaming'),
            'not_found_in_trash' => __('No creators found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'creators', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 8,
            'menu_icon'          => 'dashicons-video-alt3',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('creator', $args);
    }

    /**
     * Register Events CPT
     */
    private static function register_events() {
        $labels = [
            'name'               => _x('Events', 'post type general name', 'flaw-gaming'),
            'singular_name'      => _x('Event', 'post type singular name', 'flaw-gaming'),
            'menu_name'          => _x('Events', 'admin menu', 'flaw-gaming'),
            'add_new'            => _x('Add New', 'event', 'flaw-gaming'),
            'add_new_item'       => __('Add New Event', 'flaw-gaming'),
            'edit_item'          => __('Edit Event', 'flaw-gaming'),
            'new_item'           => __('New Event', 'flaw-gaming'),
            'view_item'          => __('View Event', 'flaw-gaming'),
            'search_items'       => __('Search Events', 'flaw-gaming'),
            'not_found'          => __('No events found', 'flaw-gaming'),
            'not_found_in_trash' => __('No events found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'events', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 9,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'       => true,
        ];

        register_post_type('event', $args);
    }

    /**
     * Register Partners CPT
     */
    private static function register_partners() {
        $labels = [
            'name'               => _x('Partners', 'post type general name', 'flaw-gaming'),
            'singular_name'      => _x('Partner', 'post type singular name', 'flaw-gaming'),
            'menu_name'          => _x('Partners', 'admin menu', 'flaw-gaming'),
            'add_new'            => _x('Add New', 'partner', 'flaw-gaming'),
            'add_new_item'       => __('Add New Partner', 'flaw-gaming'),
            'edit_item'          => __('Edit Partner', 'flaw-gaming'),
            'new_item'           => __('New Partner', 'flaw-gaming'),
            'view_item'          => __('View Partner', 'flaw-gaming'),
            'search_items'       => __('Search Partners', 'flaw-gaming'),
            'not_found'          => __('No partners found', 'flaw-gaming'),
            'not_found_in_trash' => __('No partners found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'partners', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => false, // Partners don't need public archive
            'hierarchical'       => false,
            'menu_position'      => 10,
            'menu_icon'          => 'dashicons-building',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('partner', $args);
    }

    /**
     * Get Pods field definitions for export/documentation
     * These should be imported via Pods admin UI or pods_register_field_group()
     */
    public static function get_pods_field_definitions() {
        return [
            'game' => [
                'game_logo' => [
                    'label' => 'Game Logo',
                    'type' => 'file',
                    'file_format_type' => 'single',
                    'file_type' => 'images',
                    'required' => true,
                ],
                'game_cover' => [
                    'label' => 'Cover Image',
                    'type' => 'file',
                    'file_format_type' => 'single',
                    'file_type' => 'images',
                ],
                'game_studio' => [
                    'label' => 'Studio / Publisher',
                    'type' => 'text',
                    'required' => true,
                ],
                'game_url' => [
                    'label' => 'Official Game Link',
                    'type' => 'website',
                ],
                'game_stage' => [
                    'label' => 'Development Stage',
                    'type' => 'pick',
                    'pick_object' => 'custom-simple',
                    'pick_custom' => "announced|Announced\nalpha|Alpha\nclosed_beta|Closed Beta\nopen_beta|Open Beta\nearly_access|Early Access\nlive|Live\nsunset|Sunset / EOL",
                    'default' => 'live',
                    'required' => true,
                ],
                'game_genre' => [
                    'label' => 'Genre',
                    'type' => 'pick',
                    'pick_object' => 'custom-simple',
                    'pick_custom' => "fps|FPS\ntac_shooter|Tactical Shooter\nbattle_royale|Battle Royale\nmoba|MOBA\nfighting|Fighting\nsports|Sports / Racing\ncard|Card / Auto-battler\nrts|RTS / Strategy\nmmo|MMO\nother|Other",
                    'required' => true,
                ],
                'game_platforms' => [
                    'label' => 'Platforms',
                    'type' => 'pick',
                    'pick_object' => 'custom-simple',
                    'pick_custom' => "pc|PC\nplaystation|PlayStation\nxbox|Xbox\nswitch|Nintendo Switch\nmobile|Mobile\nvr|VR",
                    'pick_format_type' => 'multi',
                    'required' => true,
                ],
                'game_blockchain' => [
                    'label' => 'Blockchain',
                    'type' => 'pick',
                    'pick_object' => 'custom-simple',
                    'pick_custom' => "none|None (Traditional)\nethereum|Ethereum\npolygon|Polygon\nsolana|Solana\nimmutable|Immutable X\navalanche|Avalanche\narbitrum|Arbitrum\nother|Other",
                    'default' => 'none',
                ],
                'game_flaw_status' => [
                    'label' => 'FLAW Status',
                    'type' => 'pick',
                    'pick_object' => 'custom-simple',
                    'pick_custom' => "active|Active (Has Team/Creators)\nrecruiting|Recruiting\nexploring|Exploring\nwatching|Watching\ninactive|Inactive",
                    'default' => 'exploring',
                    'required' => true,
                ],
                'game_flaw_needs' => [
                    'label' => 'What FLAW Needs',
                    'type' => 'pick',
                    'pick_object' => 'custom-simple',
                    'pick_custom' => "competitive_team|Competitive Team\ncontent_creators|Content Creators\ncommunity_managers|Community Managers\ncoaches|Coaches / Analysts\ncasters|Casters / Talent\nbeta_testers|Beta Testers\npartners|Partners / Sponsors",
                    'pick_format_type' => 'multi',
                ],
                'game_is_featured' => [
                    'label' => 'Featured Game',
                    'type' => 'boolean',
                    'default' => 0,
                ],
                'game_priority' => [
                    'label' => 'Display Priority',
                    'type' => 'number',
                    'default' => 10,
                ],
            ],
            // Additional CPT definitions would follow the same pattern
            // See pods-fields.json for complete definitions
        ];
    }
}
