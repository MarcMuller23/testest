<?php

namespace App\Http\traits;

use App\Http\traits\coordinateTrait;
use connectionNameSpace\databaseTrait;
use connectionNameSpace\databaseTrait as connection;

trait RoutingTrait
{
    public static function generateManualRoute($destinationIds, $groupId)
    {
        $conn = connection::getConnection();
        $destinations = [];
        foreach ($destinationIds as $destinationId) {
            if ($getAddressQuery = $conn->prepare('SELECT longitude,latitude,poiId FROM addresses WHERE addressId=?')) {
                $var1 = $destinationId->destinationId;
                $getAddressQuery->bind_param('s', $var1);
                $getAddressQuery->execute();
                $destination = $getAddressQuery->get_result();
                $rowCount = mysqli_num_rows($destination);
                if ($rowCount) {
                    if ($row = $destination->fetch_assoc()) {
                        $rowDataAddress = [];
                        $rowDataAddress["longitude"] = $row['longitude'];
                        $rowDataAddress["latitude"] = $row['latitude'];
                        $rowDataAddress["poiId"] = $row['poiId'];
                        if ($rowDataAddress["poiId"] != null) {
                            if ($getPoiQuery = $conn->prepare('SELECT longitude,latitude FROM pois WHERE poiId=?')) {
                                $var2 = $rowDataAddress["poiId"];
                                $getPoiQuery->bind_param('s', $var2);
                                $getPoiQuery->execute();
                                $destinationPoi = $getPoiQuery->get_result();
                                $rowCount = mysqli_num_rows($destinationPoi);
                                if ($rowCount) {
                                    if ($row = $destinationPoi->fetch_assoc()) {
                                        $rowDataPoi = [];
                                        $rowDataPoi["longitude"] = $row['longitude'];
                                        $rowDataPoi["latitude"] = $row['latitude'];
                                        $rowDataPoi["poiId"] = $row['poiId'];
                                        array_push($destinations, [$destinationPoi['latitude'], $destinationPoi['longitude']]);
                                    }
                                    $getPoiQuery->free_result();
                                    $getPoiQuery->close();
                                }
                            }
                        } else {
                            array_push($destinations, [$rowDataAddress['latitude'], $rowDataAddress['longitude']]);
                        }
                    }
                }
                $getAddressQuery->free_result();
                $getAddressQuery->close();
            }
        }


        $routingResponse = self::fetchRoutingServiceRoute($destinations);
        $routeDetails = [];
        $routeDetails['geometry'] = [];
        $routeDetails['distance'] = $routingResponse['result']['distance'];
        $routeDetails['duration'] = $routingResponse['result']['duration'];
        $routeDetails['stops'] = $destinations;
        $routeDetails['geometry'] = $routingResponse['result']['geometry'];

        $orderArray = [];

        foreach ($destinationIds as $destId) {
            $rowDataAddressParticipant = [];
            $rowDataAddressParticipant["destinationId"] = $destId->destinationId;
            if ($participantQuery = $conn->prepare('SELECT participants.participantId,participants.name From participants JOIN participant_address ON participants.participantId = participant_address.participantId WHERE participant_address.addressId=?')) {
                $participantQuery->bind_param('s', $destId->destinationId);
                $participantQuery->execute();
                $destination = $participantQuery->get_result();
                $rowCount = mysqli_num_rows($destination);
                if ($rowCount) {
                    if ($row = $destination->fetch_assoc()) {

                        $rowDataAddressParticipant["participantId"] = $row['participantId'];
                        $rowDataAddressParticipant["name"] = $row['name'];

                    }
                }
            }
            //Add indication for In or out, 0,1  // query the group_participant table
            if ($participantAddress = $conn->prepare('SELECT fromAddressId FROM participant_group WHERE participantId=? AND groupId=? AND fromAddressId=? ')) {
                $participantAddress->bind_param('sss', $row['participantId'], $groupId, $destId->destinationId);
                $participantAddress->execute();
                $destination = $participantAddress->get_result();
                $rowCount = mysqli_num_rows($destination);
                if ($rowCount != 0) {
                    $rowDataAddressParticipant["statusIndication"] = '0';
                    $participantAddress->close();
                } else if ($participantAddress = $conn->prepare('SELECT toAddressId FROM participant_group WHERE participantId=? AND groupId=? AND toAddressId=? ')) {
                    $participantAddress->bind_param('sss', $row['participantId'], $groupId, $destId->destinationId);
                    $participantAddress->execute();
                    $destination = $participantAddress->get_result();
                    $rowCount = mysqli_num_rows($destination);
                    if ($rowCount != 0) {
                        $rowDataAddressParticipant["statusIndication"] = '1';
                    }
                }
            }
            array_push($orderArray, $rowDataAddressParticipant);
        }
        $routeDetails['order'] = $orderArray;


        //checks for 1.5 hours //SENT WARNING
        if (self::checkForMaximumTime($routeDetails['order']) == false) {
            $routeDetails['warning'] = '01- 1.5 uur';
        }
        return $routeDetails;
    }

    public static function generateAutomatedRoute($groupList)
    {




    }

