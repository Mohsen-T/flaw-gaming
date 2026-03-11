<?php
/**
 * Join CTA Section Template
 *
 * @package FLAW_Gaming
 */
?>

<section class="join-cta" aria-labelledby="join-cta-heading">
    <div class="container">
        <div class="join-cta__content">
            <h2 id="join-cta-heading" class="join-cta__title">Want to Compete?</h2>
            <p class="join-cta__description">
                We're always looking for talented players to join our rosters.
                Think you have what it takes?
            </p>

            <a href="<?php echo esc_url(home_url('/join?type=player')); ?>" class="btn btn--primary">
                Apply as Player
            </a>
        </div>
    </div>
</section>
