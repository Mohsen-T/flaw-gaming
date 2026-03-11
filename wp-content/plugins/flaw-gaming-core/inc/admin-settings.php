<?php
/**
 * Admin Settings Page
 *
 * Provides a GUI for managing FLAW Gaming settings including API credentials.
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register settings page
 */
function flaw_register_settings_page() {
    add_options_page(
        'FLAW Gaming Settings',
        'FLAW Gaming',
        'manage_options',
        'flaw-gaming-settings',
        'flaw_render_settings_page'
    );
}
add_action('admin_menu', 'flaw_register_settings_page');

/**
 * Register settings
 */
function flaw_register_settings() {
    // Register setting group
    register_setting('flaw_settings_group', 'flaw_settings', [
        'type' => 'array',
        'sanitize_callback' => 'flaw_sanitize_settings',
        'default' => [],
    ]);

    // Twitch API Section
    add_settings_section(
        'flaw_twitch_section',
        'Twitch API Settings',
        'flaw_twitch_section_callback',
        'flaw-gaming-settings'
    );

    add_settings_field(
        'twitch_client_id',
        'Client ID',
        'flaw_text_field_callback',
        'flaw-gaming-settings',
        'flaw_twitch_section',
        [
            'id' => 'twitch_client_id',
            'description' => 'Your Twitch application Client ID',
            'placeholder' => 'e.g., abc123def456...',
        ]
    );

    add_settings_field(
        'twitch_client_secret',
        'Client Secret',
        'flaw_password_field_callback',
        'flaw-gaming-settings',
        'flaw_twitch_section',
        [
            'id' => 'twitch_client_secret',
            'description' => 'Your Twitch application Client Secret (stored securely)',
            'placeholder' => '••••••••••••••••',
        ]
    );

    // Discord Section
    add_settings_section(
        'flaw_discord_section',
        'Discord Integration (Optional)',
        'flaw_discord_section_callback',
        'flaw-gaming-settings'
    );

    add_settings_field(
        'discord_webhook_url',
        'Webhook URL',
        'flaw_text_field_callback',
        'flaw-gaming-settings',
        'flaw_discord_section',
        [
            'id' => 'discord_webhook_url',
            'description' => 'Discord webhook URL for notifications',
            'placeholder' => 'https://discord.com/api/webhooks/...',
        ]
    );

    // Social Links Section
    add_settings_section(
        'flaw_social_section',
        'Organization Social Links',
        'flaw_social_section_callback',
        'flaw-gaming-settings'
    );

    $social_platforms = [
        'twitter' => ['label' => 'Twitter/X', 'placeholder' => 'https://twitter.com/flawgaming'],
        'discord' => ['label' => 'Discord', 'placeholder' => 'https://discord.gg/flawgaming'],
        'twitch' => ['label' => 'Twitch', 'placeholder' => 'https://twitch.tv/flawgaming'],
        'youtube' => ['label' => 'YouTube', 'placeholder' => 'https://youtube.com/@flawgaming'],
        'instagram' => ['label' => 'Instagram', 'placeholder' => 'https://instagram.com/flawgaming'],
        'tiktok' => ['label' => 'TikTok', 'placeholder' => 'https://tiktok.com/@flawgaming'],
    ];

    foreach ($social_platforms as $platform => $data) {
        add_settings_field(
            "social_{$platform}",
            $data['label'],
            'flaw_url_field_callback',
            'flaw-gaming-settings',
            'flaw_social_section',
            [
                'id' => "social_{$platform}",
                'placeholder' => $data['placeholder'],
            ]
        );
    }
}
add_action('admin_init', 'flaw_register_settings');

/**
 * Sanitize settings
 */
function flaw_sanitize_settings($input) {
    $sanitized = [];

    // Text fields
    $text_fields = ['twitch_client_id', 'discord_webhook_url'];
    foreach ($text_fields as $field) {
        if (isset($input[$field])) {
            $sanitized[$field] = sanitize_text_field($input[$field]);
        }
    }

    // Password/secret fields
    if (isset($input['twitch_client_secret']) && !empty($input['twitch_client_secret'])) {
        // Only update if a new value is provided (not the placeholder)
        if ($input['twitch_client_secret'] !== '••••••••') {
            $sanitized['twitch_client_secret'] = sanitize_text_field($input['twitch_client_secret']);
        } else {
            // Keep existing value
            $existing = get_option('flaw_settings', []);
            $sanitized['twitch_client_secret'] = $existing['twitch_client_secret'] ?? '';
        }
    }

    // URL fields
    $url_fields = ['social_twitter', 'social_discord', 'social_twitch', 'social_youtube', 'social_instagram', 'social_tiktok'];
    foreach ($url_fields as $field) {
        if (isset($input[$field])) {
            $sanitized[$field] = esc_url_raw($input[$field]);
        }
    }

    return $sanitized;
}

/**
 * Section callbacks
 */
function flaw_twitch_section_callback() {
    $config_defined = defined('TWITCH_CLIENT_ID') && TWITCH_CLIENT_ID !== 'your_twitch_client_id_here';

    if ($config_defined) {
        echo '<p style="color: #10b981;">✓ Twitch credentials are configured in <code>wp-config.php</code>. Settings below will be ignored.</p>';
    } else {
        echo '<p>Enter your Twitch API credentials. Get them from <a href="https://dev.twitch.tv/console/apps" target="_blank">Twitch Developer Console</a>.</p>';
    }
}

function flaw_discord_section_callback() {
    echo '<p>Optional Discord integration for notifications and announcements.</p>';
}

