<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('format_bangla_time')) {
    function format_bangla_time($timeString)
    {
        if (app()->getLocale() !== 'bn') return $timeString;
        if (empty($timeString)) return $timeString;

        // Clean invisible/non-breaking spaces to standard spaces
        $timeString = preg_replace('/[\x{00A0}\x{200B}\x{202F}]/u', ' ', $timeString);

        // Specialized replacement for "24 Hours" and "Closed"
        $formatted = str_ireplace(['24 hours', 'closed'], ['২৪ ঘণ্টা', 'বন্ধ'], $timeString);

        // 1. Convert hours like 7 AM or 8:30 PM
        $formatted = preg_replace_callback('/(\d{1,2})(?::(\d{2}))?\s*(AM|PM|am|pm)/iu', function($matches) {
            $numContext = (int)$matches[1];
            $minutes = isset($matches[2]) && $matches[2] !== '' ? $matches[2] : null;
            $meridian = strtoupper($matches[3]);
            
            $isPM = $meridian === 'PM';
            $hour24 = $isPM ? ($numContext == 12 ? 12 : $numContext + 12) : ($numContext == 12 ? 0 : $numContext);
            
            $prefix = '';
            if ($hour24 >= 5 && $hour24 <= 11) $prefix = 'সকাল';
            elseif ($hour24 == 12 || $hour24 == 13 || $hour24 == 14) $prefix = 'দুপুর';
            elseif ($hour24 == 15 || $hour24 == 16 || $hour24 == 17) $prefix = 'বিকাল';
            elseif ($hour24 == 18 || $hour24 == 19) $prefix = 'সন্ধ্যা';
            else $prefix = 'রাত';
            
            $numBn = str_replace(
                ['1','2','3','4','5','6','7','8','9','0'],
                ['১','২','৩','৪','৫','৬','৭','৮','৯','০'],
                $matches[1]
            );
            
            if ($minutes) {
                $minBn = ':' . str_replace(
                    ['1','2','3','4','5','6','7','8','9','0'],
                    ['১','২','৩','৪','৫','৬','৭','৮','৯','০'],
                    $minutes
                );
                return $prefix . ' ' . $numBn . $minBn;
            } else {
                return $prefix . ' ' . $numBn . 'টা';
            }
        }, $formatted);

        // Replace hyphens with en-dashes, and ensure spaces
        $formatted = str_replace('-', ' – ', $formatted);
        // Collapse multiple spaces
        $formatted = preg_replace('/\s+/u', ' ', $formatted);
        
        // Final fallback: transform any remaining English digits to Bengali digits
        $formatted = str_replace(
            ['1','2','3','4','5','6','7','8','9','0'],
            ['১','২','৩','৪','৫','৬','৭','৮','৯','০'],
            $formatted
        );

        return trim($formatted);
    }
}

if (!function_exists('en2bn')) {
    function en2bn($number) {
        if (app()->getLocale() !== 'bn') return $number;
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        return str_replace($en, $bn, $number);
    }
}
