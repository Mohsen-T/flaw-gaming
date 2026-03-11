<?php
/**
 * Card Data Preparation Functions
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get event card data from Pods instance
 *
 * @param Pods $pod Pods instance
 * @return array
 */
function flaw_get_event_card_data($pod): array {
    if (!$pod) {
        return [];
    }

    // Support FLAW_Query_Wrapper, Pods, WP_Post, and numeric IDs
    if (is_numeric($pod)) {
        $event_id = (int) $pod;
        return [
            'id'        => $event_id,
            'title'     => get_the_title($event_id),
            'permalink' => get_permalink($event_id),
            'thumbnail' => get_the_post_thumbnail_url($event_id, 'medium'),
            'status'    => function_exists('flaw_event_get_state') ? flaw_event_get_state($event_id) : 'upcoming',
            'date_start' => flaw_pick_value(get_post_meta($event_id, 'event_date_start', true)),
            'date_end'   => flaw_pick_value(get_post_meta($event_id, 'event_date_end', true)),
        ];
    }

    $event_id = method_exists($pod, 'field') ? $pod->field('ID') : ($pod->ID ?? get_the_ID());

    return [
        'id'           => $event_id,
        'title'        => flaw_pick_value($pod->field('post_title')),
        'permalink'    => get_permalink($event_id),
        'status'       => flaw_pick_value($pod->field('event_status'), 'upcoming'),
        'date_start'   => flaw_pick_value($pod->field('event_date_start')),
        'date_end'     => flaw_pick_value($pod->field('event_date_end')),
        'format'       => flaw_pick_value($pod->field('event_format')),
        'type'         => flaw_get_event_type($event_id),
        'game'         => [
            'id'    => $pod->field('event_game.ID'),
            'title' => flaw_pick_value($pod->field('event_game.post_title')),
            'logo'  => flaw_pick_value($pod->field('event_game.game_logo._src')),
        ],
        'thumbnail'    => get_the_post_thumbnail_url($event_id, 'medium'),
        'registration' => [
            'enabled' => (bool) $pod->field('event_registration_enabled'),
            'url'     => flaw_pick_value($pod->field('event_registration_url')),
        ],
        'stream'       => [
            'platform' => flaw_pick_value($pod->field('event_stream_platform')),
            'channel'  => flaw_pick_value($pod->field('event_stream_channel')),
        ],
        'results'      => [
            'placement' => (int) flaw_pick_value($pod->field('event_placement_org')),
            'prize_won' => flaw_pick_value($pod->field('event_prize_won')),
        ],
        'has_vod'      => !empty($pod->field('event_vod_id')),
    ];
}

/**
 * Get team card data from Pods instance
 *
 * @param Pods $pod Pods instance
 * @return array
 */
function flaw_get_team_card_data($pod): array {
    if (!$pod) {
        return [];
    }

    $team_id = $pod->field('ID');

    // Get player count
    $players = flaw_get_team_players($team_id);
    $player_count = $players ? $players->total() : 0;

    return [
        'id'           => $team_id,
        'title'        => flaw_pick_value($pod->field('post_title')),
        'permalink'    => get_permalink($team_id),
        'logo'         => flaw_get_image_url($pod, 'team_logo'),
        'logo_light'   => flaw_get_image_url($pod, 'team_logo_light'),
        'status'       => flaw_pick_value($pod->field('team_status'), 'active'),
        'region'       => flaw_pick_value($pod->field('team_region')),
        'founded'      => flaw_pick_value($pod->field('team_founded')),
        'game'         => [
            'id'    => $pod->field('team_game.ID'),
            'title' => flaw_pick_value($pod->field('team_game.post_title')),
            'logo'  => flaw_pick_value($pod->field('team_game.game_logo._src')),
        ],
        'player_count' => $player_count,
        'socials'      => [
            'twitter' => flaw_pick_value($pod->field('team_social_twitter')),
            'discord' => flaw_pick_value($pod->field('team_social_discord')),
        ],
    ];
}

/**
 * Get player card data from Pods instance
 *
 * @param Pods $pod Pods instance
 * @return array
 */
function flaw_get_player_card_data($pod): array {
    if (!$pod) {
        return [];
    }

    $player_id = $pod->field('ID');

    return [
        'id'          => $player_id,
        'gamertag'    => flaw_pick_value($pod->field('player_gamertag')),
        'real_name'   => flaw_pick_value($pod->field('player_real_name')),
        'permalink'   => get_permalink($player_id),
        'photo'       => flaw_get_image_url($pod, 'player_photo'),
        'role'        => flaw_get_player_role($player_id),
        'status'      => flaw_pick_value($pod->field('player_status'), 'active'),
        'nationality' => flaw_pick_value($pod->field('player_nationality')),
        'jersey'      => flaw_pick_value($pod->field('player_jersey_number')),
        'team'        => [
            'id'    => $pod->field('player_team.ID'),
            'title' => flaw_pick_value($pod->field('player_team.post_title')),
            'logo'  => flaw_pick_value($pod->field('player_team.team_logo._src')),
        ],
        'game'        => [
            'id'    => $pod->field('player_game_primary.ID'),
            'title' => flaw_pick_value($pod->field('player_game_primary.post_title')),
        ],
        'socials'     => [
            'twitter'   => flaw_pick_value($pod->field('player_social_twitter')),
            'twitch'    => flaw_pick_value($pod->field('player_social_twitch')),
            'youtube'   => flaw_pick_value($pod->field('player_social_youtube')),
            'tiktok'    => flaw_pick_value($pod->field('player_social_tiktok')),
            'discord'   => flaw_pick_value($pod->field('player_social_discord')),
            'instagram' => flaw_pick_value($pod->field('player_social_instagram')),
            'blaze'     => flaw_pick_value($pod->field('player_social_blaze')),
        ],
    ];
}

