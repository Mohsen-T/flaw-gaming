<?php
/**
 * Event Partners Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;

if (!$pod) {
    return;
}

$partners = $pod->field('event_partners');

if (empty($partners)) {
    return;
}
?>

<section class="event-section event-partners" aria-labelledby="partners-heading">
    <header class="event-section__header">
        <h3 id="partners-heading" class="event-section__title">Event Partners</h3>
    </header>

    <div class="event-section__content">
        <ul class="partners-list">
            <?php foreach ($partners as $partner) : ?>
                <?php
                $partner_id = is_array($partner) ? ($partner['ID'] ?? 0) : $partner;
                if (!$partner_id) continue;

                $partner_pod = pods('partner', $partner_id);
                if (!$partner_pod->exists()) continue;

                $logo = flaw_get_image_url($partner_pod, 'partner_logo');
                $title = flaw_pick_value($partner_pod->field('post_title'));
                $website = flaw_pick_value($partner_pod->field('partner_website'));
                ?>
                <li class="partners-list__item">
                    <?php if ($website) : ?>
                        <a href="<?php echo esc_url($website); ?>"
                           class="partner-link"
                           target="_blank"
                           rel="noopener noreferrer">
                    <?php else : ?>
                        <span class="partner-link">
                    <?php endif; ?>
                        <?php if ($logo) : ?>
                            <img src="<?php echo esc_url($logo); ?>"
                                 alt="<?php echo esc_attr($title); ?>"
                                 class="partner-link__logo"
                                 loading="lazy">
                        <?php else : ?>
                            <span class="partner-link__name"><?php echo esc_html($title); ?></span>
                        <?php endif; ?>
                    <?php echo $website ? '</a>' : '</span>'; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
