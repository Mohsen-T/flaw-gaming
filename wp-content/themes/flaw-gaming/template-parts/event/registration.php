<?php
/**
 * Event Registration Template Part
 *
 * @package FLAW_Gaming
 */

$pod = $args['pod'] ?? null;
$event_id = $args['event_id'] ?? get_the_ID();

if (!$pod) {
    return;
}

// Get registration enabled - handle Pods boolean (may return 1, '1', true, or array)
$enabled_raw = $pod->field('event_registration_enabled');
$enabled = !empty(flaw_pick_value(is_array($enabled_raw) ? $enabled_raw : (string) $enabled_raw));

if (!$enabled) {
    return;
}

// Get registration URL (for external links like Google Forms)
$url = flaw_pick_value($pod->field('event_registration_url'));

// Get embedded form config (form plugin + ID approach - avoids Pods shortcode sanitization)
$form_plugin = flaw_pick_value($pod->field('event_registration_form_plugin'), 'none');
$form_id = (int) flaw_pick_value($pod->field('event_registration_form_id'));

// Build shortcode from form plugin + ID
$form_shortcode = '';
if ($form_id > 0 && $form_plugin !== 'none') {
    switch ($form_plugin) {
        case 'wpforms':
            $form_shortcode = '[wpforms id="' . $form_id . '"]';
            break;
        case 'gravityforms':
            $form_shortcode = '[gravityform id="' . $form_id . '" title="false" description="false"]';
            break;
        case 'cf7':
            $form_shortcode = '[contact-form-7 id="' . $form_id . '"]';
            break;
    }
}

$deadline = flaw_pick_value($pod->field('event_registration_deadline'));
$slots_total = (int) flaw_pick_value($pod->field('event_registration_slots_total'));
$slots_filled = (int) flaw_pick_value($pod->field('event_registration_slots_filled'));
$requirements = flaw_pick_value($pod->field('event_registration_requirements'));
$fee = flaw_pick_value($pod->field('event_entry_fee'));
$fee_token = flaw_pick_value($pod->field('event_entry_fee_token'));

$is_open = true;
if ($deadline) {
    $is_open = strtotime($deadline) > current_time('timestamp');
}
if ($slots_total > 0 && $slots_filled >= $slots_total) {
    $is_open = false;
}
?>

<section class="event-section event-registration" aria-labelledby="registration-heading">
    <header class="event-section__header">
        <h2 id="registration-heading" class="event-section__title">Registration</h2>

        <?php if ($is_open) : ?>
            <span class="registration-status registration-status--open">Open</span>
        <?php else : ?>
            <span class="registration-status registration-status--closed">Closed</span>
        <?php endif; ?>
    </header>

    <div class="event-section__content">
        <?php if ($deadline) : ?>
            <div class="registration-deadline">
                <span class="registration-deadline__label">Registration closes:</span>
                <time datetime="<?php echo esc_attr($deadline); ?>">
                    <?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($deadline))); ?>
                </time>
            </div>
        <?php endif; ?>

        <?php if ($slots_total) : ?>
            <div class="registration-slots">
                <div class="registration-slots__bar">
                    <?php $percentage = ($slots_filled / $slots_total) * 100; ?>
                    <div class="registration-slots__fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                </div>
                <span class="registration-slots__text">
                    <?php echo esc_html($slots_filled); ?> / <?php echo esc_html($slots_total); ?> slots filled
                </span>
            </div>
        <?php endif; ?>

        <?php if ($fee) : ?>
            <div class="registration-fee">
                <span class="registration-fee__label">Entry Fee:</span>
                <span class="registration-fee__value">
                    <?php echo esc_html($fee); ?>
                    <?php if ($fee_token) : ?>
                        <span class="registration-fee__token"><?php echo esc_html($fee_token); ?></span>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($requirements) : ?>
            <div class="registration-requirements">
                <h3>Requirements</h3>
                <div class="prose">
                    <?php echo wp_kses_post($requirements); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($is_open) : ?>
            <?php if (!empty($form_shortcode)) : ?>
                <!-- Embedded form -->
                <div class="registration-form">
                    <?php echo do_shortcode($form_shortcode); ?>
                </div>
            <?php elseif (!empty($url)) : ?>
                <!-- External registration link -->
                <div class="registration-cta">
                    <a href="<?php echo esc_url($url); ?>"
                       class="btn btn--primary btn--lg"
                       target="_blank"
                       rel="noopener noreferrer">
                        Register Now
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
/* Registration Form Styling */
.registration-form {
    margin-top: var(--space-6);
    padding: var(--space-6);
    background: var(--color-bg-tertiary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
}

/* Style form plugin inputs to match theme */
.registration-form input[type="text"],
.registration-form input[type="email"],
.registration-form input[type="tel"],
.registration-form input[type="url"],
.registration-form input[type="number"],
.registration-form textarea,
.registration-form select {
    width: 100%;
    padding: var(--space-3);
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    color: var(--color-text-primary);
    font-family: var(--font-primary);
    font-size: var(--text-base);
    transition: border-color var(--transition-fast);
}

.registration-form input:focus,
.registration-form textarea:focus,
.registration-form select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.1);
}

.registration-form label {
    display: block;
    margin-bottom: var(--space-2);
    color: var(--color-text-primary);
    font-weight: var(--font-medium);
    font-size: var(--text-sm);
}

.registration-form .gfield_required,
.registration-form .required {
    color: var(--color-error);
}

