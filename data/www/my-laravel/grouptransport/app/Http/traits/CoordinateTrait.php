<?php

namespace App\Http\traits;

trait CoordinateTrait
{
    public static function getCoordinates($postalCode, $houseNumber, $houseNumberAddition)
    {

        $coordinateServiceResult = self::fetchCoordinates($postalCode, $houseNumber, $houseNumberAddition);
        $coordinates['lat'] = null;
        $coordinates['lng'] = null;
        if ($coordinateServiceResult != false) {
            $coordinates['lat'] = $coordinateServiceResult[0];
            $coordinates['lng'] = $coordinateServiceResult[1];
            return $coordinates;
        } else {
            return false;
        }
    }

    private static function fetchCoordinates($postalCode, $housenumber, $houseNumberAddittion)
    {
        $port = "433";
        $baseUrl = "https://location.rysk-it.net";
        $url = $baseUrl . '/addressGetCoordinates/' . str_replace(" ", "", $postalCode) . '/' . $housenumber;
        if ($houseNumberAddittion != null) {
            $url .= '/' . $houseNumberAddittion;
        }
        //Start CURL client
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //Execute request and cache
        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure Location service");
        }
        curl_close($curl);
        $result = json_decode($result, true);
        if ($result != null && $result['lat'] != null && $result['lng'] != null) {
            return [$result['lat'], $result['lng']];
        } else {
            return false;
        }
    }
}
