<?php
/**
 * Query Functions
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

// ═══════════════════════════════════════════════════════════════
// EVENT QUERIES
// ═══════════════════════════════════════════════════════════════

/**
 * Get upcoming events
 *
 * @param int $limit Number of events
 * @param int|null $game_id Filter by game ID
 * @return Pods|null
 */
function flaw_get_upcoming_events(int $limit = 10, ?int $game_id = null) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $now = current_time('mysql');

    $where = [
        "event_status = 'upcoming'",
        "event_date_start >= '{$now}'",
    ];

    if ($game_id) {
        $where[] = "event_game.ID = " . (int) $game_id;
    }

    return pods('event', [
        'where'   => implode(' AND ', $where),
        'orderby' => 'event_date_start ASC',
        'limit'   => $limit,
    ]);
}

/**
 * Get live events
 *
 * @return Pods|null
 */
function flaw_get_live_events() {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $now = current_time('mysql');

    return pods('event', [
        'where' => "
            event_status = 'live'
            OR (
                event_status = 'upcoming'
                AND event_date_start <= '{$now}'
                AND (event_date_end IS NULL OR event_date_end >= '{$now}')
            )
        ",
        'orderby' => 'event_date_start DESC',
        'limit'   => 5,
    ]);
}

/**
 * Get past/completed events
 *
 * @param int $limit Number of events
 * @param int $offset Offset for pagination
 * @param array $filters Additional filters
 * @return Pods|null
 */
function flaw_get_past_events(int $limit = 12, int $offset = 0, array $filters = []) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $where = ["event_status = 'completed'"];

    if (!empty($filters['game_id'])) {
        $where[] = "event_game.ID = " . (int) $filters['game_id'];
    }

    if (!empty($filters['year'])) {
        $where[] = "YEAR(event_date_start) = " . (int) $filters['year'];
    }

    if (!empty($filters['has_vod'])) {
        $where[] = "(event_vod_id IS NOT NULL AND event_vod_id != '')";
    }

    return pods('event', [
        'where'   => implode(' AND ', $where),
        'orderby' => 'event_date_start DESC',
        'limit'   => $limit,
        'offset'  => $offset,
    ]);
}

/**
 * Get events for a specific team
 *
 * @param int $team_id Team ID
 * @param string $status Event status filter
 * @param int $limit Number of events
 * @return Pods|null
 */
function flaw_get_team_events(int $team_id, string $status = 'completed', int $limit = 5) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $where = ["event_teams_participating.ID = {$team_id}"];

    if ($status) {
        $where[] = "event_status = '{$status}'";
    }

    return pods('event', [
        'where'   => implode(' AND ', $where),
        'orderby' => 'event_date_start DESC',
        'limit'   => $limit,
    ]);
}

// ═══════════════════════════════════════════════════════════════
// TEAM QUERIES
// ═══════════════════════════════════════════════════════════════

/**
 * Get active teams
 *
 * @param string|null $game_slug Filter by game slug
 * @return Pods|null
 */
function flaw_get_active_teams(?string $game_slug = null) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $where = ["team_status = 'active'"];

    if ($game_slug && $game_slug !== 'all') {
        $where[] = "team_game.post_name = '" . sanitize_text_field($game_slug) . "'";
    }

    return pods('team', [
        'where'   => implode(' AND ', $where),
        'orderby' => 'team_game.game_priority ASC, post_title ASC',
        'limit'   => -1,
    ]);
}

/**
 * Get players for a team
 *
 * @param int $team_id Team ID
 * @param string $status Player status filter
 * @return Pods|null
 */
function flaw_get_team_players(int $team_id, string $status = 'active') {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $where = ["player_team.ID = {$team_id}"];

    if ($status !== 'all') {
        $where[] = "player_status = '{$status}'";
    }

    return pods('player', [
        'where'   => implode(' AND ', $where),
        'orderby' => 'player_gamertag ASC',
        'limit'   => -1,
    ]);
}

/**
 * Get games that have active teams
 *
 * @return Pods|null
 */
