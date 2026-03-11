<?php
/**
 * Teams Archive Template (Roster Page)
 *
 * Displays teams grouped by game with filter tabs,
 * plus Content Creators section.
 *
 * @package FLAW_Gaming
 */

get_header();

// Get all games that have teams
$games_data = [];
if (function_exists('flaw_get_games_with_active_teams')) {
    $games = flaw_get_games_with_active_teams();
    if ($games && $games->total()) {
        while ($games->fetch()) {
            $game_id = $games->field('ID');
            $games_data[] = [
                'id'    => $game_id,
                'title' => $games->field('post_title'),
                'slug'  => get_post_field('post_name', $game_id),
                'logo'  => get_the_post_thumbnail_url($game_id, 'team-logo'),
            ];
        }
    }
}
?>

<main id="main" class="site-main">
    <header class="archive-header archive-header--roster">
        <div class="container">
            <h1 class="archive-title">Our Roster</h1>
            <p class="archive-description">
                Meet the competitors and creators who represent FLAW Gaming across multiple titles.
            </p>
        </div>
    </header>

    <?php if (!empty($games_data)) : ?>
    <!-- Game Filter Tabs -->
    <div class="roster-filters">
        <div class="container">
            <div class="filter-tabs" role="tablist" aria-label="Filter by game">
                <button class="filter-tab is-active"
                        role="tab"
                        aria-selected="true"
                        data-filter="all">
                    All
                </button>
                <?php foreach ($games_data as $game) : ?>
                    <button class="filter-tab"
                            role="tab"
                            aria-selected="false"
                            data-filter="game-<?php echo esc_attr($game['id']); ?>">
                        <?php if (!empty($game['logo'])) : ?>
                            <img src="<?php echo esc_url($game['logo']); ?>"
                                 alt=""
                                 class="filter-tab__icon"
                                 loading="lazy">
                        <?php endif; ?>
                        <?php echo esc_html($game['title']); ?>
                    </button>
                <?php endforeach; ?>
                <button class="filter-tab"
                        role="tab"
                        aria-selected="false"
                        data-filter="creators">
                    Creators
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Teams Grouped by Game -->
    <div class="roster-content">
        <?php
        if (function_exists('flaw_get_active_teams')) :
            foreach ($games_data as $game) :
                $teams = flaw_get_active_teams($game['id']);
                if ($teams && $teams->total()) :
        ?>
            <section class="section section--game-roster roster-game-section"
                     id="game-<?php echo esc_attr($game['slug']); ?>"
                     data-game="game-<?php echo esc_attr($game['id']); ?>"
                     aria-labelledby="game-heading-<?php echo esc_attr($game['id']); ?>">
                <div class="container">
                    <header class="section-header section-header--game">
                        <?php if (!empty($game['logo'])) : ?>
                            <img src="<?php echo esc_url($game['logo']); ?>"
                                 alt=""
                                 class="game-section-icon"
                                 loading="lazy">
                        <?php endif; ?>
                        <h2 id="game-heading-<?php echo esc_attr($game['id']); ?>"
                            class="section-title">
                            <?php echo esc_html($game['title']); ?>
                        </h2>
                    </header>

                    <div class="teams-grid">
                        <?php
                        while ($teams->fetch()) :
                            $team_id = $teams->field('ID');
                            $data = [
                                'id'        => $team_id,
                                'title'     => $teams->field('post_title'),
                                'permalink' => get_permalink($team_id),
                                'thumbnail' => get_the_post_thumbnail_url($team_id, 'card-thumbnail'),
                                'game'      => [
                                    'title' => $game['title'],
                                    'logo'  => $game['logo'],
                                ],
                                'region'    => flaw_pick_value(get_post_meta($team_id, 'team_region', true)),
                                'status'    => flaw_pick_value(get_post_meta($team_id, 'team_status', true), 'active'),
                            ];
                            flaw_render_card('team', $data);
                        endwhile;
                        ?>
                    </div>
                </div>
            </section>
        <?php
                endif;
            endforeach;
        endif;
        ?>

        <?php
        // Show any teams not assigned to a game
        if (function_exists('flaw_get_all_teams')) :
            $all_teams = flaw_get_all_teams();
            $game_ids = array_column($games_data, 'id');
            $ungrouped = [];

            if ($all_teams && $all_teams->total()) :
                while ($all_teams->fetch()) :
                    $team_id = $all_teams->field('ID');
                    $team_game = get_post_meta($team_id, 'team_game', true);
                    if (empty($team_game) || !in_array($team_game, $game_ids)) :
                        $ungrouped[] = [
                            'id'        => $team_id,
                            'title'     => $all_teams->field('post_title'),
                            'permalink' => get_permalink($team_id),
                            'thumbnail' => get_the_post_thumbnail_url($team_id, 'card-thumbnail'),
                            'game'      => get_post_meta($team_id, 'team_game', true),
                            'region'    => flaw_pick_value(get_post_meta($team_id, 'team_region', true)),
                            'status'    => flaw_pick_value(get_post_meta($team_id, 'team_status', true), 'active'),
                        ];
                    endif;
                endwhile;
            endif;

            if (!empty($ungrouped)) :
        ?>
            <section class="section section--game-roster roster-game-section"
                     data-game="all"
                     aria-labelledby="other-teams-heading">
                <div class="container">
                    <header class="section-header">
                        <h2 id="other-teams-heading" class="section-title">Other Teams</h2>
                    </header>
                    <div class="teams-grid">
                        <?php foreach ($ungrouped as $data) : flaw_render_card('team', $data); endforeach; ?>
                    </div>
                </div>
            </section>
        <?php
            endif;
        endif;
        ?>
    </div>

    <!-- Content Creators Section -->
    <section class="section section--creators roster-creators-section"
             id="creators"
             data-game="creators"
             aria-labelledby="creators-roster-heading">
        <div class="container">
            <header class="section-header">
                <h2 id="creators-roster-heading" class="section-title">Content Creators</h2>
            </header>
            <p class="section-description">
                Streamers, video creators, and community personalities who bring FLAW Gaming to life.
            </p>

            <?php
            if (function_exists('flaw_get_creators')) :
                $creators = flaw_get_creators();
                if ($creators && $creators->total()) :
            ?>
                <div class="creators-grid">
                    <?php
                    while ($creators->fetch()) :
                        flaw_render_card_from_pod('creator', $creators);
                    endwhile;
                    ?>
                </div>
            <?php
                else :
            ?>
                <p class="no-results">No creators found yet. Stay tuned!</p>
            <?php
                endif;
            endif;
            ?>
        </div>
    </section>

    <?php get_template_part('template-parts/sections/join-cta'); ?>
