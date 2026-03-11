<?php
/**
 * Helper Functions
 *
 * @package FLAW_Gaming_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Format large numbers (1000 → 1K, 1000000 → 1M)
 *
 * @param int $num Number to format
 * @return string Formatted number
 */
function flaw_format_number(int $num): string {
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    }
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'K';
    }
    return (string) $num;
}

/**
 * Get social media profile URL
 *
 * @param string $platform Platform name
 * @param string $handle User handle
 * @return string Full URL
 */
function flaw_get_social_url(string $platform, string $handle): string {
    $handle = ltrim($handle, '@');

    switch ($platform) {
        case 'twitter':
            return "https://twitter.com/{$handle}";
        case 'twitch':
            return "https://twitch.tv/{$handle}";
        case 'youtube':
            return str_starts_with($handle, '@')
                ? "https://youtube.com/{$handle}"
                : "https://youtube.com/channel/{$handle}";
        case 'instagram':
            return "https://instagram.com/{$handle}";
        case 'tiktok':
            return "https://tiktok.com/@{$handle}";
        case 'discord':
            return $handle; // Assume full URL
        default:
            return '#';
    }
}

/**
 * Get country flag URL
 *
 * @param string $country_code ISO country code
 * @return string Flag image URL
 */
function flaw_get_flag_url(string $country_code): string {
    $code = strtolower($country_code);
    return "https://flagcdn.com/24x18/{$code}.png";
}

/**
 * Get card grid CSS class based on item count
 *
 * @param int $count Number of items
 * @param string $type Grid type
 * @return string CSS classes
 */
function flaw_get_grid_class(int $count, string $type = 'default'): string {
    $base = 'grid';

    // Type-specific class
    if ($type !== 'default') {
        $base .= ' grid--' . $type;
    }

    // Count-based class
    if ($count <= 2) {
        return "{$base} grid--cols-2";
    }
    if ($count <= 3) {
        return "{$base} grid--cols-3";
    }
    if ($count <= 4) {
        return "{$base} grid--cols-4";
    }

    return $base;
}

/**
 * Check if Pods plugin is active
 *
 * @return bool
 */
function flaw_is_pods_active(): bool {
    return function_exists('pods');
}

/**
 * Get Pods instance safely
 *
 * @param string $pod_name Pod name
 * @param mixed $id Optional ID
 * @return Pods|null
 */
function flaw_get_pod(string $pod_name, $id = null) {
    if (!flaw_is_pods_active()) {
        return null;
    }

    return pods($pod_name, $id);
}

/**
 * Get Pods field value with fallback
 *
 * @param Pods $pod Pods instance
 * @param string $field Field name
 * @param mixed $default Default value
 * @return mixed
 */
function flaw_get_field($pod, string $field, $default = '') {
    if (!$pod || !method_exists($pod, 'field')) {
        return $default;
    }

    $value = $pod->field($field);
    return $value !== null && $value !== '' ? $value : $default;
}

/**
 * Render template part with data
 *
 * @param string $slug Template slug
 * @param string|null $name Template name
 * @param array $args Arguments to pass
 */
function flaw_template_part(string $slug, ?string $name = null, array $args = []): void {
    get_template_part($slug, $name, $args);
}

/**
 * Get image URL from Pods file field
 *
 * @param Pods $pod Pods instance
 * @param string $field Field name
 * @param string $size Image size
 * @return string|null
 */
function flaw_get_image_url($pod, string $field, string $size = 'full'): ?string {
    if (!$pod) {
        return null;
    }

    $image = $pod->field($field);

    if (empty($image)) {
        return null;
    }

    // If it's an array with ID
    if (is_array($image) && isset($image['ID'])) {
        return wp_get_attachment_image_url($image['ID'], $size);
    }

    // If it's just an ID
    if (is_numeric($image)) {
        return wp_get_attachment_image_url($image, $size);
    }

    // Try the _src variant
    $src = $pod->field($field . '._src');
    return $src ?: null;
}

/**
 * Get current event status based on dates
 *
 * @param string $start_date Start date
 * @param string|null $end_date End date
 * @param string $manual_status Manual status override
 * @return string
 */
function flaw_get_event_status(string $start_date, ?string $end_date = null, string $manual_status = ''): string {
    // Manual overrides for terminal states
    if (in_array($manual_status, ['completed', 'cancelled'], true)) {
        return $manual_status;
    }

    $now = current_time('timestamp');
    $start_ts = strtotime($start_date);
    $end_ts = $end_date ? strtotime($end_date) : ($start_ts + 86400);

    if ($now < $start_ts) {
        return 'upcoming';
    }

    if ($now >= $start_ts && $now <= $end_ts) {
        return 'live';
    }

    return 'completed';
}

/**
 * Format date range for display
 *
 * @param string $start Start date
 * @param string|null $end End date
 * @param string $format Date format
 * @return string
 */
function flaw_format_date_range(string $start, ?string $end = null, string $format = 'M j, Y'): string {
    $start_date = date($format, strtotime($start));

    if (!$end) {
        return $start_date;
    }

    $end_date = date($format, strtotime($end));

    // Same day
    if (date('Y-m-d', strtotime($start)) === date('Y-m-d', strtotime($end))) {
        return $start_date;
    }

    // Same month and year
    if (date('Y-m', strtotime($start)) === date('Y-m', strtotime($end))) {
        return date('M j', strtotime($start)) . ' - ' . date('j, Y', strtotime($end));
    }

    // Same year
    if (date('Y', strtotime($start)) === date('Y', strtotime($end))) {
        return date('M j', strtotime($start)) . ' - ' . $end_date;
    }

    return $start_date . ' - ' . $end_date;
}

/**
 * Get placement ordinal suffix
 *
 * @param int $num Number
 * @return string Ordinal suffix (st, nd, rd, th)
 */
function flaw_get_ordinal(int $num): string {
    if (!in_array(($num % 100), [11, 12, 13])) {
        switch ($num % 10) {
            case 1:
                return 'st';
            case 2:
                return 'nd';
            case 3:
                return 'rd';
        }
    }
    return 'th';
}

/**
 * Format placement for display
 *
 * @param int $placement Placement number
 * @return string Formatted placement (e.g., "1st", "2nd")
 */
function flaw_format_placement(int $placement): string {
    return $placement . flaw_get_ordinal($placement);
}

/**
 * Format currency
 *
 * @param float $amount Amount
 * @param string $currency Currency code
 * @return string
 */
function flaw_format_currency(float $amount, string $currency = 'USD'): string {
    if ($currency === 'USD') {
        return '$' . number_format($amount);
    }

    return number_format($amount) . ' ' . $currency;
}
