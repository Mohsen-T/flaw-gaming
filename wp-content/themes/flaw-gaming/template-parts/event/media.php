<?php
/**
 * Event Media/Archive Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$vod_id = flaw_pick_value($pod->field('event_vod_id'));
$vod_platform = flaw_pick_value($pod->field('event_vod_platform'), 'youtube');
$vod_url = flaw_pick_value($pod->field('event_vod_url'));
$photos = $pod->field('event_photos_gallery'); // intentionally array
$photos_url = flaw_pick_value($pod->field('event_photos_external_url'));
$press_links_raw = $pod->field('event_press_links'); // intentionally array

if (empty($vod_id) && empty($photos)) {
    return;
}
?>

<section class="event-section event-media" aria-labelledby="media-heading">
    <header class="event-section__header">
        <h2 id="media-heading" class="event-section__title">Media</h2>
    </header>

    <div class="event-section__content">
        <?php if (!empty($vod_id)) : ?>
            <div class="media-vod">
                <h3>VOD</h3>
                <?php if ($vod_platform === 'youtube') : ?>
                    <div class="video-embed">
                        <iframe
                            src="https://www.youtube.com/embed/<?php echo esc_attr($vod_id); ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                <?php elseif ($vod_platform === 'twitch') : ?>
                    <div class="video-embed">
                        <iframe
                            src="https://player.twitch.tv/?video=<?php echo esc_attr($vod_id); ?>&parent=<?php echo esc_attr($_SERVER['HTTP_HOST']); ?>"
                            frameborder="0"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                <?php endif; ?>

                <?php if (!empty($vod_url)) : ?>
                    <a href="<?php echo esc_url($vod_url); ?>"
                       class="btn btn--outline btn--sm"
                       target="_blank"
                       rel="noopener noreferrer">
                        Watch Full VOD
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($photos) && is_array($photos)) : ?>
            <div class="media-photos">
                <h3>Photos</h3>
                <div class="photos-grid">
                    <?php foreach ($photos as $photo) : ?>
                        <?php
                        $photo_url = is_array($photo) ? ($photo['guid'] ?? '') : $photo;
                        $photo_thumb = is_array($photo) ? ($photo['thumbnail'] ?? $photo_url) : $photo;
                        ?>
                        <a href="<?php echo esc_url($photo_url); ?>"
                           class="photo-item"
                           data-lightbox="event-photos">
                            <img src="<?php echo esc_url($photo_thumb); ?>"
                                 alt=""
                                 loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($photos_url)) : ?>
                    <a href="<?php echo esc_url($photos_url); ?>"
                       class="btn btn--outline btn--sm"
                       target="_blank"
                       rel="noopener noreferrer">
                        View All Photos
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
