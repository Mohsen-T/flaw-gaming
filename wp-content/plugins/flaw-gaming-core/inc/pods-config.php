<?php
/**
 * Pods Field Configuration
 *
 * Programmatically registers all Pods fields for FLAW Gaming CPTs.
 * Run via admin: Tools > FLAW Setup, or trigger with: do_action('flaw_setup_pods')
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register all Pods configurations
 */
function flaw_register_pods_fields() {
    if (!function_exists('pods_api')) {
        return new WP_Error('pods_missing', 'Pods Framework is not active');
    }

    $api = pods_api();
    $results = [];

    // ═══════════════════════════════════════════════════════════════
    // GAME POD
    // ═══════════════════════════════════════════════════════════════
    $results['game'] = flaw_setup_pod_game($api);

    // ═══════════════════════════════════════════════════════════════
    // TEAM POD
    // ═══════════════════════════════════════════════════════════════
    $results['team'] = flaw_setup_pod_team($api);

    // ═══════════════════════════════════════════════════════════════
    // PLAYER POD
    // ═══════════════════════════════════════════════════════════════
    $results['player'] = flaw_setup_pod_player($api);

    // ═══════════════════════════════════════════════════════════════
    // CREATOR POD
    // ═══════════════════════════════════════════════════════════════
    $results['creator'] = flaw_setup_pod_creator($api);

    // ═══════════════════════════════════════════════════════════════
    // EVENT POD
    // ═══════════════════════════════════════════════════════════════
    $results['event'] = flaw_setup_pod_event($api);

    // ═══════════════════════════════════════════════════════════════
    // PARTNER POD
    // ═══════════════════════════════════════════════════════════════
    $results['partner'] = flaw_setup_pod_partner($api);

    return $results;
}

/**
 * Ensure a pod is registered in the Pods database.
 * Uses pods_api()->load_pod() to check the actual Pods registry,
 * not just whether the CPT exists in WordPress.
 */
function flaw_ensure_pod_registered($api, $pod_name, $config = []) {
    $existing = false;

    try {
        $existing = pods_api()->load_pod(['name' => $pod_name], false);
    } catch (Exception $e) {
        $existing = false;
    }

    if (empty($existing)) {
        $pod_params = array_merge([
            'name'    => $pod_name,
            'type'    => 'post_type',
            'storage' => 'meta',
        ], $config);

        try {
            $api->save_pod($pod_params);
        } catch (Exception $e) {
            // Pod may conflict with existing CPT, try extending instead
            error_log('FLAW Pods: Could not create pod ' . $pod_name . ': ' . $e->getMessage());
        }
    }
}

/**
 * Setup Game Pod
 */
function flaw_setup_pod_game($api) {
    $pod_name = 'game';

    // Always ensure pod is registered in Pods database
    flaw_ensure_pod_registered($api, $pod_name, [
        'label'   => 'Games',
        'options' => [
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-games',
            'supports'            => ['title', 'thumbnail', 'excerpt'],
            'has_archive'         => true,
            'rewrite_custom_slug' => 'games',
        ],
    ]);

    // Define fields
    $fields = [
        [
            'name'        => 'game_logo',
            'label'       => 'Game Logo',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
            'description' => 'Square logo for cards and icons',
        ],
        [
            'name'        => 'game_cover',
            'label'       => 'Cover Image',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
            'description' => 'Landscape cover art',
        ],
        [
            'name'    => 'game_studio',
            'label'   => 'Studio/Developer',
            'type'    => 'text',
        ],
        // Genre is handled by the 'game_genre' taxonomy (see class-taxonomies.php)
        [
            'name'    => 'game_platforms',
            'label'   => 'Platforms',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "PC\nPlayStation\nXbox\nNintendo Switch\nMobile\nBrowser",
            'pick_format_type' => 'multi',
            'pick_format_multi' => 'checkbox',
        ],
        [
            'name'    => 'game_stage',
            'label'   => 'Development Stage',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "Released\nEarly Access\nBeta\nAlpha\nIn Development",
            'pick_format_type' => 'single',
            'pick_format_single' => 'dropdown',
        ],
        [
            'name'    => 'game_blockchain',
            'label'   => 'Blockchain',
            'type'    => 'text',
            'description' => 'Leave empty for non-Web3 games',
        ],
        [
            'name'    => 'game_url',
            'label'   => 'Official Website',
            'type'    => 'website',
        ],
        [
            'name'    => 'game_flaw_status',
            'label'   => 'FLAW Status',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "active\nrecruiting\nwatching\ninactive",
            'pick_format_type' => 'single',
            'pick_format_single' => 'dropdown',
            'default' => 'watching',
        ],
        [
            'name'    => 'game_flaw_needs',
            'label'   => 'FLAW Needs',
            'type'    => 'paragraph',
            'description' => 'What FLAW is looking for in this game',
        ],
        [
            'name'    => 'game_is_featured',
            'label'   => 'Featured Game',
            'type'    => 'boolean',
            'boolean_format_type' => 'checkbox',
        ],
        [
            'name'    => 'game_priority',
            'label'   => 'Display Priority',
            'type'    => 'number',
            'number_format' => 'i18n',
            'default' => 10,
            'description' => 'Lower = higher priority',
        ],
    ];

    return flaw_save_pod_fields($api, $pod_name, $fields);
}

