<?php
/**
 * Event Content Renderer
 *
 * Handles conditional rendering of event sections.
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event Renderer Class
 */
class FLAW_Event_Renderer {

    /**
     * @var Pods
     */
    private $pod;

    /**
     * @var string
     */
    private string $state;

    /**
     * @var int
     */
    private int $event_id;

    /**
     * Constructor
     *
     * @param int $event_id Event ID
     */
    public function __construct(int $event_id) {
        $this->event_id = $event_id;
        $this->pod = flaw_is_pods_active() ? pods('event', $event_id) : null;
        $this->state = FLAW_Event_State_Manager::get_state($event_id);
    }

    /**
     * Get the Pods instance
     *
     * @return Pods|null
     */
    public function pod() {
        return $this->pod;
    }

    /**
     * Get current state
     *
     * @return string
     */
    public function state(): string {
        return $this->state;
    }

    /**
     * Get event ID
     *
     * @return int
     */
    public function event_id(): int {
        return $this->event_id;
    }

    /**
     * Check if section should render
     *
     * @param string $section Section name
     * @return bool
     */
    public function should_render(string $section): bool {
        // Check visibility rules
        if (!FLAW_Event_State_Manager::is_visible($section, $this->state)) {
            return false;
        }

        // Check if section has data
        return FLAW_Event_State_Manager::has_section_data($this->pod, $section);
    }

    /**
     * Render a section (loads template part)
     *
     * @param string $section Section name
     */
    public function render(string $section): void {
        if (!$this->should_render($section)) {
            return;
        }

        get_template_part(
            'template-parts/event/' . $section,
            null,
            [
                'pod'      => $this->pod,
                'state'    => $this->state,
                'event_id' => $this->event_id,
                'renderer' => $this,
            ]
        );
    }

    /**
     * Get section HTML as string (for AJAX/REST)
     *
     * @param string $section Section name
     * @return string
     */
    public function get_section_html(string $section): string {
        if (!$this->should_render($section)) {
            return '';
        }

        ob_start();
        $this->render($section);
        return ob_get_clean();
    }

    /**
     * Render all visible sections
     */
    public function render_all(): void {
        $groups = FLAW_Event_State_Manager::get_visible_groups($this->state);

        foreach ($groups as $section) {
            $this->render($section);
        }
    }

    // ─────────────────────────────────────────────────────────
    // Section-specific data getters
    // ─────────────────────────────────────────────────────────

    /**
     * Get registration data
     *
     * @return array
     */
    public function get_registration_data(): array {
        if (!$this->pod) {
            return [];
        }

        return [
            'enabled'      => (bool) $this->pod->field('event_registration_enabled'),
            'url'          => $this->pod->field('event_registration_url'),
            'deadline'     => $this->pod->field('event_registration_deadline'),
            'slots_total'  => (int) $this->pod->field('event_registration_slots_total'),
            'slots_filled' => (int) $this->pod->field('event_registration_slots_filled'),
            'requirements' => $this->pod->field('event_registration_requirements'),
            'fee'          => $this->pod->field('event_entry_fee'),
            'fee_token'    => $this->pod->field('event_entry_fee_token'),
            'is_open'      => $this->is_registration_open(),
        ];
    }

    /**
     * Check if registration is currently open
     *
     * @return bool
     */
    public function is_registration_open(): bool {
        if (!$this->pod || !$this->pod->field('event_registration_enabled')) {
            return false;
        }

        $deadline = $this->pod->field('event_registration_deadline');

        if (!$deadline) {
            return true;
        }

        return strtotime($deadline) > current_time('timestamp');
    }

    /**
     * Get broadcast data
     *
     * @return array
     */
    public function get_broadcast_data(): array {
        if (!$this->pod) {
            return [];
        }

        return [
            'platform'     => $this->pod->field('event_stream_platform'),
            'channel'      => $this->pod->field('event_stream_channel'),
            'backup_url'   => $this->pod->field('event_stream_url_backup'),
            'chat_enabled' => (bool) $this->pod->field('event_stream_chat_enabled'),
            'bracket_url'  => $this->pod->field('event_bracket_live_url'),
        ];
    }

