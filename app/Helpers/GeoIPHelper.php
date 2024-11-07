<?php

namespace App\Helpers;

use Torann\GeoIP\Facades\GeoIP;

class GeoIPHelper
{
    public static function getLocation($ip)
    {
        try {
            $location = GeoIP::getLocation($ip);
            return $location['city'] . ', ' . $location['country'];
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