/**
 * Setup Team Pod
 */
function flaw_setup_pod_team($api) {
    $pod_name = 'team';

    flaw_ensure_pod_registered($api, $pod_name, [
        'label'   => 'Teams',
        'options' => [
            'public'              => true,
            'show_ui'             => true,
            'menu_icon'           => 'dashicons-groups',
            'supports'            => ['title', 'thumbnail'],
            'has_archive'         => true,
            'rewrite_custom_slug' => 'teams',
        ],
    ]);

    $fields = [
        [
            'name'        => 'team_logo',
            'label'       => 'Team Logo',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
        ],
        [
            'name'        => 'team_logo_light',
            'label'       => 'Team Logo (Light)',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
            'description' => 'Logo for dark backgrounds',
        ],
        [
            'name'    => 'team_game',
            'label'   => 'Game',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'game',
            'pick_format_type' => 'single',
            'pick_format_single' => 'autocomplete',
            'required' => true,
        ],
        [
            'name'    => 'team_status',
            'label'   => 'Status',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "active\ninactive\ndisbanded",
            'pick_format_type' => 'single',
            'default' => 'active',
        ],
        [
            'name'    => 'team_region',
            'label'   => 'Region',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "NA\nEU\nASIA\nOCE\nSA\nGlobal",
            'pick_format_type' => 'single',
        ],
        [
            'name'    => 'team_founded',
            'label'   => 'Founded Date',
            'type'    => 'date',
        ],
        [
            'name'    => 'team_description',
            'label'   => 'Description',
            'type'    => 'wysiwyg',
        ],
        [
            'name'    => 'team_social_twitter',
            'label'   => 'Twitter/X URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'team_social_discord',
            'label'   => 'Discord URL',
            'type'    => 'website',
        ],
    ];

    return flaw_save_pod_fields($api, $pod_name, $fields);
}

/**
 * Setup Player Pod
 */
function flaw_setup_pod_player($api) {
    $pod_name = 'player';

    flaw_ensure_pod_registered($api, $pod_name, [
        'label'   => 'Players',
        'options' => [
            'public'              => true,
            'show_ui'             => true,
            'menu_icon'           => 'dashicons-admin-users',
            'supports'            => ['title', 'thumbnail'],
            'has_archive'         => true,
            'rewrite_custom_slug' => 'players',
        ],
    ]);

    $fields = [
        [
            'name'    => 'player_gamertag',
            'label'   => 'Gamertag / IGN',
            'type'    => 'text',
            'required' => true,
        ],
        [
            'name'    => 'player_real_name',
            'label'   => 'Real Name',
            'type'    => 'text',
        ],
        [
            'name'        => 'player_photo',
            'label'       => 'Photo',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
        ],
        [
            'name'    => 'player_team',
            'label'   => 'Current Team',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'team',
            'pick_format_type' => 'single',
            'pick_format_single' => 'autocomplete',
        ],
        [
            'name'    => 'player_game_primary',
            'label'   => 'Primary Game',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'game',
            'pick_format_type' => 'single',
        ],
        // Role is handled by the 'player_role' taxonomy (see class-taxonomies.php)
        [
            'name'    => 'player_status',
            'label'   => 'Status',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "active\ninactive\nretired\ntrial",
            'default' => 'active',
        ],
        [
            'name'    => 'player_nationality',
            'label'   => 'Nationality',
            'type'    => 'text',
            'description' => 'ISO country code (e.g., US, GB)',
        ],
        [
            'name'    => 'player_jersey_number',
            'label'   => 'Jersey Number',
            'type'    => 'number',
        ],
        [
            'name'    => 'player_bio',
            'label'   => 'Bio',
            'type'    => 'wysiwyg',
        ],
        [
            'name'    => 'player_stats_embed',
            'label'   => 'Stats Embed Code',
            'type'    => 'code',
            'description' => 'External stats embed (Tracker Network, etc.)',
        ],
        [
            'name'    => 'player_social_twitter',
            'label'   => 'Twitter/X URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'player_social_twitch',
            'label'   => 'Twitch URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'player_social_youtube',
            'label'   => 'YouTube URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'player_social_tiktok',
            'label'   => 'TikTok URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'player_social_discord',
            'label'   => 'Discord URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'player_social_instagram',
            'label'   => 'Instagram URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'player_social_blaze',
            'label'   => 'Blaze.stream URL',
            'type'    => 'website',
        ],
    ];

    return flaw_save_pod_fields($api, $pod_name, $fields);
}

