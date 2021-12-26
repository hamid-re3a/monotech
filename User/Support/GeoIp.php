<?php

namespace User\Support;

use MaxMind\Db\Reader\InvalidDatabaseException;
use User\Models\Ip;

class GeoIp
{
    public static function getInfo($ip)
    {
        if(is_null($ip))
            return null;
        if (!$ip)
            $ip = request()->ip();

        try {
            $reader = new \GeoIp2\Database\Reader(__DIR__ . '/../storage/geoip.mmdb');
            $city = $reader->city($ip);
            $user_agent = new Ip();
            $user_agent->ip = request()->ip();
            $user_agent->iso_code = $city->country->isoCode;
            $user_agent->country = $city->country->name;
            $user_agent->city = $city->city->name;
            $user_agent->postal_code = $city->postal->code;
            $user_agent->lat = $city->location->latitude;
            $user_agent->lon = $city->location->longitude;
            $user_agent->timezone = $city->location->timeZone;
            $user_agent->continent = $city->continent->name;
            $user_agent->state_name = $city->mostSpecificSubdivision->name;
            $user_agent->state = $city->mostSpecificSubdivision->names['en'];
            return $user_agent;
        } catch (InvalidDatabaseException $e) {
            return null;
        }
    }


}
