<?php
/**
 * Plugin Name: FLAW Gaming Core
 * Plugin URI: https://flawgaming.com
 * Description: Core functionality for FLAW Gaming theme - custom post types, helper functions, and REST API endpoints.
 * Version: 1.0.4
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: FLAW Gaming
 * Author URI: https://flawgaming.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: flaw-gaming-core
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('FLAW_CORE_VERSION', '1.0.4');
define('FLAW_CORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLAW_CORE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
final class FLAW_Gaming_Core {

    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        require_once FLAW_CORE_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'includes/class-taxonomies.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'includes/class-helpers.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'inc/class-event-state-manager.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'includes/class-rest-api.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'includes/class-demo-data.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'includes/class-applications.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'inc/card-data.php';
        require_once FLAW_CORE_PLUGIN_DIR . 'inc/pods-config.php';
    }

    private function init_hooks() {
        add_action('init', [$this, 'load_textdomain']);
        add_action('init', ['FLAW_Post_Types', 'register'], 5);
        add_action('init', ['FLAW_Taxonomies', 'register'], 5);
        add_action('rest_api_init', ['FLAW_REST_API', 'register_routes']);
        add_action('admin_init', [$this, 'maybe_setup_pods']);
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
    }

    /**
     * Auto-setup Pods fields if not yet configured
     */
    public function maybe_setup_pods() {
        if (!function_exists('pods_api')) {
            return;
        }

        $needs_setup = get_option('flaw_pods_needs_setup', false);
        $version = get_option('flaw_pods_version', '');

        // Run setup if flagged or if plugin version changed
        if ($needs_setup || $version !== FLAW_CORE_VERSION) {
            flaw_register_pods_fields();
            delete_option('flaw_pods_needs_setup');
            update_option('flaw_pods_version', FLAW_CORE_VERSION);
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain('flaw-gaming-core', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function activate() {
        FLAW_Post_Types::register();
        FLAW_Taxonomies::register();
        flush_rewrite_rules();
        // Flag for Pods setup on next admin load
        update_option('flaw_pods_needs_setup', true);
        set_transient('flaw_core_activated', true, 60);
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}

function flaw_gaming_core() {
    return FLAW_Gaming_Core::instance();
}

add_action('plugins_loaded', 'flaw_gaming_core');

function flaw_core_activation_notice() {
    if (get_transient('flaw_core_activated')) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>FLAW Gaming Core</strong> activated! Custom post types are now available.</p></div>';
        delete_transient('flaw_core_activated');
    }
}
add_action('admin_notices', 'flaw_core_activation_notice');