    public
    static function fetchRoutingServiceRoute($destinations)
    {

        //set service params
        $requestData = array(
            'destinations' => $destinations,
            'vehicleType' => "CAR",
            'detailed' => 1, //Removing geometry speeds up the request
            'APIkey' => 'IGOqTRa9ouA1Q1ItH$V5s0n3EkJZFnNZoB3sws9uCpSxaCZwp2YACpCvI081w7zjHAKN07xQp5J3CsHoBCJUW0m8fibw5I0jwl$2',
            "geojson" => 1,
        );

        $port = "443";
        $baseUrl = "https://osm.rysk-it.net";

        $url = $baseUrl . ':' . $port . '/api/routing/simpleRouting';

        //Start CURL client
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, 1);
        if ($requestData) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);

        if (!$result) {
            die("Connection Failure ORS");
        }
        curl_close($curl);
        return json_decode($result, true);
    }

    public
    static function optimizeRoutingServiceRoute($destinations)
    {

        //set service params
        $requestData = array(
            'destinations' => $destinations,
            'vehicleType' => "CAR",
            'preference' => "fastest",
            'detailed' => 1, //Removing geometry speeds up the request
            'APIkey' => 'IGOqTRa9ouA1Q1ItH$V5s0n3EkJZFnNZoB3sws9uCpSxaCZwp2YACpCvI081w7zjHAKN07xQp5J3CsHoBCJUW0m8fibw5I0jwl$2',
            "geojson" => 1,
        );

        $port = "443";
        $baseUrl = "https://osm.rysk-it.net";

        $url = $baseUrl . ':' . $port . '/api/routing/strictRouteOptimization';

        //Start CURL client
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, 1);
        if ($requestData) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);

        if (!$result) {
            die("Connection Failure ORS");
        }
        curl_close($curl);
        return json_decode($result, true);
    }

    public static
    function checkForMaximumTime($orderList): bool
    {
        for ($i = 0; $i < count($orderList) - 1; $i++) {
            $locationArray = [];
            array_push($locationArray, $orderList[$i]['destinationId'], $orderList[$i + 1]['destinationId']);
            $coordinates = self::getLongLat($locationArray);
            $result = self::fetchRoutingServiceRoute($coordinates);
            $orderList[$i]['duration'] = $result['result']['duration'] + 500;
        }
        $time = 0;
        foreach ($orderList as $d) {
            if ($d['statusIndication'] == 0) {
                $pid = $d['participantId'];

                for ($i = 0; $i < count($orderList); $i++) {
                    if ($orderList[$i]['participantId'] == $pid and $orderList[$i]['statusIndication'] == 0) {
                        $keyPlaceStart = $i;
                        for ($x = 0; $x < count($orderList); $x++) {
                            if ($orderList[$x]['participantId'] == $pid and $orderList[$x]['statusIndication'] == 1) {
                                $keyPlaceEnd = $x;
                                break;
                            }
                        }
                        break;
                    }
                }
                //calculate time
                $time = 0;
                for ($y = $keyPlaceStart; $y < $keyPlaceEnd; $y++) {
                    $time .= $orderList[$y]['duration'];
                }
            }
            if ($time > 5400) {
                return false;
            }
        }
        return true;
    }

    private static function getLongLat($destinationIdList)
    {
        $conn = connection::getConnection();
        $destinations = [];

        foreach ($destinationIdList as $id) {
            if ($getLongLatQuery = $conn->prepare('SELECT longitude,latitude,poiId FROM addresses WHERE addressId=?')) {
                $var1 = $id;
                $getLongLatQuery->bind_param('s', $var1);
                $getLongLatQuery->execute();
                $destination = $getLongLatQuery->get_result();
                $rowCount = mysqli_num_rows($destination);
                if ($rowCount) {
                    if ($row = $destination->fetch_assoc()) {
                        $rowDataAddress = [];
                        $rowDataAddress["lat"] = $row['latitude'];
                        $rowDataAddress["lng"] = $row['longitude'];

                    }
                }
            }
            array_push($destinations, $rowDataAddress);
            $getLongLatQuery->free_result();
            $getLongLatQuery->close();
        }
        return $destinations;
    }

    public static function calculateDriverDeparture($address, $groupId)
    {
        $returnData = [];
        $addressCoordinates = coordinateTrait::getCoordinates($address->postalCode, $address->houseNumber, $address->houseNumberAddition);
        array_push($returnData, [$addressCoordinates['lat'], $addressCoordinates['lng']]);
        $conn = databaseTrait::getConnection();
        if ($routeQuery = $conn->prepare('SELECT route FROM `groups` WHERE groupId=?')) {
            $var1 = $groupId;
            $routeQuery->bind_param('s', $var1);
            $routeQuery->execute();
            $route = $routeQuery->get_result();
            $rowCount = mysqli_num_rows($route);
            if ($rowCount) {
                if ($row = $route->fetch_assoc()) {

                    $rowDataAddress = $row['route'];
                }
            }

            $rowDataAddress = json_decode($rowDataAddress, 1);

            $destinationList = [];
            array_push($destinationList, $rowDataAddress['order'][0]['destinationId']);

            $destinations = self::getLongLat($destinationList);

        }
        array_push($returnData, [$destinations[0]['lat'], $destinations[0]['lng']]);


        $routingResponse = self::fetchRoutingServiceRoute($returnData);

        $response = $routingResponse['result']['duration'] / 60;
        return $response;
    }


}