function flaw_social_section_callback() {
    echo '<p>Your organization\'s official social media links. These can be used in templates and widgets.</p>';
}

/**
 * Field callbacks
 */
function flaw_text_field_callback($args) {
    $options = get_option('flaw_settings', []);
    $value = $options[$args['id']] ?? '';
    $disabled = '';

    // Check if defined in wp-config.php
    if ($args['id'] === 'twitch_client_id' && defined('TWITCH_CLIENT_ID') && TWITCH_CLIENT_ID !== 'your_twitch_client_id_here') {
        $value = TWITCH_CLIENT_ID;
        $disabled = 'disabled';
    }

    printf(
        '<input type="text" id="%1$s" name="flaw_settings[%1$s]" value="%2$s" class="regular-text" placeholder="%3$s" %4$s>',
        esc_attr($args['id']),
        esc_attr($value),
        esc_attr($args['placeholder'] ?? ''),
        $disabled
    );

    if (!empty($args['description'])) {
        printf('<p class="description">%s</p>', esc_html($args['description']));
    }
}

function flaw_password_field_callback($args) {
    $options = get_option('flaw_settings', []);
    $has_value = !empty($options[$args['id']]);
    $disabled = '';

    // Check if defined in wp-config.php
    if ($args['id'] === 'twitch_client_secret' && defined('TWITCH_CLIENT_SECRET') && TWITCH_CLIENT_SECRET !== 'your_twitch_client_secret_here') {
        $has_value = true;
        $disabled = 'disabled';
    }

    printf(
        '<input type="password" id="%1$s" name="flaw_settings[%1$s]" value="%2$s" class="regular-text" placeholder="%3$s" %4$s>',
        esc_attr($args['id']),
        $has_value ? '••••••••' : '',
        esc_attr($args['placeholder'] ?? ''),
        $disabled
    );

    if ($has_value && !$disabled) {
        echo ' <span style="color: #10b981;">✓ Configured</span>';
    }

    if (!empty($args['description'])) {
        printf('<p class="description">%s</p>', esc_html($args['description']));
    }
}

function flaw_url_field_callback($args) {
    $options = get_option('flaw_settings', []);
    $value = $options[$args['id']] ?? '';

    printf(
        '<input type="url" id="%1$s" name="flaw_settings[%1$s]" value="%2$s" class="regular-text" placeholder="%3$s">',
        esc_attr($args['id']),
        esc_url($value),
        esc_attr($args['placeholder'] ?? '')
    );
}

/**
 * Render settings page
 */
function flaw_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check for settings update
    if (isset($_GET['settings-updated'])) {
        add_settings_error('flaw_messages', 'flaw_message', 'Settings saved.', 'updated');
    }

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php settings_errors('flaw_messages'); ?>

        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="flex: 1;">
                <form action="options.php" method="post">
                    <?php
                    settings_fields('flaw_settings_group');
                    do_settings_sections('flaw-gaming-settings');
                    submit_button('Save Settings');
                    ?>
                </form>
            </div>

            <div style="width: 300px;">
                <div style="background: #fff; border: 1px solid #c3c4c7; padding: 15px; border-radius: 4px;">
                    <h3 style="margin-top: 0;">Quick Links</h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><a href="<?php echo admin_url('admin.php?page=pods'); ?>">Pods Admin</a></li>
                        <li><a href="<?php echo admin_url('tools.php?page=flaw-setup'); ?>">FLAW Setup</a></li>
                        <li><a href="<?php echo admin_url('nav-menus.php'); ?>">Menus</a></li>
                    </ul>

                    <h3>Content</h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><a href="<?php echo admin_url('edit.php?post_type=game'); ?>">Games</a></li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=team'); ?>">Teams</a></li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=player'); ?>">Players</a></li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=event'); ?>">Events</a></li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=creator'); ?>">Creators</a></li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=partner'); ?>">Partners</a></li>
                    </ul>

                    <h3>Resources</h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><a href="https://dev.twitch.tv/console/apps" target="_blank">Twitch Dev Console</a></li>
                        <li><a href="https://discord.com/developers/applications" target="_blank">Discord Dev Portal</a></li>
                    </ul>
                </div>

                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin-top: 15px;">
                    <h4 style="margin-top: 0; color: #856404;">wp-config.php Priority</h4>
                    <p style="margin-bottom: 0; font-size: 13px; color: #856404;">
                        If credentials are defined in <code>wp-config.php</code>, they take priority over these settings.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Helper function to get Twitch credentials
 */
function flaw_get_twitch_credentials() {
    // wp-config.php takes priority
    if (defined('TWITCH_CLIENT_ID') && TWITCH_CLIENT_ID !== 'your_twitch_client_id_here') {
        return [
            'client_id' => TWITCH_CLIENT_ID,
            'client_secret' => defined('TWITCH_CLIENT_SECRET') ? TWITCH_CLIENT_SECRET : '',
        ];
    }

    // Fallback to options
    $options = get_option('flaw_settings', []);

    return [
        'client_id' => $options['twitch_client_id'] ?? '',
        'client_secret' => $options['twitch_client_secret'] ?? '',
    ];
}

/**
 * Helper function to get social links
 */
function flaw_get_social_links() {
    $options = get_option('flaw_settings', []);

    return [
        'twitter' => $options['social_twitter'] ?? '',
        'discord' => $options['social_discord'] ?? '',
        'twitch' => $options['social_twitch'] ?? '',
        'youtube' => $options['social_youtube'] ?? '',
        'instagram' => $options['social_instagram'] ?? '',
        'tiktok' => $options['social_tiktok'] ?? '',
    ];
}