/**
 * Setup Creator Pod
 */
function flaw_setup_pod_creator($api) {
    $pod_name = 'creator';

    flaw_ensure_pod_registered($api, $pod_name, [
        'label'   => 'Creators',
        'options' => [
            'public'              => true,
            'show_ui'             => true,
            'menu_icon'           => 'dashicons-video-alt3',
            'supports'            => ['title', 'thumbnail'],
            'has_archive'         => true,
            'rewrite_custom_slug' => 'creators',
        ],
    ]);

    $fields = [
        [
            'name'    => 'creator_handle',
            'label'   => 'Handle / Display Name',
            'type'    => 'text',
            'required' => true,
        ],
        [
            'name'        => 'creator_photo',
            'label'       => 'Photo',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
        ],
        [
            'name'    => 'creator_specialty',
            'label'   => 'Specialty',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "streaming\nvideo\ncommentary\neducation\nentertainment",
            'pick_format_type' => 'multi',
            'pick_format_multi' => 'checkbox',
        ],
        [
            'name'    => 'creator_platforms',
            'label'   => 'Platforms',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "Twitch\nYouTube\nTikTok\nTwitter\nInstagram",
            'pick_format_type' => 'multi',
            'pick_format_multi' => 'checkbox',
        ],
        [
            'name'    => 'creator_status',
            'label'   => 'Status',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "active\ninactive",
            'default' => 'active',
        ],
        [
            'name'    => 'creator_is_featured',
            'label'   => 'Featured Creator',
            'type'    => 'boolean',
        ],
        [
            'name'    => 'creator_games',
            'label'   => 'Games',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'game',
            'pick_format_type' => 'multi',
        ],
        [
            'name'    => 'creator_followers_total',
            'label'   => 'Total Followers',
            'type'    => 'number',
        ],
        [
            'name'    => 'creator_followers_twitch',
            'label'   => 'Twitch Followers',
            'type'    => 'number',
        ],
        [
            'name'    => 'creator_followers_youtube',
            'label'   => 'YouTube Subscribers',
            'type'    => 'number',
        ],
        [
            'name'    => 'creator_social_twitch',
            'label'   => 'Twitch URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'creator_social_youtube',
            'label'   => 'YouTube URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'creator_social_twitter',
            'label'   => 'Twitter/X URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'creator_social_tiktok',
            'label'   => 'TikTok URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'creator_social_instagram',
            'label'   => 'Instagram URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'creator_social_discord',
            'label'   => 'Discord URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'creator_social_blaze',
            'label'   => 'Blaze.stream URL',
            'type'    => 'website',
        ],
    ];

    return flaw_save_pod_fields($api, $pod_name, $fields);
}

/**
 * Setup Event Pod
 */
