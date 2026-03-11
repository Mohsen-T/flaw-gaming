<?php
/**
 * Members Gallery Section Template
 * Displays players from database or fallback demo data
 *
 * @package FLAW_Gaming
 */

// Generate avatar colors based on name
if (!function_exists('flaw_get_avatar_color')) {
    function flaw_get_avatar_color($name) {
        $colors = [
            '#D4A843', '#C9282D', '#E3FC02', '#9146FF', '#FF6B6B',
            '#B08B2A', '#E0C068', '#96CEB4', '#DDA0DD', '#98D8C8'
        ];
        return $colors[crc32($name) % count($colors)];
    }
}

// Try to get players from database
$members = [];
$has_players = false;

// Check if Pods is active and flaw_get_players exists
if (function_exists('flaw_get_players') && function_exists('flaw_is_pods_active') && flaw_is_pods_active()) {
    $players_pod = flaw_get_players(['status' => 'active', 'limit' => 20]);

    if ($players_pod && $players_pod->total() > 0) {
        $has_players = true;
        while ($players_pod->fetch()) {
            $gamertag = flaw_pick_value($players_pod->field('player_gamertag'));
            $name = !empty($gamertag) ? $gamertag : flaw_pick_value($players_pod->field('post_title'));
            $role = function_exists('flaw_get_player_role') ? flaw_get_player_role($players_pod->field('ID')) : '';
            $team = flaw_pick_value($players_pod->field('player_team.post_title'));
            $photo = flaw_pick_value($players_pod->field('player_photo._src'));

            $members[] = [
                'name'  => $name,
                'role'  => $role ?: 'Player',
                'team'  => $team ? preg_replace('/^FLAW\s*/i', '', $team) : '',
                'photo' => $photo,
            ];
        }
    }
}