/**
 * Get creator card data from Pods instance
 *
 * @param Pods $pod Pods instance
 * @return array
 */
function flaw_get_creator_card_data($pod): array {
    if (!$pod) {
        return [];
    }

    $creator_id = $pod->field('ID');

    return [
        'id'          => $creator_id,
        'handle'      => flaw_pick_value($pod->field('creator_handle')),
        'permalink'   => get_permalink($creator_id),
        'photo'       => flaw_get_image_url($pod, 'creator_photo'),
        'specialty'   => $pod->field('creator_specialty'), // intentionally array
        'platforms'   => $pod->field('creator_platforms'), // intentionally array
        'status'      => flaw_pick_value($pod->field('creator_status'), 'active'),
        'featured'    => (bool) $pod->field('creator_is_featured'),
        'followers'   => [
            'total'   => (int) flaw_pick_value($pod->field('creator_followers_total')),
            'twitch'  => (int) flaw_pick_value($pod->field('creator_followers_twitch')),
            'youtube' => (int) flaw_pick_value($pod->field('creator_followers_youtube')),
        ],
        'games'       => $pod->field('creator_games'), // intentionally array
        'socials'     => [
            'twitch'    => flaw_pick_value($pod->field('creator_social_twitch')),
            'youtube'   => flaw_pick_value($pod->field('creator_social_youtube')),
            'twitter'   => flaw_pick_value($pod->field('creator_social_twitter')),
            'tiktok'    => flaw_pick_value($pod->field('creator_social_tiktok')),
            'instagram' => flaw_pick_value($pod->field('creator_social_instagram')),
            'discord'   => flaw_pick_value($pod->field('creator_social_discord')),
            'blaze'     => flaw_pick_value($pod->field('creator_social_blaze')),
        ],
    ];
}

/**
 * Get game card data from Pods instance
 *
 * @param Pods $pod Pods instance
 * @return array
 */
function flaw_get_game_card_data($pod): array {
    if (!$pod) {
        return [];
    }

    $game_id = $pod->field('ID');

    return [
        'id'         => $game_id,
        'title'      => flaw_pick_value($pod->field('post_title')),
        'slug'       => flaw_pick_value($pod->field('post_name')),
        'permalink'  => get_permalink($game_id),
        'logo'       => flaw_get_image_url($pod, 'game_logo'),
        'cover'      => flaw_get_image_url($pod, 'game_cover'),
        'studio'     => flaw_pick_value($pod->field('game_studio')),
        'stage'      => flaw_pick_value($pod->field('game_stage')),
        'genre'      => flaw_get_game_genre($game_id),
        'platforms'  => $pod->field('game_platforms'), // intentionally array
        'blockchain' => flaw_pick_value($pod->field('game_blockchain')),
        'flaw_status'=> flaw_pick_value($pod->field('game_flaw_status')),
        'needs'      => flaw_pick_value($pod->field('game_flaw_needs')),
        'featured'   => (bool) $pod->field('game_is_featured'),
        'url'        => flaw_pick_value($pod->field('game_url')),
    ];
}

/**
 * Get partner card data from Pods instance
 *
 * @param Pods $pod Pods instance
 * @return array
 */
function flaw_get_partner_card_data($pod): array {
    if (!$pod) {
        return [];
    }

    $partner_id = $pod->field('ID');

    return [
        'id'          => $partner_id,
        'title'       => flaw_pick_value($pod->field('post_title')),
        'permalink'   => get_permalink($partner_id),
        'logo'        => flaw_get_image_url($pod, 'partner_logo'),
        'logo_light'  => flaw_get_image_url($pod, 'partner_logo_light'),
        'type'        => flaw_pick_value($pod->field('partner_type')),
        'tier'        => flaw_get_partner_tier($partner_id),
        'status'      => flaw_pick_value($pod->field('partner_status'), 'active'),
        'website'     => flaw_pick_value($pod->field('partner_website')),
        'promo'       => [
            'code' => flaw_pick_value($pod->field('partner_promo_code')),
            'url'  => flaw_pick_value($pod->field('partner_promo_url')),
        ],
        'description' => flaw_pick_value($pod->field('partner_description')),
    ];
}

/**
 * Universal card renderer
 *
 * @param string $type Card type (event, team, player, creator, partner, game)
 * @param array $data Card data array
 * @param array $options Rendering options
 */
function flaw_render_card(string $type, array $data, array $options = []): void {
    $valid_types = ['event', 'team', 'player', 'creator', 'partner', 'game'];

    if (!in_array($type, $valid_types, true)) {
        return;
    }

    $variant = $options['variant'] ?? 'default';
    $template = "template-parts/cards/card-{$type}";

    if ($variant !== 'default') {
        $variant_template = "template-parts/cards/card-{$type}-{$variant}";
        if (locate_template($variant_template . '.php')) {
            $template = $variant_template;
        }
    }

    get_template_part($template, null, array_merge($options, ['data' => $data]));
}

/**
 * Render card from Pods object
 *
 * @param string $type Card type
 * @param Pods $pod Pods instance
 * @param array $options Rendering options
 */
function flaw_render_card_from_pod(string $type, $pod, array $options = []): void {
    $data_fn = "flaw_get_{$type}_card_data";

    if (!function_exists($data_fn)) {
        return;
    }

    $data = $data_fn($pod);
    flaw_render_card($type, $data, $options);
}
