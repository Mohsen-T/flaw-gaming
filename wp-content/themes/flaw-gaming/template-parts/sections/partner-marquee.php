<?php
/**
 * Partner Marquee Section Template
 * Auto-scrolling partner logos inspired by Merciless Hub
 *
 * @package FLAW_Gaming
 */

// Get partners
$partners = [];
if (function_exists('flaw_get_partners_by_tier')) {
    $partners_by_tier = flaw_get_partners_by_tier();
    $partners = array_merge(
        $partners_by_tier['platinum'] ?? [],
        $partners_by_tier['gold'] ?? [],
        $partners_by_tier['silver'] ?? [],
        $partners_by_tier['bronze'] ?? []
    );
}

// Use demo partners if none exist (for theme preview)
if (empty($partners)) {
    $partners = [
        ['title' => 'GameFuel', 'logo' => '', 'website' => '#'],
        ['title' => 'ProGear', 'logo' => '', 'website' => '#'],
        ['title' => 'StreamTech', 'logo' => '', 'website' => '#'],
        ['title' => 'PixelPerfect', 'logo' => '', 'website' => '#'],
        ['title' => 'CloudNine', 'logo' => '', 'website' => '#'],
        ['title' => 'ByteSpeed', 'logo' => '', 'website' => '#'],
    ];
}

// Double the partners array for seamless loop
$partners_loop = array_merge($partners, $partners);
?>

<section class="marquee-section" aria-label="Our Partners">
    <p class="marquee-section__title">Trusted By</p>

    <div class="marquee">
        <div class="marquee__content">
            <?php foreach ($partners_loop as $partner) : ?>
                <div class="marquee__item">
                    <?php if (!empty($partner['website'])) : ?>
                        <a href="<?php echo esc_url($partner['website']); ?>" target="_blank" rel="noopener noreferrer">
                    <?php endif; ?>

                    <?php if (!empty($partner['logo'])) : ?>
                        <img src="<?php echo esc_url($partner['logo']); ?>"
                             alt="<?php echo esc_attr($partner['title']); ?>"
                             class="marquee__logo"
                             loading="lazy">
                    <?php else : ?>
                        <span class="marquee__text"><?php echo esc_html($partner['title']); ?></span>
                    <?php endif; ?>

                    <?php if (!empty($partner['website'])) : ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
