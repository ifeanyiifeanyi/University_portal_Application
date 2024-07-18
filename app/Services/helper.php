<?php
use Carbon\Carbon;

if (!function_exists('formatDateWithOrdinal')) {
    function formatDateWithOrdinal($date) {
        $date = Carbon::parse($date);
        $day = $date->day;
        $dayWithSuffix = $day . getDaySuffix($day);
        return $date->format('Y') . ' ' . $date->format('F') . ' ' . $dayWithSuffix;
    }

    function getDaySuffix($day) {
        if (!in_array(($day % 100), [11, 12, 13])) {
            switch ($day % 10) {
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
}