function flaw_get_games_with_active_teams() {
    if (!flaw_is_pods_active()) {
        return null;
    }

    // First get all games that have active teams
    global $wpdb;

    $game_ids = $wpdb->get_col("
        SELECT DISTINCT pm.meta_value
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'team_game'
        INNER JOIN {$wpdb->postmeta} status ON p.ID = status.post_id AND status.meta_key = 'team_status'
        WHERE p.post_type = 'team'
        AND p.post_status = 'publish'
        AND status.meta_value = 'active'
    ");

    if (empty($game_ids)) {
        return pods('game', ['limit' => 0]);
    }

    return pods('game', [
        'where'   => "ID IN (" . implode(',', array_map('intval', $game_ids)) . ")",
        'orderby' => 'game_priority ASC',
        'limit'   => -1,
    ]);
}

// ═══════════════════════════════════════════════════════════════
// PLAYER QUERIES
// ═══════════════════════════════════════════════════════════════

/**
 * Get players with filters
 *
 * @param array $args Query arguments
 * @return Pods|null
 */
function flaw_get_players(array $args = []) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $defaults = [
        'status'  => 'active',
        'game_id' => null,
        'team_id' => null,
        'limit'   => 24,
        'offset'  => 0,
        'orderby' => 'player_gamertag ASC',
    ];

    $args = wp_parse_args($args, $defaults);

    $where = [];

    if ($args['status'] !== 'all') {
        $where[] = "player_status = '{$args['status']}'";
    }

    if ($args['game_id']) {
        $where[] = "player_game_primary.ID = " . (int) $args['game_id'];
    }

    if ($args['team_id']) {
        $where[] = "player_team.ID = " . (int) $args['team_id'];
    }

    return pods('player', [
        'where'   => !empty($where) ? implode(' AND ', $where) : null,
        'orderby' => $args['orderby'],
        'limit'   => $args['limit'],
        'offset'  => $args['offset'],
    ]);
}

// ═══════════════════════════════════════════════════════════════
// CREATOR QUERIES
// ═══════════════════════════════════════════════════════════════

/**
 * Get creators with filters
 *
 * @param array $args Query arguments
 * @return Pods|null
 */
function flaw_get_creators(array $args = []) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $defaults = [
        'status'    => 'active',
        'specialty' => null,
        'platform'  => null,
        'game_id'   => null,
        'featured'  => null,
        'limit'     => 12,
        'offset'    => 0,
        'orderby'   => 'creator_followers_total DESC',
    ];

    $args = wp_parse_args($args, $defaults);

    $where = ["creator_status = '{$args['status']}'"];

    if ($args['specialty']) {
        $where[] = "creator_specialty LIKE '%\"{$args['specialty']}\"%'";
    }

    if ($args['platform']) {
        $where[] = "creator_platforms LIKE '%\"{$args['platform']}\"%'";
    }

    if ($args['game_id']) {
        $where[] = "creator_games.ID = " . (int) $args['game_id'];
    }

    if ($args['featured']) {
        $where[] = "creator_is_featured = 1";
    }

    return pods('creator', [
        'where'   => implode(' AND ', $where),
        'orderby' => $args['orderby'],
        'limit'   => $args['limit'],
        'offset'  => $args['offset'],
    ]);
}

/**
 * Get featured creators
 *
 * @param int $limit Number of creators
 * @return Pods|null
 */
function flaw_get_featured_creators(int $limit = 6) {
    return flaw_get_creators([
        'featured' => true,
        'limit'    => $limit,
    ]);
}

// ═══════════════════════════════════════════════════════════════
// GAME QUERIES
// ═══════════════════════════════════════════════════════════════

/**
 * Get featured games
 *
 * @param int $limit Number of games
 * @return Pods|null
 */
function flaw_get_featured_games(int $limit = 6) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    return pods('game', [
        'where'   => "game_is_featured = 1 AND game_flaw_status != 'inactive'",
        'orderby' => 'game_priority ASC, post_title ASC',
        'limit'   => $limit,
    ]);
}

/**
 * Get games by FLAW status
 *
 * @param array $statuses Status values to include
 * @return Pods|null
 */
