/**
 * Customizer Live Preview
 *
 * @package FLAW_Gaming
 */

(function($) {
    'use strict';

    // Hero Tagline
    wp.customize('flaw_hero_tagline', function(value) {
        value.bind(function(newval) {
            $('.hero__tagline').text(newval);
        });
    });

    // Hero Title
    wp.customize('flaw_hero_title', function(value) {
        value.bind(function(newval) {
            $('.hero__title').html(newval);
        });
    });

    // Hero Subtitle
    wp.customize('flaw_hero_subtitle', function(value) {
        value.bind(function(newval) {
            $('.hero__subtitle').text(newval);
        });
    });

    // Hero CTA Text
    wp.customize('flaw_hero_cta_text', function(value) {
        value.bind(function(newval) {
            $('.hero__actions .btn--discord').text(newval);
        });
    });

    // Hero Secondary Text
    wp.customize('flaw_hero_secondary_text', function(value) {
        value.bind(function(newval) {
            $('.hero__actions .btn--outline').text(newval);
        });
    });

    // Discord Title
    wp.customize('flaw_discord_title', function(value) {
        value.bind(function(newval) {
            $('.discord-cta__title').text(newval);
        });
    });

    // Discord Description
    wp.customize('flaw_discord_description', function(value) {
        value.bind(function(newval) {
            $('.discord-cta__description').text(newval);
        });
    });

})(jQuery);
