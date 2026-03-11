<?php
/**
 * FLAW Gaming - Application System
 *
 * Handles player/creator/staff applications with database storage,
 * admin review interface, and email notifications.
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

class FLAW_Applications {

    /**
     * Application statuses
     */
    const STATUS_PENDING  = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Initialize the application system
     */
    public static function init() {
        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('wp_ajax_flaw_submit_application', [__CLASS__, 'handle_submission']);
        add_action('wp_ajax_nopriv_flaw_submit_application', [__CLASS__, 'handle_submission']);
        add_action('wp_ajax_flaw_update_application_status', [__CLASS__, 'update_status']);
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post_application', [__CLASS__, 'save_meta_box'], 10, 2);
        add_filter('manage_application_posts_columns', [__CLASS__, 'admin_columns']);
        add_action('manage_application_posts_custom_column', [__CLASS__, 'admin_column_content'], 10, 2);
        add_filter('manage_edit-application_sortable_columns', [__CLASS__, 'sortable_columns']);
        add_action('pre_get_posts', [__CLASS__, 'sort_columns']);
        add_filter('post_row_actions', [__CLASS__, 'row_actions'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_scripts']);

        // Add status filter dropdown
        add_action('restrict_manage_posts', [__CLASS__, 'status_filter_dropdown']);
        add_filter('parse_query', [__CLASS__, 'filter_by_status']);
    }

    /**
     * Register the application post type
     */
    public static function register_post_type() {
        $labels = [
            'name'               => __('Applications', 'flaw-gaming'),
            'singular_name'      => __('Application', 'flaw-gaming'),
            'menu_name'          => __('Applications', 'flaw-gaming'),
            'all_items'          => __('All Applications', 'flaw-gaming'),
            'view_item'          => __('View Application', 'flaw-gaming'),
            'edit_item'          => __('Review Application', 'flaw-gaming'),
            'search_items'       => __('Search Applications', 'flaw-gaming'),
            'not_found'          => __('No applications found', 'flaw-gaming'),
            'not_found_in_trash' => __('No applications found in Trash', 'flaw-gaming'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 26,
            'menu_icon'           => 'dashicons-clipboard',
            'supports'            => ['title'],
            'capability_type'     => 'post',
            'capabilities'        => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap'        => true,
        ];

        register_post_type('application', $args);
    }

    /**
     * Handle AJAX form submission
     */
    public static function handle_submission() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flaw_application_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page and try again.']);
        }

        // Validate required fields
        $required = ['name', 'email', 'discord', 'age', 'role', 'experience'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error(['message' => 'Please fill in all required fields.']);
            }
        }

        // Sanitize input
        $name       = sanitize_text_field($_POST['name']);
        $email      = sanitize_email($_POST['email']);
        $discord    = sanitize_text_field($_POST['discord']);
        $age        = absint($_POST['age']);
        $role       = sanitize_text_field($_POST['role']);
        $game       = sanitize_text_field($_POST['game'] ?? '');
        $experience = sanitize_textarea_field($_POST['experience']);
        $links      = sanitize_textarea_field($_POST['links'] ?? '');

        // Validate email
        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Please enter a valid email address.']);
        }

        // Validate age
        if ($age < 13 || $age > 99) {
            wp_send_json_error(['message' => 'Please enter a valid age.']);
        }

        // Check for duplicate applications (same email in last 30 days)
        $existing = get_posts([
            'post_type'      => 'application',
            'meta_query'     => [
                [
                    'key'   => '_application_email',
                    'value' => $email,
                ],
            ],
            'date_query'     => [
                ['after' => '30 days ago'],
            ],
            'posts_per_page' => 1,
        ]);

        if (!empty($existing)) {
            wp_send_json_error(['message' => 'You have already submitted an application recently. Please wait for a response before applying again.']);
        }

        // Create application post
        $post_id = wp_insert_post([
            'post_type'   => 'application',
            'post_title'  => sprintf('%s - %s', $name, ucfirst($role)),
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Failed to submit application. Please try again.']);
        }

        // Save meta data
        update_post_meta($post_id, '_application_name', $name);
        update_post_meta($post_id, '_application_email', $email);
        update_post_meta($post_id, '_application_discord', $discord);
        update_post_meta($post_id, '_application_age', $age);
        update_post_meta($post_id, '_application_role', $role);
        update_post_meta($post_id, '_application_game', $game);
        update_post_meta($post_id, '_application_experience', $experience);
        update_post_meta($post_id, '_application_links', $links);
        update_post_meta($post_id, '_application_status', self::STATUS_PENDING);
        update_post_meta($post_id, '_application_ip', sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''));

        // Send admin notification
        self::send_admin_notification($post_id);

        // Send applicant confirmation
        self::send_applicant_confirmation($post_id);

        wp_send_json_success([
            'message' => 'Your application has been submitted successfully! We will review it and get back to you within 48-72 hours.',
        ]);
    }

    /**
     * Send email notification to admin
     */
    private static function send_admin_notification($post_id) {
        $admin_email = get_option('admin_email');
        $name        = get_post_meta($post_id, '_application_name', true);
        $role        = get_post_meta($post_id, '_application_role', true);
        $email       = get_post_meta($post_id, '_application_email', true);
        $discord     = get_post_meta($post_id, '_application_discord', true);

        $subject = sprintf('[FLAW Gaming] New %s Application: %s', ucfirst($role), $name);

        $message = sprintf(
            "A new application has been submitted on FLAW Gaming.\n\n" .
            "Name: %s\n" .
            "Role: %s\n" .
            "Email: %s\n" .
            "Discord: %s\n\n" .
            "Review the application here:\n%s",
            $name,
            ucfirst($role),
            $email,
            $discord,
            admin_url('post.php?post=' . $post_id . '&action=edit')
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Send confirmation email to applicant
     */
    private static function send_applicant_confirmation($post_id) {
        $email   = get_post_meta($post_id, '_application_email', true);
        $name    = get_post_meta($post_id, '_application_name', true);
        $role    = get_post_meta($post_id, '_application_role', true);

        $subject = 'FLAW Gaming - Application Received';

        $message = sprintf(
            "Hi %s,\n\n" .
            "Thank you for applying to join FLAW Gaming as a %s!\n\n" .
            "We have received your application and our team will review it within 48-72 hours. " .
            "You will receive a response via email or Discord.\n\n" .
            "In the meantime, feel free to join our Discord community:\n" .
            "%s\n\n" .
            "Best regards,\n" .
            "FLAW Gaming Team",
            $name,
            ucfirst($role),
            get_theme_mod('flaw_discord_url', 'https://discord.gg/flawgaming')
        );

        wp_mail($email, $subject, $message);
    }

    /**
     * Send status update email to applicant
     */
    public static function send_status_notification($post_id, $new_status) {
        $email   = get_post_meta($post_id, '_application_email', true);
        $name    = get_post_meta($post_id, '_application_name', true);
        $role    = get_post_meta($post_id, '_application_role', true);
        $notes   = get_post_meta($post_id, '_application_admin_notes', true);

        if ($new_status === self::STATUS_APPROVED) {
            $subject = 'FLAW Gaming - Application Approved!';
            $message = sprintf(
                "Hi %s,\n\n" .
                "Congratulations! Your application to join FLAW Gaming as a %s has been APPROVED!\n\n" .
                "Next steps:\n" .
                "1. Join our Discord server if you haven't already: %s\n" .
                "2. Message a staff member to get your role assigned\n" .
                "3. Introduce yourself in the community!\n\n" .
                "%s\n\n" .
                "Welcome to the team!\n" .
                "FLAW Gaming",
                $name,
                ucfirst($role),
                get_theme_mod('flaw_discord_url', 'https://discord.gg/flawgaming'),
                $notes ? "Additional notes from our team:\n" . $notes . "\n" : ""
            );
        } elseif ($new_status === self::STATUS_REJECTED) {
            $subject = 'FLAW Gaming - Application Update';
            $message = sprintf(
                "Hi %s,\n\n" .
                "Thank you for your interest in joining FLAW Gaming.\n\n" .
                "After careful review, we have decided not to move forward with your application at this time.\n\n" .
                "%s\n" .
                "This doesn't mean the door is closed forever. Feel free to:\n" .
                "- Join our Discord community: %s\n" .
                "- Participate in our open events\n" .
                "- Apply again in the future\n\n" .
                "Best of luck,\n" .
                "FLAW Gaming Team",
                $name,
                $notes ? "Feedback:\n" . $notes . "\n\n" : "",
                get_theme_mod('flaw_discord_url', 'https://discord.gg/flawgaming')
            );
        } else {
            return; // Don't send email for other statuses
        }

        wp_mail($email, $subject, $message);
    }

    /**
     * Add meta boxes to application edit screen
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'application_details',
            __('Application Details', 'flaw-gaming'),
            [__CLASS__, 'render_details_meta_box'],
            'application',
            'normal',
            'high'
        );

        add_meta_box(
            'application_actions',
            __('Review Actions', 'flaw-gaming'),
            [__CLASS__, 'render_actions_meta_box'],
            'application',
            'side',
            'high'
        );
    }

    /**
     * Render application details meta box
     */
    public static function render_details_meta_box($post) {
        $name       = get_post_meta($post->ID, '_application_name', true);
        $email      = get_post_meta($post->ID, '_application_email', true);
        $discord    = get_post_meta($post->ID, '_application_discord', true);
        $age        = get_post_meta($post->ID, '_application_age', true);
        $role       = get_post_meta($post->ID, '_application_role', true);
        $game       = get_post_meta($post->ID, '_application_game', true);
        $experience = get_post_meta($post->ID, '_application_experience', true);
        $links      = get_post_meta($post->ID, '_application_links', true);
        $ip         = get_post_meta($post->ID, '_application_ip', true);
        ?>
        <style>
            .application-details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
            .application-details .full-width { grid-column: 1 / -1; }
            .detail-group { margin-bottom: 15px; }
            .detail-label { font-weight: 600; color: #1d2327; margin-bottom: 5px; }
            .detail-value { color: #50575e; padding: 10px; background: #f6f7f7; border-radius: 4px; }
            .detail-value a { color: #2271b1; }
            .detail-value.experience { white-space: pre-wrap; }
        </style>
        <div class="application-details">
            <div class="detail-group">
                <div class="detail-label">Name / Gamertag</div>
                <div class="detail-value"><?php echo esc_html($name); ?></div>
            </div>
            <div class="detail-group">
                <div class="detail-label">Email</div>
                <div class="detail-value"><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div>
            </div>
            <div class="detail-group">
                <div class="detail-label">Discord</div>
                <div class="detail-value"><?php echo esc_html($discord); ?></div>
            </div>
            <div class="detail-group">
                <div class="detail-label">Age</div>
                <div class="detail-value"><?php echo esc_html($age); ?></div>
            </div>
            <div class="detail-group">
                <div class="detail-label">Applying For</div>
                <div class="detail-value"><?php echo esc_html(ucfirst($role)); ?></div>
            </div>
            <div class="detail-group">
                <div class="detail-label">Primary Game</div>
                <div class="detail-value"><?php echo esc_html($game ?: 'Not specified'); ?></div>
            </div>
            <div class="detail-group full-width">
                <div class="detail-label">Experience / About</div>
                <div class="detail-value experience"><?php echo esc_html($experience); ?></div>
            </div>
            <?php if ($links) : ?>
            <div class="detail-group full-width">
                <div class="detail-label">Social / Portfolio Links</div>
                <div class="detail-value">
                    <?php
                    $link_lines = explode("\n", $links);
                    foreach ($link_lines as $link) {
                        $link = trim($link);
                        if ($link) {
                            if (filter_var($link, FILTER_VALIDATE_URL)) {
                                echo '<a href="' . esc_url($link) . '" target="_blank">' . esc_html($link) . '</a><br>';
                            } else {
                                echo esc_html($link) . '<br>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="detail-group">
                <div class="detail-label">Submitted</div>
                <div class="detail-value"><?php echo get_the_date('F j, Y \a\t g:i a', $post); ?></div>
            </div>
            <div class="detail-group">
                <div class="detail-label">IP Address</div>
                <div class="detail-value"><?php echo esc_html($ip ?: 'Unknown'); ?></div>
            </div>
        </div>
        <?php
    }

    /**
     * Render review actions meta box
     */
    public static function render_actions_meta_box($post) {
        $status = get_post_meta($post->ID, '_application_status', true) ?: self::STATUS_PENDING;
        $notes  = get_post_meta($post->ID, '_application_admin_notes', true);

        wp_nonce_field('flaw_application_meta', 'flaw_application_nonce');
        ?>
        <style>
            .status-badge { display: inline-block; padding: 5px 12px; border-radius: 4px; font-weight: 600; text-transform: uppercase; font-size: 11px; }
            .status-pending { background: #f0c33c; color: #1d2327; }
            .status-reviewed { background: #72aee6; color: white; }
            .status-approved { background: #00a32a; color: white; }
            .status-rejected { background: #d63638; color: white; }
            .action-field { margin-bottom: 15px; }
            .action-field label { display: block; font-weight: 600; margin-bottom: 5px; }
            .action-field select, .action-field textarea { width: 100%; }
            .action-field textarea { min-height: 100px; }
            .status-note { font-size: 12px; color: #646970; margin-top: 10px; }
        </style>

        <div class="action-field">
            <label>Current Status</label>
            <span class="status-badge status-<?php echo esc_attr($status); ?>">
                <?php echo esc_html(ucfirst($status)); ?>
            </span>
        </div>

        <div class="action-field">
            <label for="application_status">Update Status</label>
            <select name="application_status" id="application_status">
                <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
                <option value="reviewed" <?php selected($status, 'reviewed'); ?>>Under Review</option>
                <option value="approved" <?php selected($status, 'approved'); ?>>Approved</option>
                <option value="rejected" <?php selected($status, 'rejected'); ?>>Rejected</option>
            </select>
        </div>

        <div class="action-field">
            <label for="application_notes">Admin Notes (included in email)</label>
            <textarea name="application_notes" id="application_notes" placeholder="Optional notes to include in the status email..."><?php echo esc_textarea($notes); ?></textarea>
        </div>

        <div class="action-field">
            <label>
                <input type="checkbox" name="send_notification" value="1" checked>
                Send email notification on status change
            </label>
        </div>

        <p class="status-note">
            <strong>Tip:</strong> Changing status to Approved or Rejected will automatically send an email to the applicant (if checkbox is checked).
        </p>
        <?php
    }

    /**
     * Save meta box data
     */
    public static function save_meta_box($post_id, $post) {
        if (!isset($_POST['flaw_application_nonce']) || !wp_verify_nonce($_POST['flaw_application_nonce'], 'flaw_application_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $old_status = get_post_meta($post_id, '_application_status', true);
        $new_status = sanitize_text_field($_POST['application_status'] ?? '');
        $notes      = sanitize_textarea_field($_POST['application_notes'] ?? '');
        $send_email = isset($_POST['send_notification']);

        // Save notes
        update_post_meta($post_id, '_application_admin_notes', $notes);

        // Update status and send notification if changed
        if ($new_status && $new_status !== $old_status) {
            update_post_meta($post_id, '_application_status', $new_status);

            if ($send_email && in_array($new_status, [self::STATUS_APPROVED, self::STATUS_REJECTED])) {
                self::send_status_notification($post_id, $new_status);
            }
        }
    }

    /**
     * Custom admin columns
     */
    public static function admin_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new_columns[$key] = $value;
                $new_columns['applicant_role'] = __('Role', 'flaw-gaming');
                $new_columns['applicant_email'] = __('Email', 'flaw-gaming');
                $new_columns['applicant_discord'] = __('Discord', 'flaw-gaming');
                $new_columns['application_status'] = __('Status', 'flaw-gaming');
            } elseif ($key !== 'date') {
                $new_columns[$key] = $value;
            }
        }
        $new_columns['date'] = __('Submitted', 'flaw-gaming');
        return $new_columns;
    }

    /**
     * Admin column content
     */
    public static function admin_column_content($column, $post_id) {
        switch ($column) {
            case 'applicant_role':
                $role = get_post_meta($post_id, '_application_role', true);
                echo esc_html(ucfirst($role));
                break;

            case 'applicant_email':
                $email = get_post_meta($post_id, '_application_email', true);
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                break;

            case 'applicant_discord':
                echo esc_html(get_post_meta($post_id, '_application_discord', true));
                break;

            case 'application_status':
                $status = get_post_meta($post_id, '_application_status', true) ?: 'pending';
                $colors = [
                    'pending'  => '#f0c33c',
                    'reviewed' => '#72aee6',
                    'approved' => '#00a32a',
                    'rejected' => '#d63638',
                ];
                $bg = $colors[$status] ?? '#ddd';
                $text = $status === 'pending' ? '#1d2327' : 'white';
                printf(
                    '<span style="display:inline-block;padding:3px 8px;border-radius:3px;background:%s;color:%s;font-size:11px;font-weight:600;text-transform:uppercase;">%s</span>',
                    esc_attr($bg),
                    esc_attr($text),
                    esc_html(ucfirst($status))
                );
                break;
        }
    }

    /**
     * Make columns sortable
     */
    public static function sortable_columns($columns) {
        $columns['application_status'] = 'application_status';
        $columns['applicant_role'] = 'applicant_role';
        return $columns;
    }

    /**
     * Handle column sorting
     */
    public static function sort_columns($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== 'application') {
            return;
        }

        $orderby = $query->get('orderby');

        if ($orderby === 'application_status') {
            $query->set('meta_key', '_application_status');
            $query->set('orderby', 'meta_value');
        }

        if ($orderby === 'applicant_role') {
            $query->set('meta_key', '_application_role');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * Custom row actions
     */
    public static function row_actions($actions, $post) {
        if ($post->post_type === 'application') {
            unset($actions['inline hide-if-no-js']); // Remove quick edit
            $actions['view'] = sprintf(
                '<a href="%s">%s</a>',
                admin_url('post.php?post=' . $post->ID . '&action=edit'),
                __('Review', 'flaw-gaming')
            );
        }
        return $actions;
    }

    /**
     * Status filter dropdown
     */
    public static function status_filter_dropdown() {
        global $typenow;

        if ($typenow !== 'application') {
            return;
        }

        $current = $_GET['application_status'] ?? '';
        $statuses = [
            ''         => __('All Statuses', 'flaw-gaming'),
            'pending'  => __('Pending', 'flaw-gaming'),
            'reviewed' => __('Under Review', 'flaw-gaming'),
            'approved' => __('Approved', 'flaw-gaming'),
            'rejected' => __('Rejected', 'flaw-gaming'),
        ];

        echo '<select name="application_status">';
        foreach ($statuses as $value => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($value),
                selected($current, $value, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    /**
     * Filter by status
     */
    public static function filter_by_status($query) {
        global $pagenow, $typenow;

        if ($pagenow !== 'edit.php' || $typenow !== 'application') {
            return;
        }

        if (!empty($_GET['application_status'])) {
            $query->query_vars['meta_key'] = '_application_status';
            $query->query_vars['meta_value'] = sanitize_text_field($_GET['application_status']);
        }
    }

    /**
     * Admin scripts
     */
    public static function admin_scripts($hook) {
        global $post_type;

        if ($post_type !== 'application') {
            return;
        }

        // Add pending count to menu
        $pending_count = wp_count_posts('application');
        if (isset($pending_count->publish)) {
            $pending = get_posts([
                'post_type'      => 'application',
                'posts_per_page' => -1,
                'meta_query'     => [
                    [
                        'key'   => '_application_status',
                        'value' => 'pending',
                    ],
                ],
            ]);
            $count = count($pending);

            if ($count > 0) {
                ?>
                <script>
                jQuery(document).ready(function($) {
                    var menuItem = $('a[href="edit.php?post_type=application"]');
                    if (menuItem.length) {
                        menuItem.append(' <span class="awaiting-mod count-<?php echo $count; ?>"><span class="pending-count"><?php echo $count; ?></span></span>');
                    }
                });
                </script>
                <?php
            }
        }
    }

    /**
     * Get application count by status
     */
    public static function get_count_by_status($status) {
        $posts = get_posts([
            'post_type'      => 'application',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'   => '_application_status',
                    'value' => $status,
                ],
            ],
        ]);
        return count($posts);
    }
}

// Initialize
FLAW_Applications::init();
