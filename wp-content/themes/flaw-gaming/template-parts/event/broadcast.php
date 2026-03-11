<?php
/**
 * Event Broadcast Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$channel = flaw_pick_value($pod->field('event_stream_channel'));

if (empty($channel)) {
    return;
}

$platform = flaw_pick_value($pod->field('event_stream_platform'), 'twitch');
$chat_enabled = (bool) $pod->field('event_stream_chat_enabled');
$bracket_url = flaw_pick_value($pod->field('event_bracket_live_url'));
$backup_url = flaw_pick_value($pod->field('event_stream_url_backup'));
?>

<section class="event-section event-broadcast" aria-labelledby="broadcast-heading">
    <header class="event-section__header">
        <h2 id="broadcast-heading" class="event-section__title">
            <span class="live-pulse"></span>
            Live Stream
        </h2>

        <?php if (function_exists('flaw_the_twitch_status')) : ?>
            <?php flaw_the_twitch_status($channel); ?>
        <?php endif; ?>
    </header>

    <div class="event-section__content">
        <?php if ($platform === 'twitch') : ?>
            <div class="stream-container <?php echo $chat_enabled ? 'stream-container--with-chat' : ''; ?>">
                <div class="stream-embed"
                     id="twitch-embed"
                     <?php echo function_exists('flaw_twitch_embed_attrs') ? flaw_twitch_embed_attrs($channel, $chat_enabled) : ''; ?>>
                    <div class="stream-embed__placeholder">
                        <a href="https://twitch.tv/<?php echo esc_attr($channel); ?>"
                           class="btn btn--primary"
                           target="_blank"
                           rel="noopener noreferrer">
                            Watch on Twitch
                        </a>
                    </div>
                </div>

                <?php if ($chat_enabled) : ?>
                    <div class="stream-chat" id="twitch-chat"></div>
                <?php endif; ?>
            </div>
        <?php elseif ($platform === 'youtube') : ?>
            <div class="stream-container">
                <div class="stream-embed">
                    <iframe
                        src="https://www.youtube.com/embed/<?php echo esc_attr($channel); ?>?autoplay=1"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($bracket_url)) : ?>
            <div class="broadcast-bracket">
                <a href="<?php echo esc_url($bracket_url); ?>"
                   class="btn btn--outline"
                   target="_blank"
                   rel="noopener noreferrer">
                    View Live Bracket
                </a>
            </div>
        <?php endif; ?>

        <?php if (!empty($backup_url)) : ?>
            <p class="broadcast-backup">
                Stream not working?
                <a href="<?php echo esc_url($backup_url); ?>" target="_blank" rel="noopener noreferrer">
                    Try backup stream
                </a>
            </p>
        <?php endif; ?>
    </div>
</section>