function flaw_setup_pod_event($api) {
    $pod_name = 'event';

    flaw_ensure_pod_registered($api, $pod_name, [
        'label'   => 'Events',
        'options' => [
            'public'              => true,
            'show_ui'             => true,
            'menu_icon'           => 'dashicons-calendar-alt',
            'supports'            => ['title', 'thumbnail', 'editor'],
            'has_archive'         => true,
            'rewrite_custom_slug' => 'events',
        ],
    ]);

    $fields = [
        // === Core Info ===
        [
            'name'    => 'event_game',
            'label'   => 'Game',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'game',
            'pick_format_type' => 'single',
            'required' => true,
        ],
        [
            'name'    => 'event_status',
            'label'   => 'Status',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "upcoming\nlive\ncompleted\ncancelled",
            'default' => 'upcoming',
        ],
        [
            'name'    => 'event_date_start',
            'label'   => 'Start Date/Time',
            'type'    => 'datetime',
            'required' => true,
        ],
        [
            'name'    => 'event_date_end',
            'label'   => 'End Date/Time',
            'type'    => 'datetime',
        ],
        [
            'name'    => 'event_format',
            'label'   => 'Format',
            'type'    => 'text',
            'description' => 'e.g., Single Elimination, Round Robin',
        ],
        // Type is handled by the 'event_type' taxonomy (see class-taxonomies.php)
        [
            'name'    => 'event_location',
            'label'   => 'Location',
            'type'    => 'text',
            'description' => 'Online or physical location',
        ],
        [
            'name'    => 'event_organizer',
            'label'   => 'Organizer',
            'type'    => 'text',
        ],
        [
            'name'    => 'event_description',
            'label'   => 'Description',
            'type'    => 'wysiwyg',
        ],
        [
            'name'    => 'event_rules',
            'label'   => 'Rules',
            'type'    => 'wysiwyg',
        ],

        // === Registration ===
        [
            'name'    => 'event_registration_enabled',
            'label'   => 'Registration Enabled',
            'type'    => 'boolean',
        ],
        [
            'name'    => 'event_registration_url',
            'label'   => 'Registration URL',
            'type'    => 'text',
            'description' => 'External registration URL (e.g., https://forms.google.com/...). Leave empty if using embedded form below.',
        ],
        [
            'name'    => 'event_registration_form_plugin',
            'label'   => 'Embedded Form Plugin',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "none\nwpforms\ngravityforms\ncf7",
            'pick_format_type' => 'single',
            'pick_format_single' => 'dropdown',
            'default' => 'none',
            'description' => 'Select the form plugin to embed inline. Set to "none" if using an external URL above.',
        ],
        [
            'name'    => 'event_registration_form_id',
            'label'   => 'Form ID',
            'type'    => 'number',
            'number_format' => 'i18n',
            'description' => 'The form ID number from your form plugin (e.g., 507 for WPForms).',
        ],
        [
            'name'    => 'event_registration_deadline',
            'label'   => 'Registration Deadline',
            'type'    => 'datetime',
        ],
        [
            'name'    => 'event_registration_slots_total',
            'label'   => 'Total Slots',
            'type'    => 'number',
        ],
        [
            'name'    => 'event_registration_slots_filled',
            'label'   => 'Filled Slots',
            'type'    => 'number',
        ],
        [
            'name'    => 'event_registration_requirements',
            'label'   => 'Requirements',
            'type'    => 'wysiwyg',
        ],
        [
            'name'    => 'event_entry_fee',
            'label'   => 'Entry Fee',
            'type'    => 'text',
        ],
        [
            'name'    => 'event_entry_fee_token',
            'label'   => 'Fee Currency/Token',
            'type'    => 'text',
        ],

        // === Broadcast ===
        [
            'name'    => 'event_stream_platform',
            'label'   => 'Stream Platform',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "twitch\nyoutube\nkick\nfacebook",
        ],
        [
            'name'    => 'event_stream_channel',
            'label'   => 'Stream Channel',
            'type'    => 'text',
            'description' => 'Channel name or ID',
        ],
        [
            'name'    => 'event_stream_url_backup',
            'label'   => 'Backup Stream URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'event_stream_chat_enabled',
            'label'   => 'Show Chat',
            'type'    => 'boolean',
            'default' => true,
        ],
        [
            'name'    => 'event_bracket_live_url',
            'label'   => 'Live Bracket URL',
            'type'    => 'website',
        ],

        // === Results ===
        [
            'name'    => 'event_winner_team',
            'label'   => 'Winner Team',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'team',
            'pick_format_type' => 'single',
        ],
        [
            'name'    => 'event_winner_players',
            'label'   => 'Winner Players',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'player',
            'pick_format_type' => 'multi',
        ],
        [
            'name'    => 'event_placement_org',
            'label'   => 'FLAW Placement',
            'type'    => 'number',
            'description' => '1 = 1st place, 2 = 2nd, etc.',
        ],
        [
            'name'    => 'event_prize_pool_total',
            'label'   => 'Total Prize Pool',
            'type'    => 'text',
        ],
        [
            'name'    => 'event_prize_won',
            'label'   => 'Prize Won',
            'type'    => 'text',
        ],
        [
            'name'    => 'event_prize_token',
            'label'   => 'Prize Currency/Token',
            'type'    => 'text',
        ],
        [
            'name'    => 'event_final_bracket_url',
            'label'   => 'Final Bracket URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'event_recap_content',
            'label'   => 'Event Recap',
            'type'    => 'wysiwyg',
        ],

        // === Statistics ===
        [
            'name'    => 'event_stats_matches_played',
            'label'   => 'Matches Played',
            'type'    => 'number',
        ],
        [
            'name'    => 'event_stats_matches_won',
            'label'   => 'Matches Won',
            'type'    => 'number',
        ],
        [
            'name'    => 'event_stats_maps_played',
            'label'   => 'Maps Played',
            'type'    => 'number',
        ],
        [
            'name'    => 'event_stats_maps_won',
            'label'   => 'Maps Won',
            'type'    => 'number',
        ],
        [
            'name'    => 'event_stats_mvp',
            'label'   => 'Event MVP',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'player',
            'pick_format_type' => 'single',
        ],
        [
            'name'    => 'event_stats_highlights',
            'label'   => 'Highlights',
            'type'    => 'wysiwyg',
        ],
        [
            'name'    => 'event_stats_json',
            'label'   => 'Stats JSON',
            'type'    => 'code',
            'description' => 'Advanced stats data',
        ],

        // === Media ===
        [
            'name'    => 'event_vod_platform',
            'label'   => 'VOD Platform',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "youtube\ntwitch",
        ],
        [
            'name'    => 'event_vod_id',
            'label'   => 'VOD ID',
            'type'    => 'text',
        ],
        [
            'name'    => 'event_vod_url',
            'label'   => 'VOD URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'event_photos_gallery',
            'label'   => 'Photo Gallery',
            'type'    => 'file',
            'file_format_type' => 'multi',
            'file_type' => 'images',
        ],
        [
            'name'    => 'event_photos_external_url',
            'label'   => 'External Photos URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'event_press_links',
            'label'   => 'Press Coverage',
            'type'    => 'wysiwyg',
            'description' => 'Links to press articles',
        ],

        // === Relationships ===
        [
            'name'    => 'event_teams_participating',
            'label'   => 'Participating Teams',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'team',
            'pick_format_type' => 'multi',
        ],
        [
            'name'    => 'event_partners',
            'label'   => 'Event Partners',
            'type'    => 'pick',
            'pick_object' => 'post_type',
            'pick_val'    => 'partner',
            'pick_format_type' => 'multi',
        ],
    ];

    return flaw_save_pod_fields($api, $pod_name, $fields);
}