function flaw_get_games_by_status(array $statuses = ['active', 'recruiting']) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    $status_list = "'" . implode("','", array_map('sanitize_text_field', $statuses)) . "'";

    return pods('game', [
        'where'   => "game_flaw_status IN ({$status_list})",
        'orderby' => 'game_priority ASC',
        'limit'   => -1,
    ]);
}

// ═══════════════════════════════════════════════════════════════
// PARTNER QUERIES
// ═══════════════════════════════════════════════════════════════

/**
 * Get partners grouped by tier
 *
 * @return array
 */
function flaw_get_partners_by_tier(): array {
    if (!flaw_is_pods_active()) {
        return [];
    }

    $tiers = ['platinum', 'gold', 'silver', 'bronze'];
    $result = [];

    foreach ($tiers as $tier) {
        $partners = pods('partner', [
            'where'   => "partner_status = 'active' AND partner_tier = '{$tier}'",
            'orderby' => 'partner_priority ASC, post_title ASC',
            'limit'   => -1,
        ]);

        $result[$tier] = [];

        while ($partners->fetch()) {
            $result[$tier][] = [
                'id'         => $partners->field('ID'),
                'title'      => $partners->field('post_title'),
                'logo'       => flaw_get_image_url($partners, 'partner_logo'),
                'logo_light' => flaw_get_image_url($partners, 'partner_logo_light'),
                'type'       => $partners->field('partner_type'),
                'website'    => $partners->field('partner_website'),
                'promo_code' => $partners->field('partner_promo_code'),
                'promo_url'  => $partners->field('partner_promo_url'),
            ];
        }
    }

    return $result;
}

/**
 * Get active partners
 *
 * @param int $limit Number of partners
 * @return Pods|null
 */
function flaw_get_active_partners(int $limit = -1) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    return pods('partner', [
        'where'   => "partner_status = 'active'",
        'orderby' => 'partner_tier ASC, partner_priority ASC',
        'limit'   => $limit,
    ]);
}

// ═══════════════════════════════════════════════════════════════
// HOMEPAGE DATA
// ═══════════════════════════════════════════════════════════════

/**
 * Get aggregated homepage data with caching
 *
 * @return array
 */
function flaw_get_homepage_data(): array {
    $cache_key = 'flaw_homepage_data';
    $cached = wp_cache_get($cache_key, 'flaw');

    if (false !== $cached) {
        return $cached;
    }

    $data = [
        'featured_games'    => [],
        'featured_creators' => [],
        'partners'          => [],
        'live_events'       => [],
        'upcoming_events'   => [],
    ];

    // Featured games
    $games = flaw_get_featured_games(4);
    if ($games) {
        while ($games->fetch()) {
            $data['featured_games'][] = flaw_get_game_card_data($games);
        }
    }

    // Featured creators
    $creators = flaw_get_featured_creators(6);
    if ($creators) {
        while ($creators->fetch()) {
            $data['featured_creators'][] = flaw_get_creator_card_data($creators);
        }
    }

    // Partners (platinum + gold only for homepage)
    $partners_by_tier = flaw_get_partners_by_tier();
    $data['partners'] = array_merge(
        $partners_by_tier['platinum'] ?? [],
        $partners_by_tier['gold'] ?? []
    );

    // Live events
    $live = flaw_get_live_events();
    if ($live) {
        while ($live->fetch()) {
            $data['live_events'][] = flaw_get_event_card_data($live);
        }
    }

    // Upcoming events
    $upcoming = flaw_get_upcoming_events(4);
    if ($upcoming) {
        while ($upcoming->fetch()) {
            $data['upcoming_events'][] = flaw_get_event_card_data($upcoming);
        }
    }

    // Cache for 10 minutes
    wp_cache_set($cache_key, $data, 'flaw', 600);

    return $data;
}

/**
 * Clear homepage cache on relevant post updates
 */
add_action('pods_api_post_save_pod_item', function($pieces, $is_new, $id) {
    $pod_name = $pieces['pod']['name'] ?? '';

    if (in_array($pod_name, ['game', 'creator', 'partner', 'event'])) {
        wp_cache_delete('flaw_homepage_data', 'flaw');
    }
}, 10, 3);
