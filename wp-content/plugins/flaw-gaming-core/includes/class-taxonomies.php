<?php
/**
 * Custom Taxonomies Registration
 */

if (!defined('ABSPATH')) {
    exit;
}

class FLAW_Taxonomies {

    public static function register() {
        // Event Type Taxonomy
        register_taxonomy('event_type', ['event'], [
            'labels' => [
                'name'          => __('Event Types', 'flaw-gaming-core'),
                'singular_name' => __('Event Type', 'flaw-gaming-core'),
                'search_items'  => __('Search Event Types', 'flaw-gaming-core'),
                'all_items'     => __('All Event Types', 'flaw-gaming-core'),
                'edit_item'     => __('Edit Event Type', 'flaw-gaming-core'),
                'add_new_item'  => __('Add New Event Type', 'flaw-gaming-core'),
                'menu_name'     => __('Event Types', 'flaw-gaming-core'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'event-type'],
        ]);

        // Partner Tier Taxonomy
        register_taxonomy('partner_tier', ['partner'], [
            'labels' => [
                'name'          => __('Partner Tiers', 'flaw-gaming-core'),
                'singular_name' => __('Partner Tier', 'flaw-gaming-core'),
                'search_items'  => __('Search Partner Tiers', 'flaw-gaming-core'),
                'all_items'     => __('All Partner Tiers', 'flaw-gaming-core'),
                'edit_item'     => __('Edit Partner Tier', 'flaw-gaming-core'),
                'add_new_item'  => __('Add New Partner Tier', 'flaw-gaming-core'),
                'menu_name'     => __('Partner Tiers', 'flaw-gaming-core'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'partner-tier'],
        ]);

        // Player Role Taxonomy
        register_taxonomy('player_role', ['player'], [
            'labels' => [
                'name'          => __('Roles', 'flaw-gaming-core'),
                'singular_name' => __('Role', 'flaw-gaming-core'),
                'menu_name'     => __('Roles', 'flaw-gaming-core'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'role'],
        ]);

        // Game Genre Taxonomy
        register_taxonomy('game_genre', ['game'], [
            'labels' => [
                'name'          => __('Genres', 'flaw-gaming-core'),
                'singular_name' => __('Genre', 'flaw-gaming-core'),
                'menu_name'     => __('Genres', 'flaw-gaming-core'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'genre'],
        ]);

        // Insert default terms
        self::insert_default_terms();
    }

    private static function insert_default_terms() {
        // Event Types
        $event_types = ['Tournament', 'Scrimmage', 'Community Event', 'Stream Event', 'Practice'];
        foreach ($event_types as $type) {
            if (!term_exists($type, 'event_type')) {
                wp_insert_term($type, 'event_type');
            }
        }

        // Partner Tiers
        $partner_tiers = [
            'platinum' => 'Platinum',
            'gold'     => 'Gold',
            'silver'   => 'Silver',
            'bronze'   => 'Bronze',
        ];
        foreach ($partner_tiers as $slug => $name) {
            if (!term_exists($slug, 'partner_tier')) {
                wp_insert_term($name, 'partner_tier', ['slug' => $slug]);
            }
        }

        // Player Roles
        $roles = ['Captain', 'IGL', 'Fragger', 'Support', 'Flex', 'Coach', 'Manager', 'Analyst'];
        foreach ($roles as $role) {
            if (!term_exists($role, 'player_role')) {
                wp_insert_term($role, 'player_role');
            }
        }

        // Game Genres
        $genres = ['Battle Royale', 'FPS', 'MOBA', 'Fighting', 'Racing', 'Sports', 'Strategy', 'RPG'];
        foreach ($genres as $genre) {
            if (!term_exists($genre, 'game_genre')) {
                wp_insert_term($genre, 'game_genre');
            }
        }
    }
}