/**
 * Setup Partner Pod
 */
function flaw_setup_pod_partner($api) {
    $pod_name = 'partner';

    flaw_ensure_pod_registered($api, $pod_name, [
        'label'   => 'Partners',
        'options' => [
            'public'              => true,
            'show_ui'             => true,
            'menu_icon'           => 'dashicons-businessperson',
            'supports'            => ['title', 'thumbnail'],
            'has_archive'         => true,
            'rewrite_custom_slug' => 'partners',
        ],
    ]);

    $fields = [
        [
            'name'        => 'partner_logo',
            'label'       => 'Logo',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
        ],
        [
            'name'        => 'partner_logo_light',
            'label'       => 'Logo (Light)',
            'type'        => 'file',
            'file_format_type' => 'single',
            'file_type'   => 'images',
        ],
        [
            'name'    => 'partner_type',
            'label'   => 'Type',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "Sponsor\nPeripheral\nApparel\nPlatform\nMedia\nCommunity",
        ],
        // Tier is handled by the 'partner_tier' taxonomy (see class-taxonomies.php)
        [
            'name'    => 'partner_status',
            'label'   => 'Status',
            'type'    => 'pick',
            'pick_object' => 'custom-simple',
            'pick_custom' => "active\ninactive",
            'default' => 'active',
        ],
        [
            'name'    => 'partner_website',
            'label'   => 'Website',
            'type'    => 'website',
        ],
        [
            'name'    => 'partner_promo_code',
            'label'   => 'Promo Code',
            'type'    => 'text',
        ],
        [
            'name'    => 'partner_promo_url',
            'label'   => 'Promo URL',
            'type'    => 'website',
        ],
        [
            'name'    => 'partner_description',
            'label'   => 'Description',
            'type'    => 'paragraph',
        ],
        [
            'name'    => 'partner_priority',
            'label'   => 'Display Priority',
            'type'    => 'number',
            'default' => 10,
        ],
    ];

    return flaw_save_pod_fields($api, $pod_name, $fields);
}