    /**
     * Get results data
     *
     * @return array
     */
    public function get_results_data(): array {
        if (!$this->pod) {
            return [];
        }

        $winner_team = $this->pod->field('event_winner_team');
        $winner_players = $this->pod->field('event_winner_players');

        return [
            'winner_team'    => $winner_team ? [
                'id'    => $winner_team['ID'] ?? null,
                'title' => $winner_team['post_title'] ?? null,
                'logo'  => $this->get_team_logo($winner_team['ID'] ?? 0),
            ] : null,
            'winner_players' => $winner_players ?: [],
            'placement'      => (int) $this->pod->field('event_placement_org'),
            'prize_pool'     => $this->pod->field('event_prize_pool_total'),
            'prize_won'      => $this->pod->field('event_prize_won'),
            'prize_token'    => $this->pod->field('event_prize_token'),
            'bracket_url'    => $this->pod->field('event_final_bracket_url'),
            'recap'          => $this->pod->field('event_recap_content'),
        ];
    }

    /**
     * Get statistics data
     *
     * @return array
     */
    public function get_statistics_data(): array {
        if (!$this->pod) {
            return [];
        }

        $mvp = $this->pod->field('event_stats_mvp');

        return [
            'matches_played' => (int) $this->pod->field('event_stats_matches_played'),
            'matches_won'    => (int) $this->pod->field('event_stats_matches_won'),
            'maps_played'    => (int) $this->pod->field('event_stats_maps_played'),
            'maps_won'       => (int) $this->pod->field('event_stats_maps_won'),
            'highlights'     => $this->pod->field('event_stats_highlights'),
            'mvp'            => $mvp ? [
                'id'       => $mvp['ID'] ?? null,
                'gamertag' => $this->get_player_gamertag($mvp['ID'] ?? 0),
            ] : null,
            'json'           => $this->pod->field('event_stats_json'),
        ];
    }

    /**
     * Get media/archive data
     *
     * @return array
     */
    public function get_media_data(): array {
        if (!$this->pod) {
            return [];
        }

        return [
            'vod_platform'   => $this->pod->field('event_vod_platform'),
            'vod_id'         => $this->pod->field('event_vod_id'),
            'vod_url'        => $this->pod->field('event_vod_url'),
            'photos'         => $this->pod->field('event_photos_gallery') ?: [],
            'photos_url'     => $this->pod->field('event_photos_external_url'),
            'press_links'    => $this->pod->field('event_press_links') ?: [],
        ];
    }

    // ─────────────────────────────────────────────────────────
    // Helper methods
    // ─────────────────────────────────────────────────────────

    /**
     * Get team logo URL
     *
     * @param int $team_id Team ID
     * @return string|null
     */
    private function get_team_logo(int $team_id): ?string {
        if (!$team_id || !flaw_is_pods_active()) {
            return null;
        }

        $team = pods('team', $team_id);
        return flaw_get_image_url($team, 'team_logo');
    }

    /**
     * Get player gamertag
     *
     * @param int $player_id Player ID
     * @return string|null
     */
    private function get_player_gamertag(int $player_id): ?string {
        if (!$player_id || !flaw_is_pods_active()) {
            return null;
        }

        $player = pods('player', $player_id);
        return $player->field('player_gamertag');
    }
}

// ═══════════════════════════════════════════════════════════════
// HELPER FUNCTION
// ═══════════════════════════════════════════════════════════════

/**
 * Get event renderer instance
 *
 * @param int|null $event_id Event ID (defaults to current post)
 * @return FLAW_Event_Renderer
 */
function flaw_get_event_renderer(?int $event_id = null): FLAW_Event_Renderer {
    $event_id = $event_id ?: get_the_ID();
    return new FLAW_Event_Renderer($event_id);
}

/**
 * Render event section if visible
 *
 * @param string $section Section name
 * @param int|null $event_id Event ID
 */
function flaw_render_event_section(string $section, ?int $event_id = null): void {
    $renderer = flaw_get_event_renderer($event_id);
    $renderer->render($section);
}
