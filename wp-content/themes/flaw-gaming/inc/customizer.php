<?php
/**
 * Theme Customizer Settings
 *
 * @package FLAW_Gaming
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Customizer settings
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager
 */
function flaw_customize_register($wp_customize) {

    // ═══════════════════════════════════════════════════════════════
    // FLAW Gaming Panel
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_panel('flaw_theme_options', [
        'title'       => __('FLAW Gaming Options', 'flaw-gaming'),
        'description' => __('Customize the FLAW Gaming theme settings.', 'flaw-gaming'),
        'priority'    => 30,
    ]);

    // ═══════════════════════════════════════════════════════════════
    // Hero Section
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_hero_section', [
        'title'    => __('Hero Section', 'flaw-gaming'),
        'panel'    => 'flaw_theme_options',
        'priority' => 10,
    ]);

    // Hero Background Image
    $wp_customize->add_setting('flaw_hero_background', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'flaw_hero_background', [
        'label'       => __('Background Image', 'flaw-gaming'),
        'description' => __('Recommended size: 1920x1080px. Leave empty for gradient-only background.', 'flaw-gaming'),
        'section'     => 'flaw_hero_section',
        'settings'    => 'flaw_hero_background',
    ]));

    // Hero Background Video URL
    $wp_customize->add_setting('flaw_hero_video', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_video', [
        'label'       => __('Background Video URL', 'flaw-gaming'),
        'description' => __('Optional: YouTube or direct video URL for hero background.', 'flaw-gaming'),
        'section'     => 'flaw_hero_section',
        'type'        => 'url',
    ]);

    // Hero Tagline
    $wp_customize->add_setting('flaw_hero_tagline', [
        'default'           => 'Flawless by name. Flawless by nature.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_tagline', [
        'label'   => __('Tagline', 'flaw-gaming'),
        'section' => 'flaw_hero_section',
        'type'    => 'text',
    ]);

    // Hero Title
    $wp_customize->add_setting('flaw_hero_title', [
        'default'           => 'Powering the <span>Future</span> of Gaming',
        'sanitize_callback' => 'flaw_sanitize_html',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_title', [
        'label'       => __('Title', 'flaw-gaming'),
        'description' => __('Use &lt;span&gt; tags to highlight words in red.', 'flaw-gaming'),
        'section'     => 'flaw_hero_section',
        'type'        => 'text',
    ]);

    // Hero Subtitle
    $wp_customize->add_setting('flaw_hero_subtitle', [
        'default'           => 'A global gaming community focused on mastering Web3 games and building the next generation of competitive players.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_subtitle', [
        'label'   => __('Subtitle', 'flaw-gaming'),
        'section' => 'flaw_hero_section',
        'type'    => 'textarea',
    ]);

    // Primary CTA Text
    $wp_customize->add_setting('flaw_hero_cta_text', [
        'default'           => 'Join Our Discord',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_cta_text', [
        'label'   => __('Primary Button Text', 'flaw-gaming'),
        'section' => 'flaw_hero_section',
        'type'    => 'text',
    ]);

    // Primary CTA URL
    $wp_customize->add_setting('flaw_hero_cta_url', [
        'default'           => 'https://discord.gg/flawgaminghq',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_cta_url', [
        'label'   => __('Primary Button URL', 'flaw-gaming'),
        'section' => 'flaw_hero_section',
        'type'    => 'url',
    ]);

    // Secondary CTA Text
    $wp_customize->add_setting('flaw_hero_secondary_text', [
        'default'           => 'View Events',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_secondary_text', [
        'label'   => __('Secondary Button Text', 'flaw-gaming'),
        'section' => 'flaw_hero_section',
        'type'    => 'text',
    ]);

    // Secondary CTA URL
    $wp_customize->add_setting('flaw_hero_secondary_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_hero_secondary_url', [
        'label'       => __('Secondary Button URL', 'flaw-gaming'),
        'description' => __('Leave empty to link to Events archive.', 'flaw-gaming'),
        'section'     => 'flaw_hero_section',
        'type'        => 'url',
    ]);

    // ═══════════════════════════════════════════════════════════════
    // Social Links Section
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_social_section', [
        'title'    => __('Social Links', 'flaw-gaming'),
        'panel'    => 'flaw_theme_options',
        'priority' => 20,
    ]);

    $social_platforms = [
        'discord'   => 'Discord',
        'twitter'   => 'Twitter / X',
        'youtube'   => 'YouTube',
        'twitch'    => 'Twitch',
        'tiktok'    => 'TikTok',
        'instagram' => 'Instagram',
    ];

    foreach ($social_platforms as $platform => $label) {
        $wp_customize->add_setting("flaw_social_{$platform}", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);

        $wp_customize->add_control("flaw_social_{$platform}", [
            'label'   => $label,
            'section' => 'flaw_social_section',
            'type'    => 'url',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Discord Section
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_discord_section', [
        'title'    => __('Discord CTA', 'flaw-gaming'),
        'panel'    => 'flaw_theme_options',
        'priority' => 30,
    ]);

    // Discord Title
    $wp_customize->add_setting('flaw_discord_title', [
        'default'           => 'Join the FLAW Community',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_discord_title', [
        'label'   => __('Title', 'flaw-gaming'),
        'section' => 'flaw_discord_section',
        'type'    => 'text',
    ]);

    // Discord Description
    $wp_customize->add_setting('flaw_discord_description', [
        'default'           => 'Connect with fellow gamers, find teammates, participate in events, and stay updated on everything FLAW Gaming.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_discord_description', [
        'label'   => __('Description', 'flaw-gaming'),
        'section' => 'flaw_discord_section',
        'type'    => 'textarea',
    ]);

    // Discord URL
    $wp_customize->add_setting('flaw_discord_url', [
        'default'           => 'https://discord.gg/flawgaminghq',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_discord_url', [
        'label'   => __('Discord Invite URL', 'flaw-gaming'),
        'section' => 'flaw_discord_section',
        'type'    => 'url',
    ]);

    // ═══════════════════════════════════════════════════════════════
    // Statistics Section (Achievements)
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_stats_section', [
        'title'       => __('Statistics / Achievements', 'flaw-gaming'),
        'description' => __('Customize the "By The Numbers" statistics section on the homepage.', 'flaw-gaming'),
        'panel'       => 'flaw_theme_options',
        'priority'    => 35,
    ]);

    // Stats Section Tagline
    $wp_customize->add_setting('flaw_stats_tagline', [
        'default'           => 'Our Impact',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stats_tagline', [
        'label'   => __('Section Tagline', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    // Stats Section Title
    $wp_customize->add_setting('flaw_stats_title', [
        'default'           => 'By The Numbers',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stats_title', [
        'label'   => __('Section Title', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    // Stat 1
    $wp_customize->add_setting('flaw_stat_1_value', [
        'default'           => '50+',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_1_value', [
        'label'   => __('Stat 1: Value', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_1_label', [
        'default'           => 'Active Members',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_1_label', [
        'label'   => __('Stat 1: Label', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_1_icon', [
        'default'           => 'members',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_1_icon', [
        'label'   => __('Stat 1: Icon', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'members' => __('Members (people)', 'flaw-gaming'),
            'trophy'  => __('Trophy', 'flaw-gaming'),
            'events'  => __('Events (calendar)', 'flaw-gaming'),
            'games'   => __('Games (controller)', 'flaw-gaming'),
        ],
    ]);

    $wp_customize->add_setting('flaw_stat_1_color', [
        'default'           => 'primary',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_1_color', [
        'label'   => __('Stat 1: Color', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'primary'   => __('Primary (Red)', 'flaw-gaming'),
            'gold'      => __('Gold', 'flaw-gaming'),
            'secondary' => __('Secondary (Cyan)', 'flaw-gaming'),
            'accent'    => __('Accent (Yellow)', 'flaw-gaming'),
        ],
    ]);

    // Stat 2
    $wp_customize->add_setting('flaw_stat_2_value', [
        'default'           => '#1',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_2_value', [
        'label'   => __('Stat 2: Value', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_2_label', [
        'default'           => 'World Record Kills (OTG)',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_2_label', [
        'label'   => __('Stat 2: Label', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_2_icon', [
        'default'           => 'trophy',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_2_icon', [
        'label'   => __('Stat 2: Icon', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'members' => __('Members (people)', 'flaw-gaming'),
            'trophy'  => __('Trophy', 'flaw-gaming'),
            'events'  => __('Events (calendar)', 'flaw-gaming'),
            'games'   => __('Games (controller)', 'flaw-gaming'),
        ],
    ]);

    $wp_customize->add_setting('flaw_stat_2_color', [
        'default'           => 'gold',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_2_color', [
        'label'   => __('Stat 2: Color', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'primary'   => __('Primary (Red)', 'flaw-gaming'),
            'gold'      => __('Gold', 'flaw-gaming'),
            'secondary' => __('Secondary (Cyan)', 'flaw-gaming'),
            'accent'    => __('Accent (Yellow)', 'flaw-gaming'),
        ],
    ]);

    // Stat 3
    $wp_customize->add_setting('flaw_stat_3_value', [
        'default'           => '100+',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_3_value', [
        'label'   => __('Stat 3: Value', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_3_label', [
        'default'           => 'Events Competed',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_3_label', [
        'label'   => __('Stat 3: Label', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_3_icon', [
        'default'           => 'events',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_3_icon', [
        'label'   => __('Stat 3: Icon', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'members' => __('Members (people)', 'flaw-gaming'),
            'trophy'  => __('Trophy', 'flaw-gaming'),
            'events'  => __('Events (calendar)', 'flaw-gaming'),
            'games'   => __('Games (controller)', 'flaw-gaming'),
        ],
    ]);

    $wp_customize->add_setting('flaw_stat_3_color', [
        'default'           => 'secondary',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_3_color', [
        'label'   => __('Stat 3: Color', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'primary'   => __('Primary (Red)', 'flaw-gaming'),
            'gold'      => __('Gold', 'flaw-gaming'),
            'secondary' => __('Secondary (Cyan)', 'flaw-gaming'),
            'accent'    => __('Accent (Yellow)', 'flaw-gaming'),
        ],
    ]);

    // Stat 4
    $wp_customize->add_setting('flaw_stat_4_value', [
        'default'           => '6+',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_4_value', [
        'label'   => __('Stat 4: Value', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_4_label', [
        'default'           => 'Games We Play',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_4_label', [
        'label'   => __('Stat 4: Label', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('flaw_stat_4_icon', [
        'default'           => 'games',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_4_icon', [
        'label'   => __('Stat 4: Icon', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'members' => __('Members (people)', 'flaw-gaming'),
            'trophy'  => __('Trophy', 'flaw-gaming'),
            'events'  => __('Events (calendar)', 'flaw-gaming'),
            'games'   => __('Games (controller)', 'flaw-gaming'),
        ],
    ]);

    $wp_customize->add_setting('flaw_stat_4_color', [
        'default'           => 'accent',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_stat_4_color', [
        'label'   => __('Stat 4: Color', 'flaw-gaming'),
        'section' => 'flaw_stats_section',
        'type'    => 'select',
        'choices' => [
            'primary'   => __('Primary (Red)', 'flaw-gaming'),
            'gold'      => __('Gold', 'flaw-gaming'),
            'secondary' => __('Secondary (Cyan)', 'flaw-gaming'),
            'accent'    => __('Accent (Yellow)', 'flaw-gaming'),
        ],
    ]);

    // ═══════════════════════════════════════════════════════════════
    // Members Section
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_members_section', [
        'title'       => __('Members Gallery', 'flaw-gaming'),
        'description' => __('Customize the members/players gallery section. Players are managed via the Players post type.', 'flaw-gaming'),
        'panel'       => 'flaw_theme_options',
        'priority'    => 36,
    ]);

    // Members Section Tagline
    $wp_customize->add_setting('flaw_members_tagline', [
        'default'           => 'Our Community',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_members_tagline', [
        'label'   => __('Section Tagline', 'flaw-gaming'),
        'section' => 'flaw_members_section',
        'type'    => 'text',
    ]);

    // Members Section Title
    $wp_customize->add_setting('flaw_members_title', [
        'default'           => 'Meet the FLAW Family',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_members_title', [
        'label'   => __('Section Title', 'flaw-gaming'),
        'section' => 'flaw_members_section',
        'type'    => 'text',
    ]);

    // Members Section Description
    $wp_customize->add_setting('flaw_members_description', [
        'default'           => 'Over 50 active members across competitive teams and content creation.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_members_description', [
        'label'   => __('Section Description', 'flaw-gaming'),
        'section' => 'flaw_members_section',
        'type'    => 'text',
    ]);

    // Members CTA Text
    $wp_customize->add_setting('flaw_members_cta_text', [
        'default'           => 'Join Our Community',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_members_cta_text', [
        'label'   => __('Button Text', 'flaw-gaming'),
        'section' => 'flaw_members_section',
        'type'    => 'text',
    ]);

    // Members CTA URL
    $wp_customize->add_setting('flaw_members_cta_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_members_cta_url', [
        'label'       => __('Button URL', 'flaw-gaming'),
        'description' => __('Leave empty to link to /join page.', 'flaw-gaming'),
        'section'     => 'flaw_members_section',
        'type'        => 'url',
    ]);

    // ═══════════════════════════════════════════════════════════════
    // Demo Data Section
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_demo_section', [
        'title'       => __('Demo / Placeholder Data', 'flaw-gaming'),
        'description' => __('Control placeholder content shown on empty sections of the front page.', 'flaw-gaming'),
        'panel'       => 'flaw_theme_options',
        'priority'    => 39,
    ]);

    $wp_customize->add_setting('flaw_show_demo_placeholders', [
        'default'           => true,
        'type'              => 'option',
        'sanitize_callback' => 'flaw_sanitize_checkbox',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_show_demo_placeholders', [
        'label'       => __('Show placeholder data', 'flaw-gaming'),
        'description' => __('When enabled, sections with no real content will display demo cards (Games, Teams, Events, Creators, Members). Disable once you have added real content.', 'flaw-gaming'),
        'section'     => 'flaw_demo_section',
        'type'        => 'checkbox',
    ]);

    // ═══════════════════════════════════════════════════════════════
    // Footer Section
    // ═══════════════════════════════════════════════════════════════

    $wp_customize->add_section('flaw_footer_section', [
        'title'    => __('Footer', 'flaw-gaming'),
        'panel'    => 'flaw_theme_options',
        'priority' => 40,
    ]);

    // Footer Tagline
    $wp_customize->add_setting('flaw_footer_tagline', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_footer_tagline', [
        'label'       => __('Footer Tagline', 'flaw-gaming'),
        'description' => __('Leave empty to use site description.', 'flaw-gaming'),
        'section'     => 'flaw_footer_section',
        'type'        => 'text',
    ]);

    // Copyright Text
    $wp_customize->add_setting('flaw_copyright_text', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('flaw_copyright_text', [
        'label'       => __('Copyright Text', 'flaw-gaming'),
        'description' => __('Leave empty for default copyright.', 'flaw-gaming'),
        'section'     => 'flaw_footer_section',
        'type'        => 'text',
    ]);
}
add_action('customize_register', 'flaw_customize_register');

/**
 * Sanitize checkbox value
 *
 * @param mixed $input Input value
 * @return bool
 */
function flaw_sanitize_checkbox($input) {
    return (bool) $input;
}

/**
 * Sanitize HTML content for hero title
 *
 * @param string $input Input string
 * @return string
 */
function flaw_sanitize_html($input) {
    return wp_kses($input, [
        'span'   => [],
        'strong' => [],
        'em'     => [],
        'br'     => [],
    ]);
}

/**
 * Get social links from Customizer
 *
 * @return array
 */
function flaw_get_social_links() {
    $platforms = ['discord', 'twitter', 'youtube', 'twitch', 'tiktok', 'instagram'];
    $links = [];

    foreach ($platforms as $platform) {
        $url = get_theme_mod("flaw_social_{$platform}", '');
        if (!empty($url)) {
            $links[$platform] = $url;
        }
    }

    return $links;
}

/**
 * Enqueue Customizer preview scripts
 */
function flaw_customize_preview_js() {
    wp_enqueue_script(
        'flaw-customizer',
        FLAW_THEME_URI . '/assets/js/customizer.js',
        ['customize-preview'],
        FLAW_THEME_VERSION,
        true
    );
}
add_action('customize_preview_init', 'flaw_customize_preview_js');
