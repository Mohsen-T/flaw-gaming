<?php
/**
 * REST API Endpoints
 */

if (!defined("ABSPATH")) exit;

class FLAW_REST_API {
    public static function register_routes() {
        register_rest_route("flaw/v1", "/events", [
            "methods" => "GET",
            "callback" => [__CLASS__, "get_events"],
            "permission_callback" => "__return_true",
        ]);

        register_rest_route("flaw/v1", "/teams", [
            "methods" => "GET", 
            "callback" => [__CLASS__, "get_teams"],
            "permission_callback" => "__return_true",
        ]);

        register_rest_route("flaw/v1", "/twitch-status", [
            "methods" => "GET",
            "callback" => [__CLASS__, "get_twitch_status"],
            "permission_callback" => "__return_true",
        ]);
    }

    public static function get_events($request) {
        $state = $request->get_param("state") ?: "upcoming";
        $limit = $request->get_param("limit") ?: 10;
        
        $events = flaw_get_events_by_state($state, $limit);
        $data = [];
        
        while ($events->fetch()) {
            $data[] = flaw_get_event_card_data($events->field("ID"));
        }
        
        return rest_ensure_response($data);
    }

    public static function get_teams($request) {
        $limit = $request->get_param("limit") ?: -1;
        $teams = flaw_get_active_teams($limit);
        $data = [];
        
        while ($teams->fetch()) {
            $data[] = [
                "id" => $teams->field("ID"),
                "title" => $teams->field("post_title"),
                "permalink" => get_permalink($teams->field("ID")),
                "thumbnail" => get_the_post_thumbnail_url($teams->field("ID"), "team-logo"),
            ];
        }
        
        return rest_ensure_response($data);
    }

    public static function get_twitch_status($request) {
        $channel = sanitize_text_field($request->get_param("channel"));
        if (empty($channel)) {
            return new WP_Error("missing_channel", "Channel parameter required", ["status" => 400]);
        }
        
        // In production, this would call the Twitch API
        // For now, return a placeholder response
        return rest_ensure_response([
            "channel" => $channel,
            "is_live" => false,
            "title" => "",
            "viewers" => 0,
        ]);
    }
}