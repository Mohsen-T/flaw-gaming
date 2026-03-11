<?php
/**
 * Demo Data Generator & Manager
 *
 * Handles generating demo content, toggling placeholder data,
 * and removing all demo content with a single click.
 *
 * @package FLAW_Gaming_Core
 */

if (!defined("ABSPATH")) exit;

class FLAW_Demo_Data {

    /** Meta key used to tag all demo-generated posts */
    const META_KEY = '_flaw_demo_data';

    /** Option key for placeholder (hardcoded) demo data toggle */
    const OPTION_KEY = 'flaw_show_demo_placeholders';

    public static function init() {
        add_action("admin_menu", [__CLASS__, "add_menu"]);
        add_action("admin_post_flaw_generate_demo", [__CLASS__, "handle_generate"]);
        add_action("admin_post_flaw_remove_demo", [__CLASS__, "handle_remove"]);
        add_action("admin_post_flaw_toggle_placeholders", [__CLASS__, "handle_toggle_placeholders"]);
        add_action("admin_bar_menu", [__CLASS__, "admin_bar_button"], 999);
        add_action("wp_ajax_flaw_toggle_placeholders", [__CLASS__, "ajax_toggle_placeholders"]);
    }

    /**
     * Check if placeholder (hardcoded) demo data should be shown.
     */
    public static function show_placeholders(): bool {
        return (bool) get_option(self::OPTION_KEY, true);
    }

