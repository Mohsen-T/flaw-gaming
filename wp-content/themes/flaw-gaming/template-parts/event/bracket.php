<?php
/**
 * Event Bracket Template Part
 * Displays embedded tournament bracket from external services
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;
$state = $args['state'] ?? 'upcoming';

if (!$pod) {
    return;
}

// Determine which bracket URL to use based on event state
$bracket_url = '';
$bracket_label = 'Tournament Bracket';

if ($state === 'live') {
    $bracket_url = flaw_pick_value($pod->field('event_bracket_live_url'));
    $bracket_label = 'Live Bracket';
} elseif ($state === 'completed') {
    $bracket_url = flaw_pick_value($pod->field('event_final_bracket_url'));
    $bracket_label = 'Final Bracket';
}

// Fallback to live bracket if final bracket doesn't exist
if (empty($bracket_url) && $state === 'completed') {
    $bracket_url = flaw_pick_value($pod->field('event_bracket_live_url'));
}

if (empty($bracket_url)) {
    return;
}

// Detect bracket service and format embed URL
$embed_url = $bracket_url;
$service = '';

if (strpos($bracket_url, 'challonge.com') !== false) {
    $service = 'challonge';
    // Convert to embed URL if not already
    if (strpos($bracket_url, '/module') === false) {
        $embed_url = str_replace('challonge.com/', 'challonge.com/module?', $bracket_url);
    }
} elseif (strpos($bracket_url, 'battlefy.com') !== false) {
    $service = 'battlefy';
    // Battlefy embed format
    if (preg_match('/battlefy\.com\/[\w-]+\/([\w-]+)/', $bracket_url, $matches)) {
        $embed_url = 'https://battlefy.com/embed/tournament/' . $matches[1];
    }
} elseif (strpos($bracket_url, 'start.gg') !== false || strpos($bracket_url, 'smash.gg') !== false) {
    $service = 'startgg';
    // Start.gg uses direct URLs with /brackets view
    $embed_url = $bracket_url;
}
?>

<section class="event-section event-bracket" aria-labelledby="bracket-heading">
    <header class="event-section__header">
        <h2 id="bracket-heading" class="event-section__title"><?php echo esc_html($bracket_label); ?></h2>
    </header>

    <div class="event-section__content">
        <div class="bracket-embed" data-service="<?php echo esc_attr($service); ?>">
            <iframe src="<?php echo esc_url($embed_url); ?>"
                    class="bracket-embed__iframe"
                    frameborder="0"
                    scrolling="auto"
                    allowtransparency="true"
                    loading="lazy">
            </iframe>
        </div>

        <div class="bracket-fallback">
            <p>Bracket not loading? <a href="<?php echo esc_url($bracket_url); ?>" target="_blank" rel="noopener noreferrer">View in new window</a></p>
        </div>
    </div>
</section>

<style>
.event-bracket {
    margin-top: var(--space-8);
}

.bracket-embed {
    position: relative;
    width: 100%;
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.bracket-embed__iframe {
    width: 100%;
    min-height: 600px;
    border: none;
    display: block;
}

/* Service-specific sizing */
.bracket-embed[data-service="challonge"] .bracket-embed__iframe {
    min-height: 700px;
}

.bracket-embed[data-service="battlefy"] .bracket-embed__iframe {
    min-height: 800px;
}

.bracket-embed[data-service="startgg"] .bracket-embed__iframe {
    min-height: 900px;
}

.bracket-fallback {
    margin-top: var(--space-4);
    text-align: center;
    color: var(--color-text-secondary);
    font-size: var(--text-sm);
}

.bracket-fallback a {
    color: var(--color-primary);
    text-decoration: underline;
    transition: color var(--transition-fast);
}

.bracket-fallback a:hover {
    color: var(--color-primary-light);
}

@media (max-width: 768px) {
    .bracket-embed__iframe {
        min-height: 500px;
    }
}
</style>
