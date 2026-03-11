<?php
/**
 * Event State Manager
 *
 * Handles event state detection and field visibility rules.
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event State Manager Class
 */
class FLAW_Event_State_Manager {

    // State constants
    const UPCOMING   = 'upcoming';
    const LIVE       = 'live';
    const COMPLETED  = 'completed';
    const CANCELLED  = 'cancelled';

    /**
     * Field group visibility matrix
     */
    private static array $field_groups = [
        self::UPCOMING => [
            'header',
            'details',
            'registration',
            'teams',
            'partners',
        ],
        self::LIVE => [
            'header',
            'details',
            'broadcast',
            'teams',
            'partners',
        ],
        self::COMPLETED => [
            'header',
            'details',
            'results',
            'statistics',
            'media',
            'teams',
            'partners',
        ],
        self::CANCELLED => [
            'header',
            'details',
        ],
    ];

    /**
     * Get event state with optional auto-detection
     *
     * @param int $event_id Event ID
     * @param bool $auto_detect Whether to auto-detect based on dates
     * @return string
     */
    public static function get_state(int $event_id, bool $auto_detect = true): string {
        if (!flaw_is_pods_active()) {
            return self::UPCOMING;
        }

        $pod = pods('event', $event_id);

        if (!$pod->exists()) {
            return self::UPCOMING;
        }

        $manual_status = flaw_pick_value($pod->field('event_status'));

        // Manual status always takes precedence when explicitly set
        if (!empty($manual_status) && in_array($manual_status, self::get_all_states(), true)) {
            return $manual_status;
        }

        // Auto-detect based on dates as fallback
        if ($auto_detect) {
            return self::compute_state_from_dates($pod);
        }

        return self::UPCOMING;
    }

    /**
     * Compute state from event dates
     *
     * @param Pods $pod Pods instance
     * @return string
     */
    private static function compute_state_from_dates($pod): string {
        $now = current_time('timestamp');

        $start = flaw_pick_value($pod->field('event_date_start'));
        $end   = flaw_pick_value($pod->field('event_date_end'));

        $start_ts = $start ? strtotime($start) : null;
        $end_ts   = $end ? strtotime($end) : ($start_ts ? $start_ts + 86400 : null);

        if (!$start_ts) {
            return self::UPCOMING;
        }

        if ($now < $start_ts) {
            return self::UPCOMING;
        }

        if ($now >= $start_ts && $now <= $end_ts) {
            return self::LIVE;
        }

        return self::COMPLETED;
    }

    /**
     * Check if a field group should be visible
     *
     * @param string $group Group name
     * @param string $state Event state
     * @return bool
     */
    public static function is_visible(string $group, string $state): bool {
        $visible = self::$field_groups[$state] ?? [];
        return in_array($group, $visible, true);
    }

    /**
     * Get all visible groups for a state
     *
     * @param string $state Event state
     * @return array
     */
    public static function get_visible_groups(string $state): array {
        return self::$field_groups[$state] ?? [];
    }

    /**
     * Check if event has required data for a section
     *
     * @param Pods $pod Pods instance
     * @param string $section Section name
     * @return bool
     */
    public static function has_section_data($pod, string $section): bool {
        if (!$pod) {
            return false;
        }

        switch ($section) {
            case 'registration':
                return (bool) $pod->field('event_registration_enabled');

            case 'broadcast':
                return !empty($pod->field('event_stream_channel'));

            case 'results':
                return !empty($pod->field('event_winner_team')) ||
                       !empty($pod->field('event_placement_org'));

            case 'statistics':
                return !empty($pod->field('event_stats_matches_played')) ||
                       !empty($pod->field('event_stats_json'));

            case 'media':
                return !empty($pod->field('event_vod_id')) ||
                       !empty($pod->field('event_photos_gallery'));

            case 'teams':
                return !empty($pod->field('event_teams_participating'));

            case 'partners':
                return !empty($pod->field('event_partners'));

            default:
                return true;
        }
    }

    /**
     * Get state badge HTML
     *
     * @param string $state Event state
     * @return string
     */
    public static function get_state_badge(string $state): string {
        $labels = [
            self::UPCOMING   => 'Upcoming',
            self::LIVE       => 'Live Now',
            self::COMPLETED  => 'Completed',
            self::CANCELLED  => 'Cancelled',
        ];

        $label = $labels[$state] ?? ucfirst($state);

        return sprintf(
            '<span class="event-badge event-badge--%s">%s</span>',
            esc_attr($state),
            esc_html($label)
        );
    }

    /**
     * Get all valid states
     *
     * @return array
     */
    public static function get_all_states(): array {
        return [
            self::UPCOMING,
            self::LIVE,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }

    /**
     * Check if state is terminal (won't change automatically)
     *
     * @param string $state Event state
     * @return bool
     */
    public static function is_terminal_state(string $state): bool {
        return in_array($state, [self::COMPLETED, self::CANCELLED], true);
    }
}

// ═══════════════════════════════════════════════════════════════
// HELPER FUNCTIONS
// ═══════════════════════════════════════════════════════════════

/**
 * Get event state
 *
 * @param int|null $event_id Event ID (defaults to current post)
 * @return string
 */
function flaw_event_get_state(?int $event_id = null): string {
    $event_id = $event_id ?: get_the_ID();
    return FLAW_Event_State_Manager::get_state($event_id);
}

/**
 * Check if event is in a specific state
 *
 * @param string $state State to check
 * @param int|null $event_id Event ID
 * @return bool
 */
function flaw_event_is(string $state, ?int $event_id = null): bool {
    return flaw_event_get_state($event_id) === $state;
}

/**
 * Check if event is upcoming
 *
 * @param int|null $event_id Event ID
 * @return bool
 */
function flaw_event_is_upcoming(?int $event_id = null): bool {
    return flaw_event_is(FLAW_Event_State_Manager::UPCOMING, $event_id);
}

/**
 * Check if event is live
 *
 * @param int|null $event_id Event ID
 * @return bool
 */
function flaw_event_is_live(?int $event_id = null): bool {
    return flaw_event_is(FLAW_Event_State_Manager::LIVE, $event_id);
}

/**
 * Check if event is completed
 *
 * @param int|null $event_id Event ID
 * @return bool
 */
function flaw_event_is_completed(?int $event_id = null): bool {
    return flaw_event_is(FLAW_Event_State_Manager::COMPLETED, $event_id);
}

/**
 * Get event state badge HTML
 *
 * @param int|null $event_id Event ID
 * @return string
 */
function flaw_event_badge(?int $event_id = null): string {
    $state = flaw_event_get_state($event_id);
    return FLAW_Event_State_Manager::get_state_badge($state);
}
