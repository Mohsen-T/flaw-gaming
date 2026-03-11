<?php
/**
 * REST API Endpoints
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST routes
 */
function flaw_register_rest_routes() {
    // Events endpoints
    register_rest_route('flaw/v1', '/events/upcoming', [
        'methods'             => 'GET',
        'callback'            => 'flaw_rest_get_upcoming_events',
        'permission_callback' => '__return_true',
        'args'                => [
            'limit' => [
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ],
            'game_id' => [
                'default'           => null,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);

    register_rest_route('flaw/v1', '/events/live', [
        'methods'             => 'GET',
        'callback'            => 'flaw_rest_get_live_events',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('flaw/v1', '/events/past', [
        'methods'             => 'GET',
        'callback'            => 'flaw_rest_get_past_events',
        'permission_callback' => '__return_true',
        'args'                => [
            'limit'   => ['default' => 12, 'sanitize_callback' => 'absint'],
            'page'    => ['default' => 1, 'sanitize_callback' => 'absint'],
            'year'    => ['default' => null, 'sanitize_callback' => 'absint'],
            'game_id' => ['default' => null, 'sanitize_callback' => 'absint'],
        ],
    ]);

    register_rest_route('flaw/v1', '/event/(?P<id>\d+)/section/(?P<section>\w+)', [
        'methods'             => 'GET',
        'callback'            => 'flaw_rest_get_event_section',
        'permission_callback' => '__return_true',
        'args'                => [
            'id'      => ['required' => true, 'sanitize_callback' => 'absint'],
            'section' => ['required' => true, 'sanitize_callback' => 'sanitize_key'],
        ],
    ]);

    // Twitch status proxy
    register_rest_route('flaw/v1', '/twitch/status', [
        'methods'             => 'POST',
        'callback'            => 'flaw_rest_twitch_status',
        'permission_callback' => '__return_true',
    ]);

    // Homepage data
    register_rest_route('flaw/v1', '/homepage', [
        'methods'             => 'GET',
        'callback'            => 'flaw_rest_get_homepage_data',
        'permission_callback' => '__return_true',
    ]);
}

/**
 * Get upcoming events
 */
function flaw_rest_get_upcoming_events(WP_REST_Request $request) {
    $limit = $request->get_param('limit');
    $game_id = $request->get_param('game_id');

    $events = flaw_get_upcoming_events($limit, $game_id);

    if (!$events) {
        return rest_ensure_response([]);
    }

    $result = [];
    while ($events->fetch()) {
        $result[] = flaw_get_event_card_data($events);
    }

    return rest_ensure_response($result);
}

/**
 * Get live events
 */
function flaw_rest_get_live_events(WP_REST_Request $request) {
    $events = flaw_get_live_events();

    if (!$events) {
        return rest_ensure_response([]);
    }

    $result = [];
    while ($events->fetch()) {
        $data = flaw_get_event_card_data($events);
        $data['stream'] = [
            'platform' => $events->field('event_stream_platform'),
            'channel'  => $events->field('event_stream_channel'),
        ];
        $result[] = $data;
    }

    return rest_ensure_response($result);
}

/**
 * Get past events with pagination
 */
function flaw_rest_get_past_events(WP_REST_Request $request) {
    $limit = $request->get_param('limit');
    $page = $request->get_param('page');
    $offset = ($page - 1) * $limit;

    $filters = [
        'year'    => $request->get_param('year'),
        'game_id' => $request->get_param('game_id'),
    ];

    $events = flaw_get_past_events($limit, $offset, array_filter($filters));

    if (!$events) {
        return rest_ensure_response([
            'events'      => [],
            'total'       => 0,
            'total_pages' => 0,
        ]);
    }

    $result = [];
    while ($events->fetch()) {
        $result[] = flaw_get_event_card_data($events);
    }

    return rest_ensure_response([
        'events'      => $result,
        'total'       => $events->total_found(),
        'total_pages' => ceil($events->total_found() / $limit),
        'page'        => $page,
    ]);
}

/**
 * Get event section HTML
 */
function flaw_rest_get_event_section(WP_REST_Request $request) {
    $event_id = $request->get_param('id');
    $section = $request->get_param('section');

    $renderer = new FLAW_Event_Renderer($event_id);

    return rest_ensure_response([
        'state'   => $renderer->state(),
        'visible' => $renderer->should_render($section),
        'html'    => $renderer->get_section_html($section),
    ]);
}

/**
 * Twitch status proxy
 */
function flaw_rest_twitch_status(WP_REST_Request $request) {
    $channels = $request->get_param('channels');

    if (empty($channels) || !is_array($channels)) {
        return new WP_Error('invalid_request', 'Channels array required', ['status' => 400]);
    }

    // Limit to prevent abuse
    $channels = array_slice($channels, 0, 10);
    $channels = array_map('sanitize_text_field', $channels);

    // Get Twitch credentials
    if (function_exists('flaw_get_twitch_credentials')) {
        $credentials = flaw_get_twitch_credentials();
        $client_id = $credentials['client_id'];
        $client_secret = $credentials['client_secret'];
    } else {
        $client_id = defined('TWITCH_CLIENT_ID') ? TWITCH_CLIENT_ID : get_option('flaw_twitch_client_id');
        $client_secret = defined('TWITCH_CLIENT_SECRET') ? TWITCH_CLIENT_SECRET : get_option('flaw_twitch_client_secret');
    }

    if (!$client_id || !$client_secret) {
        return new WP_Error('config_error', 'Twitch credentials not configured', ['status' => 500]);
    }

    // Get access token (cached)
    $access_token = flaw_get_twitch_token($client_id, $client_secret);

    if (is_wp_error($access_token)) {
        return $access_token;
    }

    // Build query string for Twitch API
    $query_params = [];
    foreach ($channels as $channel) {
        $query_params[] = 'user_login=' . urlencode($channel);
    }
    $query = implode('&', $query_params);

    // Fetch live streams
    $response = wp_remote_get("https://api.twitch.tv/helix/streams?{$query}", [
        'headers' => [
            'Client-ID'     => $client_id,
            'Authorization' => "Bearer {$access_token}",
        ],
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['data'])) {
        return new WP_Error('api_error', 'Invalid Twitch response', ['status' => 502]);
    }

    // Build results map
    $results = [];

    // Initialize all channels as offline
    foreach ($channels as $channel) {
        $results[strtolower($channel)] = ['live' => false];
    }

    // Mark live channels
    foreach ($body['data'] as $stream) {
        $channel = strtolower($stream['user_login']);
        $results[$channel] = [
            'live'          => true,
            'viewer_count'  => $stream['viewer_count'],
            'game_name'     => $stream['game_name'],
            'title'         => $stream['title'],
            'started_at'    => $stream['started_at'],
            'thumbnail_url' => str_replace(
                ['{width}', '{height}'],
                ['440', '248'],
                $stream['thumbnail_url']
            ),
        ];
    }

    return rest_ensure_response($results);
}

/**
 * Get Twitch OAuth token (cached)
 */
function flaw_get_twitch_token($client_id, $client_secret) {
    $cache_key = 'flaw_twitch_token';
    $token = get_transient($cache_key);

    if ($token) {
        return $token;
    }

    $response = wp_remote_post('https://id.twitch.tv/oauth2/token', [
        'body' => [
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'grant_type'    => 'client_credentials',
        ],
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($body['access_token'])) {
        return new WP_Error('auth_error', 'Failed to get Twitch token', ['status' => 401]);
    }

    // Cache token (expires slightly before actual expiry)
    $expires_in = ($body['expires_in'] ?? 3600) - 300;
    set_transient($cache_key, $body['access_token'], $expires_in);

    return $body['access_token'];
}

/**
 * Get homepage data
 */
function flaw_rest_get_homepage_data(WP_REST_Request $request) {
    return rest_ensure_response(flaw_get_homepage_data());
}
