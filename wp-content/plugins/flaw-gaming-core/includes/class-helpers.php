<?php
/**
 * Helper Functions for Theme Integration
 */

if (!defined("ABSPATH")) exit;

class FLAW_Helpers {}

function flaw_get_live_events($limit = -1) { return flaw_get_events_by_state("live", $limit); }
function flaw_get_upcoming_events($limit = 4) { return flaw_get_events_by_state("upcoming", $limit); }
function flaw_get_past_events($limit = 10) { return flaw_get_events_by_state("completed", $limit); }

function flaw_get_events_by_state($state = "upcoming", $limit = -1) {
    $now = current_time("mysql");
    $meta_query = [];
    switch ($state) {
        case "live":
            $meta_query = ["relation" => "AND", ["key" => "event_date_start", "value" => $now, "compare" => "<=", "type" => "DATETIME"], ["key" => "event_date_end", "value" => $now, "compare" => ">=", "type" => "DATETIME"]];
            break;
        case "upcoming":
            $meta_query = [["key" => "event_date_start", "value" => $now, "compare" => ">", "type" => "DATETIME"]];
            break;
        case "completed":
            $meta_query = [["key" => "event_date_end", "value" => $now, "compare" => "<", "type" => "DATETIME"]];
            break;
    }
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "event", "posts_per_page" => $limit, "meta_query" => $meta_query, "orderby" => "meta_value", "meta_key" => "event_date_start", "order" => $state === "completed" ? "DESC" : "ASC"]));
}

// flaw_event_get_state() and flaw_event_badge() are in inc/class-event-state-manager.php

function flaw_get_active_teams($game_id = null, $limit = -1) {
    $args = ["post_type" => "team", "posts_per_page" => $limit];
    if ($game_id) {
        $args["meta_query"] = [["key" => "team_game", "value" => $game_id]];
    }
    return new FLAW_Query_Wrapper(new WP_Query($args));
}

function flaw_get_all_teams($limit = -1) {
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "team", "posts_per_page" => $limit]));
}

function flaw_get_team_players($team_id) {
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "player", "posts_per_page" => -1, "meta_query" => [["key" => "player_team", "value" => $team_id]]]));
}

function flaw_get_games_with_active_teams() {
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "game", "posts_per_page" => -1]));
}

function flaw_get_featured_creators($limit = 4) {
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "creator", "posts_per_page" => $limit]));
}

function flaw_get_creators($limit = -1) {
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "creator", "posts_per_page" => $limit]));
}

function flaw_get_featured_games($limit = 6) {
    return new FLAW_Query_Wrapper(new WP_Query(["post_type" => "game", "posts_per_page" => $limit]));
}

function flaw_get_partners_by_tier() {
    $tiers = ["platinum", "gold", "silver", "bronze"];
    $result = [];
    foreach ($tiers as $tier) {
        $query = new WP_Query(["post_type" => "partner", "posts_per_page" => -1, "tax_query" => [["taxonomy" => "partner_tier", "field" => "slug", "terms" => $tier]]]);
        $result[$tier] = [];
        while ($query->have_posts()) { 
            $query->the_post(); 
            $result[$tier][] = ["id" => get_the_ID(), "title" => get_the_title(), "logo" => get_the_post_thumbnail_url(get_the_ID(), "partner-logo"), "website" => get_post_meta(get_the_ID(), "partner_website", true)]; 
        }
        wp_reset_postdata();
    }
    return $result;
}

function flaw_format_date_range($start, $end) {
    if (empty($start)) return "";
    $start_ts = strtotime($start);
    $end_ts = $end ? strtotime($end) : null;
    if (!$end_ts || date("Y-m-d", $start_ts) === date("Y-m-d", $end_ts)) return date_i18n("M j, Y", $start_ts);
    return date_i18n("M j", $start_ts) . " - " . date_i18n("M j, Y", $end_ts);
}

function flaw_format_placement($n) { $e = ["th","st","nd","rd","th","th","th","th","th","th"]; return (($n%100)>=11 && ($n%100)<=13) ? $n."th" : $n.$e[$n%10]; }
function flaw_format_number($n) { if ($n>=1000000) return round($n/1000000,1)."M"; if ($n>=1000) return round($n/1000,1)."K"; return number_format($n); }
function flaw_get_flag_url($code) { return "https://flagcdn.com/w40/".strtolower($code).".png"; }

function flaw_get_game_genre($game_id) {
    $terms = wp_get_post_terms($game_id, 'game_genre', ['fields' => 'names']);
    return (!empty($terms) && !is_wp_error($terms)) ? implode(', ', $terms) : '';
}

function flaw_get_player_role($player_id) {
    $terms = wp_get_post_terms($player_id, 'player_role', ['fields' => 'names']);
    return (!empty($terms) && !is_wp_error($terms)) ? implode(', ', $terms) : '';
}

function flaw_get_event_type($event_id) {
    $terms = wp_get_post_terms($event_id, 'event_type', ['fields' => 'names']);
    return (!empty($terms) && !is_wp_error($terms)) ? implode(', ', $terms) : '';
}

function flaw_get_partner_tier($partner_id) {
    $terms = wp_get_post_terms($partner_id, 'partner_tier', ['fields' => 'names']);
    return (!empty($terms) && !is_wp_error($terms)) ? implode(', ', $terms) : '';
}

function flaw_get_image_url($pod, string $field, string $size = 'full'): ?string {
    if (!$pod) {
        return null;
    }

    $image = $pod->field($field);

    if (empty($image)) {
        return null;
    }

    // If it's an array with ID
    if (is_array($image) && isset($image['ID'])) {
        return wp_get_attachment_image_url($image['ID'], $size);
    }

    // If it's just an ID
    if (is_numeric($image)) {
        return wp_get_attachment_image_url($image, $size);
    }

    // Try the _src variant
    $src = $pod->field($field . '._src');
    return $src ?: null;
}

/**
 * Safely extract a string from a Pods pick field value (may be array or string)
 */
function flaw_pick_value($value, string $default = ''): string {
    if (is_array($value)) {
        return (string) ($value[0] ?? $default);
    }
    return (string) ($value ?: $default);
}

class FLAW_Query_Wrapper {
    private $query;
    private $current = null;
    public function __construct(WP_Query $q) { $this->query = $q; }
    public function total() { return $this->query->found_posts; }
    public function total_found() { return $this->query->found_posts; }
    public function max_num_pages() { return $this->query->max_num_pages; }
    public function have_posts() { return $this->query->have_posts(); }
    public function fetch() { if ($this->query->have_posts()) { $this->query->the_post(); $this->current = get_post(); return true; } wp_reset_postdata(); return false; }
    public function field($n) { if (!$this->current) return null; switch($n) { case "ID": return $this->current->ID; case "post_title": return $this->current->post_title; default: return get_post_meta($this->current->ID, $n, true); } }
    public function reset() { $this->query->rewind_posts(); $this->current = null; }
    public function get_query() { return $this->query; }
}