</main>

<style>
/* Roster Page Header */
.archive-header--roster {
    background:
        radial-gradient(ellipse at 30% 0%, rgba(212, 168, 67, 0.15) 0%, transparent 50%),
        radial-gradient(ellipse at 70% 100%, rgba(201, 40, 45, 0.1) 0%, transparent 50%),
        var(--color-bg-secondary);
}

/* Roster Grids — 4 columns on desktop like Merciless */
.teams-grid,
.creators-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: var(--space-6);
}

@media (min-width: 640px) {
    .teams-grid,
    .creators-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .teams-grid,
    .creators-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1280px) {
    .teams-grid,
    .creators-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Filter Tabs */
.roster-filters {
    position: sticky;
    top: var(--header-height);
    z-index: calc(var(--z-header) - 1);
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--color-border);
    padding: var(--space-4) 0;
}

.filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
    justify-content: center;
}

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-2) var(--space-4);
    background: var(--color-bg-tertiary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-full);
    color: var(--color-text-secondary);
    font-family: var(--font-display);
    font-size: var(--text-sm);
    font-weight: var(--font-semibold);
    text-transform: uppercase;
    letter-spacing: var(--tracking-wide);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filter-tab:hover {
    border-color: var(--color-primary);
    color: var(--color-text-primary);
}

.filter-tab.is-active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #000000;
    box-shadow: 0 0 15px rgba(212, 168, 67, 0.3);
}

.filter-tab__icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

/* Game Section Visibility for Filtering */
.roster-game-section.is-hidden,
.roster-creators-section.is-hidden {
    display: none;
}

/* Game section header with icon */
.section-header--game {
    display: flex;
    align-items: center;
    gap: var(--space-4);
}

.game-section-icon {
    width: 40px;
    height: 40px;
    object-fit: contain;
}

/* Section description */
.section-description {
    color: var(--color-text-secondary);
    margin-bottom: var(--space-8);
    max-width: 600px;
}
</style>

<script>
(function() {
    'use strict';

    var filterTabs = document.querySelectorAll('.filter-tab');
    var gameSections = document.querySelectorAll('.roster-game-section');
    var creatorsSection = document.querySelector('.roster-creators-section');

    if (!filterTabs.length) return;

    filterTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var filter = this.dataset.filter;

            // Update active tab
            filterTabs.forEach(function(t) {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            this.classList.add('is-active');
            this.setAttribute('aria-selected', 'true');

            // Filter sections
            if (filter === 'all') {
                gameSections.forEach(function(s) { s.classList.remove('is-hidden'); });
                if (creatorsSection) creatorsSection.classList.remove('is-hidden');
            } else if (filter === 'creators') {
                gameSections.forEach(function(s) { s.classList.add('is-hidden'); });
                if (creatorsSection) creatorsSection.classList.remove('is-hidden');
            } else {
                gameSections.forEach(function(s) {
                    if (s.dataset.game === filter) {
                        s.classList.remove('is-hidden');
                    } else {
                        s.classList.add('is-hidden');
                    }
                });
                if (creatorsSection) creatorsSection.classList.add('is-hidden');
            }

            // Smooth scroll to content area
            var content = document.querySelector('.roster-content');
            if (content) {
                content.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Handle URL hash for direct linking (e.g., /teams/#creators)
    function handleHash() {
        var hash = window.location.hash.replace('#', '');
        if (hash === 'creators' && creatorsSection) {
            creatorsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    if (window.location.hash) {
        setTimeout(handleHash, 300);
    }

    // Handle ?game= query parameter for filtering
    var urlParams = new URLSearchParams(window.location.search);
    var gameFilter = urlParams.get('game');
    if (gameFilter) {
        var targetTab = document.querySelector('.filter-tab[data-filter="game-' + gameFilter + '"]');
        if (targetTab) {
            targetTab.click();
        }
    }
})();
</script>

<?php
get_footer();
