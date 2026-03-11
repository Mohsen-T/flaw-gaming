<?php
/**
 * Creator CTA Section Template
 *
 * @package FLAW_Gaming
 */
?>

<section class="creator-cta" aria-labelledby="creator-cta-heading">
    <div class="container">
        <div class="creator-cta__content">
            <h2 id="creator-cta-heading" class="creator-cta__title">Content Creator?</h2>
            <p class="creator-cta__description">
                Join our creator program and get access to exclusive events,
                sponsorship opportunities, and a community of like-minded creators.
            </p>

            <a href="<?php echo esc_url(home_url('/join?type=creator')); ?>" class="btn btn--primary">
                Apply as Creator
            </a>
        </div>
    </div>
</section>