    /**
     * Count demo-generated posts in the database.
     */
    public static function count_demo_posts(): int {
        global $wpdb;
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
                self::META_KEY,
                '1'
            )
        );
    }

    /**
     * Add admin menu under a top-level FLAW menu.
     */
    public static function add_menu() {
        add_submenu_page(
            "edit.php?post_type=event",
            "Demo Data Manager",
            "Demo Data",
            "manage_options",
            "flaw-demo-data",
            [__CLASS__, "render_page"]
        );
    }

    /**
     * Admin bar button — visible only to admins on the front end.
     */
    public static function admin_bar_button($wp_admin_bar) {
        if (!current_user_can('manage_options') || is_admin()) {
            return;
        }

        $demo_count = self::count_demo_posts();
        $placeholders_on = self::show_placeholders();

        if ($demo_count === 0 && !$placeholders_on) {
            return;
        }

        $label = 'Demo Data Active';
        if ($demo_count > 0) {
            $label .= " ({$demo_count} posts)";
        }

        $wp_admin_bar->add_node([
            'id'    => 'flaw-demo-data',
            'title' => '<span style="color:#D4A843;">' . esc_html($label) . '</span>',
            'href'  => admin_url('edit.php?post_type=event&page=flaw-demo-data'),
        ]);
    }

    /**
     * Render the admin page.
     */
    public static function render_page() {
        $demo_count = self::count_demo_posts();
        $placeholders_on = self::show_placeholders();
        $generated = isset($_GET['generated']);
        $removed = isset($_GET['removed']);
        $toggled = isset($_GET['toggled']);
        ?>
        <div class="wrap">
            <h1>FLAW Gaming - Demo Data Manager</h1>

            <?php if ($generated) : ?>
                <div class="notice notice-success is-dismissible"><p>Demo data generated successfully!</p></div>
            <?php endif; ?>
            <?php if ($removed) : ?>
                <div class="notice notice-success is-dismissible"><p>All demo data has been removed.</p></div>
            <?php endif; ?>
            <?php if ($toggled) : ?>
                <div class="notice notice-success is-dismissible">
                    <p>Placeholder data is now <strong><?php echo $placeholders_on ? 'enabled' : 'disabled'; ?></strong>.</p>
                </div>
            <?php endif; ?>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">

                <!-- Generate Section -->
                <div class="card" style="padding: 20px;">
                    <h2>Generate Demo Posts</h2>
                    <p>Create actual WordPress posts as demo content. These posts are tagged and can be removed later.</p>

                    <?php if ($demo_count > 0) : ?>
                        <div class="notice notice-info inline" style="margin:10px 0;">
                            <p><strong><?php echo $demo_count; ?></strong> demo posts currently exist.</p>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
                        <input type="hidden" name="action" value="flaw_generate_demo">
                        <?php wp_nonce_field("flaw_demo_data", "flaw_demo_nonce"); ?>
                        <fieldset style="margin: 15px 0;">
                            <label><input type="checkbox" name="generate[]" value="games" checked> Games (6)</label><br>
                            <label><input type="checkbox" name="generate[]" value="teams" checked> Teams (4)</label><br>
                            <label><input type="checkbox" name="generate[]" value="players" checked> Players (20)</label><br>
                            <label><input type="checkbox" name="generate[]" value="events" checked> Events (6)</label><br>
                            <label><input type="checkbox" name="generate[]" value="creators" checked> Creators (4)</label><br>
                            <label><input type="checkbox" name="generate[]" value="partners" checked> Partners (8)</label><br>
                        </fieldset>
                        <?php submit_button("Generate Demo Data", "primary", "submit", false); ?>
                    </form>
                </div>

                <!-- Remove Section -->
                <div class="card" style="padding: 20px;">
                    <h2>Remove Demo Data</h2>

                    <?php if ($demo_count > 0) : ?>
                        <p>Remove all <strong><?php echo $demo_count; ?></strong> demo-generated posts permanently.</p>
                        <form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>" onsubmit="return confirm('Remove all demo data? This cannot be undone.');">
                            <input type="hidden" name="action" value="flaw_remove_demo">
                            <?php wp_nonce_field("flaw_remove_demo", "flaw_remove_nonce"); ?>
                            <?php submit_button("Remove All Demo Posts", "delete", "submit", false); ?>
                        </form>
                    <?php else : ?>
                        <p style="color: #666;">No demo-generated posts found.</p>
                    <?php endif; ?>

                    <hr style="margin: 20px 0;">

                    <h3>Placeholder Data (Hardcoded Fallback)</h3>
                    <p>When sections have no real content, placeholder cards are shown on the front page.</p>

                    <form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>">
                        <input type="hidden" name="action" value="flaw_toggle_placeholders">
                        <?php wp_nonce_field("flaw_toggle_placeholders", "flaw_toggle_nonce"); ?>

                        <?php if ($placeholders_on) : ?>
                            <p>Status: <span style="color:#46b450; font-weight:bold;">Enabled</span> — Placeholder cards are visible for empty sections.</p>
                            <input type="hidden" name="state" value="off">
                            <?php submit_button("Disable Placeholders", "secondary", "submit", false); ?>
                        <?php else : ?>
                            <p>Status: <span style="color:#dc3232; font-weight:bold;">Disabled</span> — Empty sections are hidden on the front page.</p>
                            <input type="hidden" name="state" value="on">
                            <?php submit_button("Enable Placeholders", "secondary", "submit", false); ?>
                        <?php endif; ?>
                    </form>
                </div>

            </div>
        </div>
        <?php
    }

    // ─── Handlers ──────────────────────────────────────────────

    public static function handle_generate() {
        if (!current_user_can("manage_options")) wp_die("Unauthorized");
        check_admin_referer("flaw_demo_data", "flaw_demo_nonce");

        $generate = $_POST["generate"] ?? [];

        if (in_array("games", $generate))    self::create_games();
        if (in_array("teams", $generate))    self::create_teams();
        if (in_array("players", $generate))  self::create_players();
        if (in_array("events", $generate))   self::create_events();
        if (in_array("creators", $generate)) self::create_creators();
        if (in_array("partners", $generate)) self::create_partners();

        wp_redirect(admin_url("edit.php?post_type=event&page=flaw-demo-data&generated=1"));
        exit;
    }

    public static function handle_remove() {
        if (!current_user_can("manage_options")) wp_die("Unauthorized");
        check_admin_referer("flaw_remove_demo", "flaw_remove_nonce");

        self::remove_all_demo_data();

        wp_redirect(admin_url("edit.php?post_type=event&page=flaw-demo-data&removed=1"));
        exit;
    }

    public static function handle_toggle_placeholders() {
        if (!current_user_can("manage_options")) wp_die("Unauthorized");
        check_admin_referer("flaw_toggle_placeholders", "flaw_toggle_nonce");

        $state = ($_POST['state'] ?? 'on') === 'on';
        update_option(self::OPTION_KEY, $state);

        wp_redirect(admin_url("edit.php?post_type=event&page=flaw-demo-data&toggled=1"));
        exit;
    }

    public static function ajax_toggle_placeholders() {
        check_ajax_referer('flaw_demo_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $state = ($_POST['state'] ?? 'off') === 'on';
        update_option(self::OPTION_KEY, $state);
        wp_send_json_success(['state' => $state]);
    }

    // ─── Remove all demo data ──────────────────────────────────

    public static function remove_all_demo_data() {
        $demo_posts = get_posts([
            'post_type'      => ['game', 'team', 'player', 'event', 'creator', 'partner'],
            'posts_per_page' => -1,
            'meta_key'       => self::META_KEY,
            'meta_value'     => '1',
            'fields'         => 'ids',
        ]);

        foreach ($demo_posts as $post_id) {
            wp_delete_post($post_id, true); // Force delete (skip trash)
        }
    }

    // ─── Data creators (tag with _flaw_demo_data meta) ─────────

    private static function tag_demo($post_id) {
        update_post_meta($post_id, self::META_KEY, '1');
    }

    private static function create_games() {
        $games = [
            ["title" => "Off The Grid",      "genre" => "Battle Royale"],
            ["title" => "Valorant",           "genre" => "FPS"],
            ["title" => "Fortnite",           "genre" => "Battle Royale"],
            ["title" => "League of Legends",  "genre" => "MOBA"],
            ["title" => "Apex Legends",       "genre" => "Battle Royale"],
            ["title" => "Rocket League",      "genre" => "Sports"],
        ];

        foreach ($games as $game) {
            if (get_page_by_title($game["title"], OBJECT, "game")) continue;
            $id = wp_insert_post([
                "post_title"  => $game["title"],
                "post_type"   => "game",
                "post_status" => "publish",
            ]);
            if ($id && !is_wp_error($id)) {
                update_post_meta($id, "game_featured", "1");
                if (taxonomy_exists('game_genre')) {
                    wp_set_object_terms($id, $game["genre"], "game_genre");
                }
                self::tag_demo($id);
            }
        }
    }

    private static function create_teams() {
        $teams = [
            ["title" => "FLAW Alpha",   "game" => "Off The Grid"],
            ["title" => "FLAW Bravo",   "game" => "Valorant"],
            ["title" => "FLAW Charlie", "game" => "Fortnite"],
            ["title" => "FLAW Delta",   "game" => "Apex Legends"],
        ];

        foreach ($teams as $team) {
            if (get_page_by_title($team["title"], OBJECT, "team")) continue;
            $id = wp_insert_post([
                "post_title"  => $team["title"],
                "post_type"   => "team",
                "post_status" => "publish",
            ]);
            if ($id && !is_wp_error($id)) {
                update_post_meta($id, "team_status", "active");
                // Link to game post if exists
                $game_post = get_page_by_title($team["game"], OBJECT, "game");
                if ($game_post) {
                    update_post_meta($id, "team_game", $game_post->ID);
                }
                self::tag_demo($id);
            }
        }
    }

    private static function create_players() {
        $players = [
            ["name" => "Phoenix",    "role" => "IGL",     "team" => "FLAW Alpha"],
            ["name" => "Shadow",     "role" => "Fragger", "team" => "FLAW Alpha"],
            ["name" => "Blaze",      "role" => "Support", "team" => "FLAW Alpha"],
            ["name" => "Frost",      "role" => "Flex",    "team" => "FLAW Alpha"],
            ["name" => "Storm",      "role" => "Captain", "team" => "FLAW Alpha"],
            ["name" => "Viper",      "role" => "IGL",     "team" => "FLAW Bravo"],
            ["name" => "Ghost",      "role" => "Fragger", "team" => "FLAW Bravo"],
            ["name" => "Titan",      "role" => "Support", "team" => "FLAW Bravo"],
            ["name" => "Nova",       "role" => "Flex",    "team" => "FLAW Bravo"],
            ["name" => "Cipher",     "role" => "Captain", "team" => "FLAW Bravo"],
            ["name" => "Reaper",     "role" => "IGL",     "team" => "FLAW Charlie"],
            ["name" => "Sage",       "role" => "Support", "team" => "FLAW Charlie"],
            ["name" => "Jett",       "role" => "Fragger", "team" => "FLAW Charlie"],
            ["name" => "Raze",       "role" => "Flex",    "team" => "FLAW Charlie"],
            ["name" => "Omen",       "role" => "IGL",     "team" => "FLAW Delta"],
            ["name" => "Killjoy",    "role" => "Support", "team" => "FLAW Delta"],
            ["name" => "Cypher",     "role" => "Fragger", "team" => "FLAW Delta"],
            ["name" => "Breach",     "role" => "Coach",   "team" => "FLAW Delta"],
            ["name" => "Sova",       "role" => "Creator", "team" => ""],
            ["name" => "Brimstone",  "role" => "Creator", "team" => ""],
        ];

        foreach ($players as $player) {
            if (get_page_by_title($player["name"], OBJECT, "player")) continue;
            $id = wp_insert_post([
                "post_title"  => $player["name"],
                "post_type"   => "player",
                "post_status" => "publish",
            ]);
            if ($id && !is_wp_error($id)) {
                update_post_meta($id, "player_gamertag", $player["name"]);
                // Link to team
                if (!empty($player["team"])) {
                    $team_post = get_page_by_title($player["team"], OBJECT, "team");
                    if ($team_post) {
                        update_post_meta($id, "player_team", $team_post->ID);
                    }
                }
                // Set role taxonomy if available
                if (taxonomy_exists('player_role') && !empty($player["role"])) {
                    wp_set_object_terms($id, $player["role"], "player_role");
                }
                self::tag_demo($id);
            }
        }
    }

    private static function create_events() {
        $events = [
            ["title" => "FLAW Championship 2025",  "days" => 30,  "game" => "Off The Grid", "prize" => "10000"],
            ["title" => "Weekly Scrims",            "days" => 7,   "game" => "Valorant",     "prize" => "0"],
            ["title" => "Community Tournament",     "days" => 14,  "game" => "Fortnite",     "prize" => "500"],
            ["title" => "Pro League Qualifier",     "days" => 21,  "game" => "Apex Legends",  "prize" => "0"],
            ["title" => "Showmatch vs Merciless",   "days" => -7,  "game" => "Valorant",     "prize" => "1000"],
            ["title" => "Season 1 Finals",          "days" => -30, "game" => "Off The Grid", "prize" => "5000"],
        ];

        foreach ($events as $event) {
            if (get_page_by_title($event["title"], OBJECT, "event")) continue;
            $start = date("Y-m-d H:i:s", strtotime("{$event["days"]} days"));
            $end   = date("Y-m-d H:i:s", strtotime("{$event["days"]} days +4 hours"));
            $id    = wp_insert_post([
                "post_title"  => $event["title"],
                "post_type"   => "event",
                "post_status" => "publish",
            ]);
            if ($id && !is_wp_error($id)) {
                update_post_meta($id, "event_date_start", $start);
                update_post_meta($id, "event_date_end", $end);
                if (!empty($event["prize"])) {
                    update_post_meta($id, "event_prize_pool", $event["prize"]);
                }
                // Link to game
                $game_post = get_page_by_title($event["game"], OBJECT, "game");
                if ($game_post) {
                    update_post_meta($id, "event_game", $game_post->ID);
                }
                self::tag_demo($id);
            }
        }
    }

    private static function create_creators() {
        $creators = [
            ["name" => "StreamKing",    "platform" => "twitch",  "url" => "https://twitch.tv/streamking"],
            ["name" => "ProGamerGirl",  "platform" => "youtube", "url" => "https://youtube.com/@progamergirl"],
            ["name" => "ESportsLegend", "platform" => "twitch",  "url" => "https://twitch.tv/esportslegend"],
            ["name" => "GamingWizard",  "platform" => "tiktok",  "url" => "https://tiktok.com/@gamingwizard"],
        ];

        foreach ($creators as $creator) {
            if (get_page_by_title($creator["name"], OBJECT, "creator")) continue;
            $id = wp_insert_post([
                "post_title"  => $creator["name"],
                "post_type"   => "creator",
                "post_status" => "publish",
            ]);
            if ($id && !is_wp_error($id)) {
                update_post_meta($id, "creator_featured", "1");
                update_post_meta($id, "creator_platform", $creator["platform"]);
                update_post_meta($id, "creator_url", $creator["url"]);
                self::tag_demo($id);
            }
        }
    }

    private static function create_partners() {
        $partners = [
            ["title" => "GameFuel Energy",      "tier" => "platinum", "url" => "https://gamefuel.example.com"],
            ["title" => "ProGear Gaming",       "tier" => "platinum", "url" => "https://progear.example.com"],
            ["title" => "StreamTech",           "tier" => "gold",     "url" => "https://streamtech.example.com"],
            ["title" => "PixelPerfect Monitors","tier" => "gold",     "url" => "https://pixelperfect.example.com"],
            ["title" => "CloudNine Hosting",    "tier" => "silver",   "url" => "https://cloudnine.example.com"],
            ["title" => "ByteSpeed Internet",   "tier" => "silver",   "url" => "https://bytespeed.example.com"],
            ["title" => "GamersWear",           "tier" => "bronze",   "url" => "https://gamerswear.example.com"],
            ["title" => "SnackAttack",          "tier" => "bronze",   "url" => "https://snackattack.example.com"],
        ];

        foreach ($partners as $partner) {
            if (get_page_by_title($partner["title"], OBJECT, "partner")) continue;
            $id = wp_insert_post([
                "post_title"  => $partner["title"],
                "post_type"   => "partner",
                "post_status" => "publish",
            ]);
            if ($id && !is_wp_error($id)) {
                wp_set_object_terms($id, $partner["tier"], "partner_tier");
                update_post_meta($id, "partner_website", $partner["url"]);
                self::tag_demo($id);
            }
        }
    }
}

// Helper function for templates
if (!function_exists('flaw_show_demo_placeholders')) {
    function flaw_show_demo_placeholders(): bool {
        return FLAW_Demo_Data::show_placeholders();
    }
}

// Initialize
add_action("init", ["FLAW_Demo_Data", "init"]);