// Fallback: Try WP_Query if Pods not available
if (!$has_players) {
    $players_query = new WP_Query([
        'post_type'      => 'player',
        'posts_per_page' => 20,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    if ($players_query->have_posts()) {
        $has_players = true;
        while ($players_query->have_posts()) {
            $players_query->the_post();
            $player_id = get_the_ID();

            // Get player meta (works with or without Pods)
            $gamertag = get_post_meta($player_id, 'player_gamertag', true);
            $role = function_exists('flaw_get_player_role') ? flaw_get_player_role($player_id) : '';
            $team_id = get_post_meta($player_id, 'player_team', true);
            $photo = get_post_meta($player_id, 'player_photo', true);

            // Get team name if team_id exists
            $team_name = '';
            if ($team_id) {
                $team_post = get_post($team_id);
                if ($team_post) {
                    $team_name = preg_replace('/^FLAW\s*/i', '', $team_post->post_title);
                }
            }

            // Get photo URL if it's an attachment ID
            $photo_url = '';
            if ($photo) {
                if (is_numeric($photo)) {
                    $photo_url = wp_get_attachment_image_url($photo, 'thumbnail');
                } else {
                    $photo_url = $photo;
                }
            }

            $members[] = [
                'name'  => !empty($gamertag) ? $gamertag : get_the_title(),
                'role'  => $role ?: 'Player',
                'team'  => $team_name,
                'photo' => $photo_url,
            ];
        }
        wp_reset_postdata();
    }
}

// Fallback to demo data if no players in database (respects placeholder toggle)
$show_demo = function_exists('flaw_show_demo_placeholders') ? flaw_show_demo_placeholders() : true;
$is_demo_data = false;

if (!$has_players && $show_demo) {
    $is_demo_data = true;
    $members = [
        ['name' => 'Phoenix', 'role' => 'IGL', 'team' => 'Alpha', 'photo' => ''],
        ['name' => 'Shadow', 'role' => 'Fragger', 'team' => 'Alpha', 'photo' => ''],
        ['name' => 'Blaze', 'role' => 'Support', 'team' => 'Alpha', 'photo' => ''],
        ['name' => 'Frost', 'role' => 'Flex', 'team' => 'Alpha', 'photo' => ''],
        ['name' => 'Storm', 'role' => 'Captain', 'team' => 'Bravo', 'photo' => ''],
        ['name' => 'Viper', 'role' => 'IGL', 'team' => 'Bravo', 'photo' => ''],
        ['name' => 'Ghost', 'role' => 'Fragger', 'team' => 'Bravo', 'photo' => ''],
        ['name' => 'Titan', 'role' => 'Support', 'team' => 'Bravo', 'photo' => ''],
        ['name' => 'Nova', 'role' => 'Flex', 'team' => 'Charlie', 'photo' => ''],
        ['name' => 'Cipher', 'role' => 'Captain', 'team' => 'Charlie', 'photo' => ''],
        ['name' => 'Reaper', 'role' => 'IGL', 'team' => 'Charlie', 'photo' => ''],
        ['name' => 'Sage', 'role' => 'Support', 'team' => 'Charlie', 'photo' => ''],
        ['name' => 'Jett', 'role' => 'Creator', 'team' => 'Content', 'photo' => ''],
        ['name' => 'Raze', 'role' => 'Creator', 'team' => 'Content', 'photo' => ''],
        ['name' => 'Omen', 'role' => 'Creator', 'team' => 'Content', 'photo' => ''],
        ['name' => 'Breach', 'role' => 'Coach', 'team' => 'Staff', 'photo' => ''],
    ];
}

// If no players and demo disabled, don't render the section
if (empty($members)) {
    return;
}

// Get customizable section text
$section_tagline = get_theme_mod('flaw_members_tagline', 'Our Community');
$section_title = get_theme_mod('flaw_members_title', 'Meet the FLAW Family');
$section_description = get_theme_mod('flaw_members_description', 'Over 50 active members across competitive teams and content creation.');
$section_cta_text = get_theme_mod('flaw_members_cta_text', 'Join Our Community');
$section_cta_url = get_theme_mod('flaw_members_cta_url', home_url('/join'));
?>

<section class="members-section" aria-labelledby="members-heading">
    <div class="container">
        <header class="section-header section-header--center">
            <p class="section-tagline"><?php echo esc_html($section_tagline); ?></p>
            <h2 id="members-heading" class="section-title"><?php echo esc_html($section_title); ?></h2>
            <p class="section-description"><?php echo esc_html($section_description); ?></p>
        </header>

        <div class="members-grid">
            <?php if ($is_demo_data && current_user_can('manage_options')) : ?>
                <div class="demo-badge" style="grid-column: 1 / -1;">Demo Data</div>
            <?php endif; ?>
            <?php foreach ($members as $member) :
                $color = flaw_get_avatar_color($member['name']);
                $initials = strtoupper(substr($member['name'], 0, 2));
                $has_photo = !empty($member['photo']);
            ?>
                <div class="member-card">
                    <div class="member-card__avatar" style="--avatar-color: <?php echo esc_attr($color); ?>">
                        <?php if ($has_photo) : ?>
                            <img src="<?php echo esc_url($member['photo']); ?>" alt="<?php echo esc_attr($member['name']); ?>" class="member-card__photo">
                        <?php else : ?>
                            <span class="member-card__initials"><?php echo esc_html($initials); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="member-card__info">
                        <span class="member-card__name"><?php echo esc_html($member['name']); ?></span>
                        <span class="member-card__role"><?php echo esc_html($member['role']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="members-cta">
            <a href="<?php echo esc_url($section_cta_url); ?>" class="btn btn--primary btn--lg">
                <?php echo esc_html($section_cta_text); ?>
            </a>
        </div>
    </div>
</section>

<style>
.members-section {
    padding: var(--space-20) 0;
    background: var(--color-bg-primary);
}

.section-description {
    color: var(--color-text-secondary);
    font-size: var(--text-lg);
    max-width: 500px;
    margin: var(--space-4) auto 0;
}

.members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: var(--space-4);
    margin-top: var(--space-12);
}

@media (min-width: 768px) {
    .members-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}

.member-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: var(--space-4);
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    transition: all var(--transition-normal);
    cursor: pointer;
}

.member-card:hover {
    border-color: var(--avatar-color, var(--color-primary));
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.member-card__avatar {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-full);
    background: linear-gradient(135deg, var(--avatar-color) 0%, color-mix(in srgb, var(--avatar-color) 70%, black) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-3);
    box-shadow: 0 0 20px color-mix(in srgb, var(--avatar-color) 30%, transparent);
    transition: all var(--transition-normal);
}

.member-card:hover .member-card__avatar {
    box-shadow: 0 0 30px color-mix(in srgb, var(--avatar-color) 50%, transparent);
    transform: scale(1.1);
}

.member-card__initials {
    font-family: var(--font-display);
    font-size: var(--text-xl);
    font-weight: var(--font-bold);
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.member-card__photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-full);
}

.member-card__info {
    text-align: center;
}

.member-card__name {
    display: block;
    font-family: var(--font-display);
    font-size: var(--text-sm);
    font-weight: var(--font-bold);
    color: var(--color-text-primary);
    margin-bottom: var(--space-1);
}

.member-card__role {
    display: block;
    font-size: var(--text-xs);
    color: var(--color-text-tertiary);
    text-transform: uppercase;
    letter-spacing: var(--tracking-wide);
}

.members-cta {
    display: flex;
    justify-content: center;
    margin-top: var(--space-12);
}
</style>