/**
 * Save fields to a pod
 */
function flaw_save_pod_fields($api, $pod_name, $fields) {
    $results = [];

    foreach ($fields as $field) {
        try {
            $field['pod'] = $pod_name;

            // Check if field exists
            $existing = pods_api()->load_field([
                'pod'  => $pod_name,
                'name' => $field['name'],
            ]);

            if ($existing) {
                $existing_type = $existing['type'] ?? '';

                // If field type has changed, delete and recreate
                // (Pods save_field may not update type on existing fields)
                if (!empty($existing_type) && $existing_type !== $field['type']) {
                    try {
                        $api->delete_field(['id' => $existing['id'], 'pod' => $pod_name, 'name' => $field['name']]);
                        // Don't set id - let it create a new field
                    } catch (Exception $e) {
                        // If delete fails, try updating with id anyway
                        $field['id'] = $existing['id'];
                    }
                } else {
                    $field['id'] = $existing['id'];
                }
            }

            $field_id = $api->save_field($field);
            $results[$field['name']] = $field_id ? 'success' : 'failed';
        } catch (Exception $e) {
            $results[$field['name']] = 'error: ' . $e->getMessage();
        }
    }

    return $results;
}

/**
 * Admin page for setup
 */
function flaw_admin_setup_page() {
    add_management_page(
        'FLAW Setup',
        'FLAW Setup',
        'manage_options',
        'flaw-setup',
        'flaw_render_setup_page'
    );
}
add_action('admin_menu', 'flaw_admin_setup_page');

/**
 * Render setup page
 */
function flaw_render_setup_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    $results = [];

    // Handle form submission
    if (isset($_POST['flaw_setup_pods']) && check_admin_referer('flaw_setup_pods_nonce')) {
        $results = flaw_register_pods_fields();

        if (is_wp_error($results)) {
            $message = '<div class="notice notice-error"><p>' . esc_html($results->get_error_message()) . '</p></div>';
        } else {
            $message = '<div class="notice notice-success"><p>Pods configuration complete!</p></div>';
        }
    }

    // Check if Pods is active
    $pods_active = function_exists('pods');
    ?>
    <div class="wrap">
        <h1>FLAW Gaming Setup</h1>

        <?php echo $message; ?>

        <?php if (!$pods_active) : ?>
            <div class="notice notice-error">
                <p><strong>Pods Framework is not active.</strong></p>
                <p>Please activate the Pods plugin first: <a href="<?php echo admin_url('plugins.php'); ?>">Plugins Page</a></p>
            </div>
        <?php else : ?>
            <div class="card">
                <h2>Configure Pods Fields</h2>
                <p>This will create/update all custom fields for your CPTs (Games, Teams, Players, Creators, Events, Partners).</p>

                <form method="post">
                    <?php wp_nonce_field('flaw_setup_pods_nonce'); ?>
                    <p>
                        <input type="submit" name="flaw_setup_pods" class="button button-primary" value="Setup Pods Fields">
                    </p>
                </form>

                <?php if (!empty($results) && !is_wp_error($results)) : ?>
                    <h3>Results:</h3>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>Pod</th>
                                <th>Field</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $pod => $fields) : ?>
                                <?php if (is_array($fields)) : ?>
                                    <?php foreach ($fields as $field => $status) : ?>
                                        <tr>
                                            <td><?php echo esc_html($pod); ?></td>
                                            <td><?php echo esc_html($field); ?></td>
                                            <td><?php echo esc_html($status); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-top: 20px;">
            <h2>Quick Links</h2>
            <ul>
                <li><a href="<?php echo admin_url('admin.php?page=pods'); ?>">Pods Admin</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=game'); ?>">Games</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=team'); ?>">Teams</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=player'); ?>">Players</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=event'); ?>">Events</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=creator'); ?>">Creators</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=partner'); ?>">Partners</a></li>
            </ul>
        </div>
    </div>
    <?php
}

// Allow triggering via action
add_action('flaw_setup_pods', 'flaw_register_pods_fields');