/* Form submit buttons */
.registration-form input[type="submit"],
.registration-form button[type="submit"],
.registration-form .gform_button,
.registration-form .wpforms-submit {
    display: inline-block;
    padding: var(--space-3) var(--space-6);
    background: var(--color-primary);
    color: #000000;
    border: none;
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: var(--text-base);
    font-weight: var(--font-semibold);
    text-transform: uppercase;
    letter-spacing: var(--tracking-wide);
    cursor: pointer;
    transition: all var(--transition-normal);
}

.registration-form input[type="submit"]:hover,
.registration-form button[type="submit"]:hover {
    background: var(--color-primary-light);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 168, 67, 0.3);
}

/* Gravity Forms specific */
.registration-form .gform_wrapper {
    max-width: 100%;
}

.registration-form .gform_fields {
    list-style: none;
    padding: 0;
    margin: 0;
}

.registration-form .gfield {
    margin-bottom: var(--space-4);
}

.registration-form .gfield_error {
    background: rgba(201, 40, 45, 0.1);
    border-left: 3px solid var(--color-error);
    padding: var(--space-3);
    border-radius: var(--radius-md);
}

.registration-form .validation_message {
    color: var(--color-error);
    font-size: var(--text-sm);
    margin-top: var(--space-1);
}

/* WPForms specific — force dark theme colors */
.registration-form .wpforms-container {
    background: transparent !important;
}

.registration-form .wpforms-container * {
    border-color: var(--color-border) !important;
}

.registration-form .wpforms-field {
    margin-bottom: var(--space-4);
}

.registration-form .wpforms-field-label,
.registration-form .wpforms-field-sublabel,
.registration-form .wpforms-field-description,
.registration-form .wpforms-field label,
.registration-form .wpforms-field .wpforms-field-label-inline,
.registration-form .wpforms-container .wpforms-field-label,
.registration-form .wpforms-container label {
    color: var(--color-text-primary) !important;
}

.registration-form .wpforms-field-sublabel,
.registration-form .wpforms-field-description {
    color: var(--color-text-secondary) !important;
}

.registration-form .wpforms-field input,
.registration-form .wpforms-field textarea,
.registration-form .wpforms-field select {
    background: var(--color-bg-secondary) !important;
    color: var(--color-text-primary) !important;
    border: 1px solid var(--color-border) !important;
    border-radius: var(--radius-md) !important;
}

.registration-form .wpforms-field input:focus,
.registration-form .wpforms-field textarea:focus,
.registration-form .wpforms-field select:focus {
    border-color: var(--color-primary) !important;
    box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.1) !important;
}

.registration-form .wpforms-field input::placeholder,
.registration-form .wpforms-field textarea::placeholder {
    color: var(--color-text-muted) !important;
}

.registration-form .wpforms-submit-container .wpforms-submit {
    background: var(--color-primary) !important;
    color: #000000 !important;
    border: none !important;
    border-radius: var(--radius-md) !important;
    font-family: var(--font-display) !important;
    font-weight: var(--font-semibold) !important;
    text-transform: uppercase !important;
    letter-spacing: var(--tracking-wide) !important;
    cursor: pointer !important;
    transition: all var(--transition-normal) !important;
}

.registration-form .wpforms-submit-container .wpforms-submit:hover {
    background: var(--color-primary-light) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 168, 67, 0.3) !important;
}

.registration-form .wpforms-error {
    color: var(--color-error) !important;
    font-size: var(--text-sm);
}

.registration-form .wpforms-required-label {
    color: var(--color-error) !important;
}

/* WPForms page indicator / progress bar */
.registration-form .wpforms-page-indicator {
    color: var(--color-text-primary) !important;
}

.registration-form .wpforms-page-indicator .wpforms-page-indicator-page-title {
    color: var(--color-text-secondary) !important;
}

.registration-form .wpforms-page-indicator .wpforms-page-indicator-page.active .wpforms-page-indicator-page-title {
    color: var(--color-primary) !important;
}

/* Contact Form 7 specific */
.registration-form .wpcf7-form-control-wrap {
    display: block;
}

.registration-form .wpcf7-not-valid-tip {
    color: var(--color-error);
    font-size: var(--text-sm);
    margin-top: var(--space-1);
}

.registration-form .wpcf7-response-output {
    padding: var(--space-3);
    border-radius: var(--radius-md);
    margin-top: var(--space-4);
}

.registration-form .wpcf7-response-output.wpcf7-validation-errors {
    background: rgba(201, 40, 45, 0.1);
    border: 1px solid var(--color-error);
    color: var(--color-error);
}

.registration-form .wpcf7-response-output.wpcf7-mail-sent-ok {
    background: rgba(0, 255, 102, 0.1);
    border: 1px solid var(--color-success);
    color: var(--color-success);
}

/* Checkbox and radio styling */
.registration-form input[type="checkbox"],
.registration-form input[type="radio"] {
    width: auto;
    margin-right: var(--space-2);
    accent-color: var(--color-primary);
}

.registration-form .gfield_checkbox li,
.registration-form .gfield_radio li,
.registration-form .wpforms-field-checkbox li,
.registration-form .wpforms-field-radio li {
    list-style: none;
    margin-bottom: var(--space-2);
}

/* Success messages */
.registration-form .gform_confirmation_message,
.registration-form .wpforms-confirmation-container-full {
    padding: var(--space-4);
    background: rgba(0, 255, 102, 0.1);
    border: 1px solid var(--color-success);
    border-radius: var(--radius-lg);
    color: var(--color-success);
    text-align: center;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .registration-form {
        padding: var(--space-4);
    }

    .registration-form input[type="submit"],
    .registration-form button[type="submit"] {
        width: 100%;
    }
}
</style>
