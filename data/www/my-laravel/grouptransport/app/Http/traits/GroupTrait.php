<?php

namespace App\Http\traits;

use App\Http\traits\coordinateTrait;
use App\Http\traits\idCreationTrait;
use App\Http\traits\routingTrait;
use Cake\Validation\Validator;
use connectionNameSpace\databaseTrait as connection;
use stdClass;

trait GroupTrait
{
    public static function getGroupObjectPost($jsonRequest)
    {
        $validator = new Validator();
        $validator
            ->notEmptyArray('companyId', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            $newGroupId = idCreationTrait::requestTokenId();
            //Create address variable
            $group = new stdClass();
            $group->groupId = $newGroupId;

            $group->periodStart = $jsonRequest->periodStart;
            $group->periodEnd = $jsonRequest->periodEnd;
            $group->companyId = $jsonRequest->companyId;
            $group->groupName = $jsonRequest->groupName;
            $group->route = $jsonRequest->route;
            $group->active = 1;
            return $group;
        }
    }

    public static function getGroupObjectPut($jsonRequest)
    {
        $validator = new Validator();
        $validator
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('groupId', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            $group = new stdClass();
            $group->groupId = $jsonRequest->groupId;
            $group->periodStart = $jsonRequest->periodStart;
            $group->periodEnd = $jsonRequest->periodEnd;
            $group->companyId = $jsonRequest->companyId;
            $group->groupName = $jsonRequest->groupName;
            $group->route = $jsonRequest->route;
            $group->active = 1;
            return $group;
        }
    }

    public static function getparticipant_GroupObjectPost($jsonRequest)
    {
        $validator = new Validator();
        $validator
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('participantId', 'This field is required')
            ->notEmptyArray('fromAddressId', 'This field is required')
            ->notEmptyArray('toAddressId', 'This field is required')
            ->notEmptyArray('groupId', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            $participant = new stdClass();
            $participant->groupId = $jsonRequest->groupId;
            $participant->participantId = $jsonRequest->participantId;
            $participant->fromAddressId = $jsonRequest->fromAddressId;
            $participant->toAddressId = $jsonRequest->toAddressId;
            $participant->companyId = $jsonRequest->companyId;
            $participant->active = $jsonRequest->active;
            return $participant;
        }
    }

    public static function getGroupDetails($groupId)
    {
        $conn = connection::getConnection();
        if ($getGroupQuery = $conn->prepare('SELECT * FROM `groups` WHERE groupId=?')) {
            $var1 = $groupId;
            $getGroupQuery->bind_param('s', $var1);
            $getGroupQuery->execute();
            $result = $getGroupQuery->get_result();
            $rowCount = mysqli_num_rows($result);
            $returnData = [];
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $result->fetch_assoc()) {
                        $rowData = [];
                        $rowData["groupId"] = $row['groupId'];
                        $rowData["companyId"] = $row['companyId'];
                        $rowData["groupName"] = $row['groupName'];
                        $rowData["route"] = $row['route'];
                        $rowData["periodStart"] = $row['periodStart'];
                        $rowData["periodEnd"] = $row['periodEnd'];
                        $rowData["active"] = $row['active'];
                        array_push($returnData, $rowData);
                    }
                }
            }
            $result->free_result();
            $getGroupQuery->close();
            if ($returnData != null) {
                $groupParticipants = [];
                $groupParticipants = self::getGroupParticipants($groupId);
                //TODO: Get all addresses from known ID
                array_push($returnData, $groupParticipants);
                return $returnData;
            }
        }
        return false;
    }

    public static function getAllGroupDetails($companyId)
    {
        $conn = connection::getConnection();
        if ($getGroupQuery = $conn->prepare('SELECT * FROM `groups` WHERE companyId=?')) {
            $var1 = $companyId;
            $getGroupQuery->bind_param('s', $var1);
            $getGroupQuery->execute();
            $result = $getGroupQuery->get_result();
            $rowCount = mysqli_num_rows($result);
            $returnData = [];
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $result->fetch_assoc()) {
                        $rowData = [];
                        $rowData["groupId"] = $row['groupId'];
                        $rowData["companyId"] = $row['companyId'];
                        $rowData["groupName"] = $row['groupName'];
                        $rowData["route"] = $row['route'];
                        $rowData["periodStart"] = $row['periodStart'];
                        $rowData["periodEnd"] = $row['periodEnd'];
                        $rowData["active"] = $row['active'];
                        if ($returnData != null) {
                            $groupParticipants = [];
                            $groupParticipants = self::getGroupParticipants($row['groupId']);
                            foreach ($groupParticipants as $participant) {
                                $rowData1 = [];
                                $rowData1['name'] = $participant['name'];
                                $rowData1['toAddressName'] = $participant['toAddressName'];
                                $rowData1['fromAddressName'] = $participant['fromAddressName'];
                                array_push($rowData, $rowData1);
                            }
                        }
                        array_push($returnData, $rowData);
                    }
                }
                $result->free_result();
                $getGroupQuery->close();
                return $returnData;
            }
        }
        return false;
    }

    private static function getGroupParticipants($groupId)
    {
        $conn = connection::getConnection();
        if ($getGroupParticipantsQuery = $conn->prepare('SELECT * FROM participant_group WHERE groupId=?')) {
            $var1 = $groupId;
            $getGroupParticipantsQuery->bind_param('s', $var1);
            $getGroupParticipantsQuery->execute();
            $result = $getGroupParticipantsQuery->get_result();
            $rowCount = mysqli_num_rows($result);
            $returnData = [];
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $result->fetch_assoc()) {
                        $rowData = [];
                        $rowData["groupId"] = $row['groupId'];
                        $rowData["participantId"] = $row['participantId'];
                        if ($getParticipantsQuery = $conn->prepare('SELECT name FROM participants WHERE participantId=?')) {

                            $var1 = $row['participantId'];
                            $getParticipantsQuery->bind_param('s', $var1);
                            $getParticipantsQuery->execute();
                            $result1 = $getParticipantsQuery->get_result();
                            $rowCount1 = mysqli_num_rows($result1);
                            if ($rowCount1) {
                                if ($row2 = $result1->fetch_assoc()) {
                                    $rowData["name"] = $row2['name'];
                                }
                            }
                            $result1->free_result();
                            $getParticipantsQuery->close();
                        }
                        $toAddressArray = [];
                        $fromAddressArray = [];
                        $toAddressArray = self::getParticipantAddress($row['toAddressId']);
                        $fromAddressArray = self::getParticipantAddress($row['fromAddressId']);
                        $rowData["toAddressName"] = $toAddressArray['addressName'];
                        $rowData["toStreet"] = $toAddressArray['street'];
                        $rowData["toHouseNumber"] = $toAddressArray['houseNumber'];
                        $rowData["toHouseNumberAddition"] = $toAddressArray['houseNumberAddition'];
                        $rowData["toCity"] = $toAddressArray['city'];
                        $rowData["toPostalCode"] = $toAddressArray['postalCode'];
                        $rowData["toCountry"] = $toAddressArray['country'];
                        $rowData["toLatitude"] = $toAddressArray['latitude'];
                        $rowData["toLongitude"] = $toAddressArray['longitude'];
                        $rowData["toPhoneNumber"] = $toAddressArray['phoneNumber'];
                        $rowData["toContactPerson"] = $toAddressArray['contactPerson'];
                        $rowData["toCompanyId"] = $toAddressArray['companyId'];
                        $rowData["toPoiId"] = $toAddressArray['poiId'];
                        $rowData["fromAddressName"] = $fromAddressArray['addressName'];
                        $rowData["fromStreet"] = $fromAddressArray['street'];
                        $rowData["fromHouseNumber"] = $fromAddressArray['houseNumber'];
                        $rowData["fromHouseNumberAddition"] = $fromAddressArray['houseNumberAddition'];
                        $rowData["fromCity"] = $fromAddressArray['city'];
                        $rowData["fromPostalCode"] = $fromAddressArray['postalCode'];
                        $rowData["fromCountry"] = $fromAddressArray['country'];
                        $rowData["fromLatitude"] = $fromAddressArray['latitude'];
                        $rowData["fromLongitude"] = $fromAddressArray['longitude'];
                        $rowData["fromPhoneNumber"] = $fromAddressArray['phoneNumber'];
                        $rowData["fromContactPerson"] = $fromAddressArray['contactPerson'];
                        $rowData["fromCompanyId"] = $fromAddressArray['companyId'];
                        $rowData["fromPoiId"] = $fromAddressArray['poiId'];
                        $rowData["active"] = $fromAddressArray['active'];
                        array_push($returnData, $rowData);
                    }
                }
            }
            $getGroupParticipantsQuery->close();
            return $returnData;
        }
        return false;
    }

    private static function getParticipantAddress($addressId)
    {
        $conn = connection::getConnection();
        if ($getAddressQuery = $conn->prepare('SELECT * FROM addresses WHERE addressId =?')) {
            $var1 = $addressId;
            $getAddressQuery->bind_param('s', $var1);
            $getAddressQuery->execute();
            $resultAddress = $getAddressQuery->get_result();
            $rowCountAddress = mysqli_num_rows($resultAddress);
            $returnData = [];
            if ($rowCountAddress) {
                if ($rowAddress = $resultAddress->fetch_assoc()) {
                    $rowData = [];
                    $rowData["addressId"] = $rowAddress['addressId'];
                    $rowData["addressName"] = $rowAddress['addressName'];
                    $rowData["street"] = $rowAddress['street'];
                    $rowData["houseNumber"] = $rowAddress['houseNumber'];
                    $rowData["houseNumberAddition"] = $rowAddress['houseNumberAddition'];
                    $rowData["city"] = $rowAddress['city'];
                    $rowData["postalCode"] = $rowAddress['postalCode'];
                    $rowData["country"] = $rowAddress['country'];
                    $rowData["latitude"] = $rowAddress['latitude'];
                    $rowData["longitude"] = $rowAddress['longitude'];
                    $rowData["phoneNumber"] = $rowAddress['phoneNumber'];
                    $rowData["contactPerson"] = $rowAddress['contactPerson'];
                    $rowData["companyId"] = $rowAddress['companyId'];
                    $rowData["poiId"] = $rowAddress['poiId'];
                    $rowData["active"] = $rowAddress['active'];

                    $resultAddress->free_result();
                    $getAddressQuery->close();

                    if ($rowData['poiId'] != null) {
                        if ($getPoiInfo = $conn->prepare('SELECT openTime, closeTime FROM pois WHERE poiId=? ')) {
                            $var1 = $rowData['poiId'];
                            $getPoiInfo->bind_param('s', $var1);
                            $getPoiInfo->execute();
                            $resultPoi = $getPoiInfo->get_result();
                            $rowCountPoi = mysqli_num_rows($resultPoi);
                            if ($rowCountPoi) {
                                if ($rowPoi = $resultPoi->fetch_assoc()) {
                                    $rowData["openTime"] = $rowPoi['openTime'];
                                    $rowData["closeTime"] = $rowPoi['closeTime'];
                                }
                            }
                            $resultPoi->free_result();
                            $getPoiInfo->close();
                            return $rowData;
                        }
                    }

                    return $rowData;
                }
                return false;
            }
        }
    }

//    public static function divide($list)
//    {
//        //voor elke participant data compleet maken
//        $participantInfoList = [];
//        foreach ($list->participants as $participant) {
//
//            $participantInfo = [];
//            $addressFromInfo = self::getParticipantAddress($participant->fromAddressId);
//            $addressToInfo = self::getParticipantAddress($participant->toAddressId);
//
//            $participantInfo['participantId'] = $participant->participantId;
//            $participantInfo['fromAddress'] = $addressFromInfo;
//            $participantInfo['toAddress'] = $addressToInfo;
//            array_push($participantInfoList, $participantInfo);
//        }
//
//        //groepjes maken op basis van tijd
//        $groups = [];
//        $flexibleGroup = [];
//
//        $suitableList = [];
//        $notSuitableList = [];
//
//        foreach ($participantInfoList as $participant) {
//            if ($participant['toAddress']['openTime'] == "08:30:00") {
////
//                array_push($suitableList, $participant);
////
////                if (count($flexibleGroup) == 5) {
////
////
////                    array_push( $flexibleGroup,$participant);
////                    array_push($groups, $flexibleGroup);
////                    $flexibleGroup = [];
////                } else {
////                    array_push($flexibleGroup, $participant);
////                }
//            } else {
//                array_push($notSuitableList, $participant);
//            }
//        }
//        return $groups;
//    }

    public static function downloadContent($url, $fileName)
    {


// Initialize the cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        file_put_contents('/home/ryskit/actions-runner/groepsvervoer/be-GroepsVervoer/be-GroepsVervoer/public/' . $fileName, $result);


        return '/home/ryskit/actions-runner/groepsvervoer/be-GroepsVervoer/be-GroepsVervoer/public/' . $fileName;

    }

    public static function convertFileSchoolList($content, $type)
    {

        switch ($type) {
            case 'CSV':
                $csv = array_map('str_getcsv', file($content));
                $returnArray['pois'] = [];
                foreach ($csv as $field) {
                    $list = [];
                    $list['school'] = $field[0];
                    $list['street'] = $field[1];
                    $list['postal'] = $field[2];
                    $list['city'] = $field[3];
                    $list['startTime'] = $field[4];
                    $list['endTime'] = $field[5];
                    $list['startTimeVso'] = $field[6];
                    $list['endTimeVso'] = $field[7];
                    $list['remark'] = $field[8];
                    array_push($returnArray['pois'], $list);
                }
                return $returnArray;

            case 'XML':
                //
                $objXmlDocument = simplexml_load_file($content);
                if ($objXmlDocument === FALSE) {
                    echo "There were errors parsing the XML file.\n";
                    exit;
                }
                $objJsonDocument = json_encode($objXmlDocument);
                foreach ($objJsonDocument as $field) {
                    $list = [];
                    $list['school'] = $field->school;
                    $list['street'] = $field->street;
                    $list['postal'] = $field->postal;
                    $list['city'] = $field->city;
                    $list['startTime'] = $field->startTime;
                    $list['endTime'] = $field->endTime;
                    $list['startTimeVso'] = $field->startTimeVso;
                    $list['endTimeVso'] = $field->endTimeVso;
                    $list['remark'] = $field->remark;
                    array_push($returnArray, $list);
                }
                return $returnArray;

            case 'JSON':
                $jsonFile = json_decode($content);
                foreach ($jsonFile as $field) {
                    $list = [];
                    $list['school'] = $field->school;
                    $list['street'] = $field->street;
                    $list['postal'] = $field->postal;
                    $list['city'] = $field->city;
                    $list['startTime'] = $field->startTime;
                    $list['endTime'] = $field->endTime;
                    $list['startTimeVso'] = $field->startTimeVso;
                    $list['endTimeVso'] = $field->endTimeVso;
                    $list['remark'] = $field->remark;
                    array_push($returnArray, $list);
                }
                return $returnArray;
        }
        return false;
    }

    public static function convertFileParticipantList($content, $type)
    {
        switch ($type) {
            case 'CSV':
                $csv = array_map('str_getcsv', file($content));
                $returnArray = [];
                foreach ($csv as $row) {
                    $list = [];
                    $list['participantNumber'] = $row[0];
                    $list['familyName'] = $row[1];
                    $list['initial'] = $row[2];
                    $list['insertion'] = $row[3];
                    $list['firstName'] = $row[4];
                    $list['birthDate'] = $row[5];
                    $list['gender'] = $row[6];
                    $list['telephone1'] = $row[7];
                    $list['telephone2'] = $row[8];
                    $list['email'] = $row[9];
                    $list['streetGet'] = $row[10];
                    $list['houseNumberGet'] = $row[11];
                    $list['houseNumberAdditionGet'] = $row[12];
                    $list['postalCodeGet'] = $row[13];
                    $list['cityGet'] = $row[14];
                    $list['schoolName'] = $row[15];
                    $list['streetDeliver'] = $row[16];
                    $list['houseNumberDeliver'] = $row[17];
                    $list['houseNumberAdditionDeliver'] = $row[18];
                    $list['postalCodeDeliver'] = $row[19];
                    $list['cityDeliver'] = $row[20];
                    $list['monTimeGet'] = $row[21];
                    $list['tueTimeGet'] = $row[22];
                    $list['wedTimeGet'] = $row[23];
                    $list['thuTimeGet'] = $row[24];
                    $list['friTimeGet'] = $row[25];
                    $list['satTimeGet'] = $row[26];
                    $list['sunTimeGet'] = $row[27];
                    $list['monTimeDeliver'] = $row[28];
                    $list['tueTimeDeliver'] = $row[29];
                    $list['wedTimeDeliver'] = $row[30];
                    $list['thuTimeDeliver'] = $row[31];
                    $list['friTimeDeliver'] = $row[32];
                    $list['satTimeDeliver'] = $row[33];
                    $list['sunTimeDeliver'] = $row[34];
                    $list['plot'] = $row[35];
                    $list['escort'] = $row[36];
                    $list['wheelChair'] = $row[37];
                    $list['individual'] = $row[38];
                    $list['inFront'] = $row[39];
                    $list['essentials'] = $row[40];
                    $list['maxTravelTime'] = $row[41];
                    $list['marge'] = $row[42];
                    $list['startDate'] = $row[43];
                    $list['endDate'] = $row[44];
                    array_push($returnArray, $list);
                }
                return $returnArray;

            case 'XML':
                //
                $objXmlDocument = simplexml_load_file($content);
                if ($objXmlDocument === FALSE) {
                    echo "There were errors parsing the XML file.\n";
                    exit;
                }
                $objJsonDocument = json_encode($objXmlDocument);
                foreach ($objJsonDocument as $field) {
                    $list = [];
                    $list['participantId'] = $field[0];
                    $list['name'] = $field[1];
                    $list['phoneNumber'] = $field[2];
                    $list['companyId'] = $field[3];
                    $list['active'] = $field[4];
                    array_push($returnArray, $list);
                }
                return $returnArray;

            case 'JSON':
                $jsonFile = json_decode($content);
                foreach ($jsonFile as $field) {
                    $list = [];
                    $list['participantId'] = $field[0];
                    $list['name'] = $field[1];
                    $list['phoneNumber'] = $field[2];
                    $list['companyId'] = $field[3];
                    $list['active'] = $field[4];
                    array_push($returnArray, $list);
                }
                return $returnArray;
        }
        return false;
    }

    public static function createPois($participantList, $label, $companyId): bool
    {
        $conn = connection::getConnection();

        foreach ($participantList as $participant) {

            $coordinates = coordinateTrait::getCoordinates($participant['postalCodeDeliver'], $participant['houseNumberDeliver'], $participant['houseNumberAdditionDeliver'] ?? null);
            if ($coordinates == false) {
                $coordinates = coordinateTrait::getCoordinates($participant['postalCodeDeliver'], $participant['houseNumberDeliver'], null);
                if ($coordinates == false) {
                    return false;
                }
            }
            if ($checkPoiQuery = $conn->prepare('SELECT * FROM pois WHERE postalCode=? AND houseNumber=? AND labelId=?')) {
                $var1 = $participant['postalCodeDeliver'];
                $var2 = $participant['houseNumberDeliver'];
                $var3 = $label;
                $checkPoiQuery->bind_param('sss', $var1, $var2, $var3);
                $checkPoiQuery->execute();
                $result = $checkPoiQuery->get_result();
                $rowCount = mysqli_num_rows($result);
                $checkPoiQuery->free_result();
                $checkPoiQuery->close();
                //Inserting when the poi does not exist
                if ($rowCount == 0) {
                    if ($poiQuery = $conn->prepare('INSERT INTO pois (poiId,companyId,poiName,street,houseNumber,houseNumberAddition,city,postalCode,country,latitude,longitude,active,created_at,labelId)
              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?); ')
                    ) {
                        $var1 = idCreationTrait::requestTokenId();
                        $var2 = $companyId;
                        $var3 = $participant['schoolName'];
                        $var4 = $participant['streetDeliver'];
                        $var5 = $participant['houseNumberDeliver'];
                        $var6 = $participant['houseNumberAdditionDeliver'];
                        $var7 = $participant['cityDeliver'];
                        $var8 = $participant['postalCodeDeliver'];
                        $var9 = "NL";
                        $var10 = $coordinates['lat'];
                        $var11 = $coordinates['lng'];
                        $var12 = 1;
                        $var13 = date("Y-m-d h:i:s", time());
                        $var14 = $label;
                        $poiQuery->bind_param('sssssssssssiss', $var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10, $var11, $var12, $var13, $var14);
                        $poiQuery->execute();
                        $poiQuery->close();

                    }

                }

            }

        }
        //return iets
        return true;
    }

    private static function checkIfParticipantExists($conn, $label, $participant, $companyId)
    {
        if ($getParticipantQuery = $conn->prepare('SELECT * FROM participants WHERE labelId=? AND participantNumber=? AND companyId=?')) {
            $getParticipantQuery->bind_param('sss', $label, $participant['participantNumber'], $companyId);
            $getParticipantQuery->execute();
            $result = $getParticipantQuery->get_result();
            $rowCount = mysqli_num_rows($result);
            //If the rowcount true(filled) the participant exists
            if ($rowCount == 1) {
                if ($row = $result->fetch_assoc()) {
                    $participantResult = [];
                    $participantResult['participantId'] = $row['participantId'];
                    $participantResult['mon_arrival_time'] = $row['mon_arrival_time'];
                    $participantResult['tue_arrival_time'] = $row['tue_arrival_time'];
                    $participantResult['wed_arrival_time'] = $row['wed_arrival_time'];
                    $participantResult['thu_arrival_time'] = $row['thu_arrival_time'];
                    $participantResult['fri_arrival_time'] = $row['fri_arrival_time'];
                    $participantResult['sat_arrival_time'] = $row['sat_arrival_time'];
                    $participantResult['sun_arrival_time'] = $row['sun_arrival_time'];
                    $participantResult['mon_departure_time'] = $row['mon_departure_time'];
                    $participantResult['tue_departure_time'] = $row['tue_departure_time'];
                    $participantResult['wed_departure_time'] = $row['wed_departure_time'];
                    $participantResult['thu_departure_time'] = $row['thu_departure_time'];
                    $participantResult['fri_departure_time'] = $row['fri_departure_time'];
                    $participantResult['sat_departure_time'] = $row['sat_departure_time'];
                    $participantResult['sun_departure_time'] = $row['sun_departure_time'];
                    $getParticipantQuery->free_result();
                    $getParticipantQuery->close();
                    return $participantResult;
                }
                return 500;
            }
            return false;
        }
        return 500;
    }

    private static function updateParticipant($conn, $participant, $searchedParticipant)
    {
        if ($updateParticipantQuery = $conn->prepare('UPDATE participants SET mon_arrival_time=?,tue_arrival_time=?,wed_arrival_time=?,thu_arrival_time=?,fri_arrival_time=?,sat_arrival_time=?,sun_arrival_time=?,mon_departure_time=?,tue_departure_time=?,wed_departure_time=?,thu_departure_time=?,fri_departure_time=?,sat_departure_time=?,sun_departure_time=? WHERE participantId=?')) {
            if ($searchedParticipant['mon_arrival_time'] == null) {
                $mondayArrival = $participant['monTimeGet'];
            } else {
                $mondayArrival = $searchedParticipant['mon_arrival_time'];
            }
            if ($searchedParticipant['tue_arrival_time'] == null) {
                $tuesdayArrival = $participant['tueTimeGet'];
            } else {
                $tuesdayArrival = $searchedParticipant['tue_arrival_time'];
            }
            if ($searchedParticipant['wed_arrival_time'] == null) {
                $wednesdayArrival = $participant['wedTimeGet'];
            } else {
                $wednesdayArrival = $searchedParticipant['wed_arrival_time'];
            }
            if ($searchedParticipant['thu_arrival_time'] == null) {
                $thursdayArrival = $participant['thuTimeGet'];
            } else {
                $thursdayArrival = $searchedParticipant['thu_arrival_time'];
            }
            if ($searchedParticipant['fri_arrival_time'] == null) {
                $fridayArrival = $participant['friTimeGet'];
            } else {
                $fridayArrival = $searchedParticipant['fri_arrival_time'];
            }
            if ($searchedParticipant['sat_arrival_time'] == null) {
                $saturdayArrival = $participant['satTimeGet'];
            } else {
                $saturdayArrival = $searchedParticipant['sat_arrival_time'];
            }
            if ($searchedParticipant['sun_arrival_time'] == null) {
                $sundayArrival = $participant['sunTimeGet'];
            } else {
                $sundayArrival = $searchedParticipant['sun_arrival_time'];
            }
            if ($searchedParticipant['mon_departure_time'] == null) {
                $mondayDeparture = $participant['monTimeDeliver'];
            } else {
                $mondayDeparture = $searchedParticipant['mon_departure_time'];
            }
            if ($searchedParticipant['tue_departure_time'] == null) {
                $tuesdayDeparture = $participant['tueTimeDeliver'];
            } else {
                $tuesdayDeparture = $searchedParticipant['tue_departure_time'];
            }
            if ($searchedParticipant['wed_departure_time'] == null) {
                $wednesdayDeparture = $participant['wedTimeDeliver'];
            } else {
                $wednesdayDeparture = $searchedParticipant['wed_departure_time'];
            }
            if ($searchedParticipant['thu_departure_time'] == null) {
                $thursdayDeparture = $participant['thuTimeDeliver'];
            } else {
                $thursdayDeparture = $searchedParticipant['thu_departure_time'];
            }
            if ($searchedParticipant['fri_departure_time'] == null) {
                $fridayDeparture = $participant['friTimeDeliver'];
            } else {
                $fridayDeparture = $searchedParticipant['fri_departure_time'];
            }
            if ($searchedParticipant['sat_departure_time'] == null) {
                $saturdayDeparture = $participant['satTimeDeliver'];
            } else {
                $saturdayDeparture = $searchedParticipant['sat_departure_time'];
            }
            if ($searchedParticipant['sun_departure_time'] == null) {
                $sundayDeparture = $participant['sunTimeDeliver'];
            } else {
                $sundayDeparture = $searchedParticipant['sat_departure_time'];
            }
            $participantId = $searchedParticipant['participantId'];
            $updateParticipantQuery->bind_param('sssssssssssssss', $mondayArrival, $tuesdayArrival, $wednesdayArrival, $thursdayArrival, $fridayArrival, $saturdayArrival, $sundayArrival, $mondayDeparture, $tuesdayDeparture, $wednesdayDeparture, $thursdayDeparture, $fridayDeparture, $saturdayDeparture, $sundayDeparture, $participantId);
            $updateParticipantQuery->execute();
            $updateParticipantQuery->close();
            return true;
        }
        return false;
    }

    private static function checkIfAddressExists($conn, $label, $participant, $status)
    {
        switch ($status) {
            case 0:
            {
                if ($getAddressQuery = $conn->prepare('SELECT * from addresses WHERE labelId=? AND postalCode=? AND houseNumber=? AND houseNumberAddition=? AND status=?')) {

                    $postalCode = $participant['postalCodeGet'];
                    $houseNumber = $participant['houseNumberGet'];
                    $houseNumberAddition = $participant['houseNumberAdditionGet'];
                    $getAddressQuery->bind_param('ssssi', $label, $postalCode, $houseNumber, $houseNumberAddition, $status);
                    $getAddressQuery->execute();
                    $result = $getAddressQuery->get_result();
                    $rowCountAddress = mysqli_num_rows($result);
                    if ($row = $result->fetch_assoc()) {
                        //Save addressId if it exists

                        $addressId = $row['addressId'];//aanpassen
                    }
                    $getAddressQuery->free_result();
                    $getAddressQuery->close();
                    if ($rowCountAddress == 0) {
                        return false;
                    } else {
                        return $addressId;
                    }
                } else {
                    print_r($conn->error);
                    return 500;
                }
            }
            case 1:
            {

                if ($getAddressQuery = $conn->prepare('SELECT * from addresses WHERE labelId=? AND postalCode=? AND houseNumber=? AND houseNumberAddition=? AND status=?')) {

                    $postalCode = $participant['postalCodeDeliver'];
                    $houseNumber = $participant['houseNumberDeliver'];
                    $houseNumberAddition = $participant['houseNumberAdditionDeliver'];
                    $getAddressQuery->bind_param('ssssi', $label, $postalCode, $houseNumber, $houseNumberAddition, $status);
                    $getAddressQuery->execute();
                    $result = $getAddressQuery->get_result();
                    $rowCountAddress = mysqli_num_rows($result);
                    if ($row = $result->fetch_assoc()) {
                        //Save addressId if it exists
                        $addressId = $row['addressId'];//aanpassen
                    }
                    $getAddressQuery->free_result();
                    $getAddressQuery->close();
                    if ($rowCountAddress == 0) {
                        return false;
                    } else {
                        return $addressId;
                    }
                } else {
                    print_r($conn->error);
                    return 500;
                }

            }
        }
        return 500;
    }


    private static function createAddress($conn, $participant, $label, $companyId, $status, $participantId)
    {

        switch ($status) {
            case 0:
            {

                $coordinates = coordinateTrait::getCoordinates($participant['postalCodeGet'], $participant['houseNumberGet'], $participant['houseNumberAdditionGet'] ?? null);
                if ($coordinates == false) {
                    $coordinates = coordinateTrait::getCoordinates($participant['postalCodeGet'], $participant['houseNumberGet'], null);
                    if ($coordinates == false) {
                        print_r('hier1');
                        return false;
                    }
                }
                //Check if a poi exists of this address
                if ($checkPoiQuery = $conn->prepare('SELECT * FROM pois WHERE postalCode=? AND houseNumber=? AND labelId=?')) {
                    $var1 = $participant['postalCodeGet'];
                    $var2 = $participant['houseNumberGet'];
                    $var3 = $label;
                    $checkPoiQuery->bind_param('sss', $var1, $var2, $var3);
                    $checkPoiQuery->execute();
                    $result = $checkPoiQuery->get_result();
                    $rowCountPoi = mysqli_num_rows($result);

                    //If it does, save the ID
                    if ($rowCountPoi) {
                        if ($row = $result->fetch_assoc()) {
                            $poiId = $row['poiId'];
                        }
                    } else {
                        $poiId = null;
                    }
                }
                $checkPoiQuery->free_result();
                $checkPoiQuery->close();

                //Insertion in database
                if ($createAddressQuery = $conn->prepare('INSERT INTO addresses (addressId,addressName,street,houseNumber,houseNumberAddition,city,postalCode,latitude,longitude,companyId,poiId,active,created_at,status,labelId) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ')) {
                    $addressId = idCreationTrait::requestTokenId();
                    //Save the ID of the first address

                    $addressName = "Thuis adres";
                    $street = $participant['streetGet'];
                    $houseNumber = $participant['houseNumberGet'];
                    $houseNumberAddition = $participant['houseNumberAdditionGet'];
                    $city = $participant['cityGet'];
                    $postalCode = $participant['postalCodeGet'];
                    $lat = $coordinates['lat'];
                    $lng = $coordinates['lng'];
                    $active = 1;
                    $created = date("Y-m-d h:i:s", time());
                    $createAddressQuery->bind_param('sssssssssssisis', $addressId, $addressName, $street, $houseNumber, $houseNumberAddition, $city, $postalCode, $lat, $lng, $companyId, $poiId, $active, $created, $status, $label);
                    $createAddressQuery->execute();
                    print_r($conn->error);
                } else {
                    print_r($conn->error);
                    print_r('hier2');
                    return false;
                }
                //Insertion in connection table
                if ($participantAddressQuery = $conn->prepare('INSERT INTO participant_address ( participantId,addressId,active, created_at,marge,maxTravelTime,startDate,endDate,monday,tuesday,wednesday,thursday,friday,saturday,sunday) VALUES(?,?,1,?,?,?,?,?,?,?,?,?,?,?,?)')) {
                    $created = date("Y-m-d h:i:s", time());
                    $marge = $participant['marge'];
                    $maxTravelTime = $participant['maxTravelTime'];
                    $startDate = date("Y-m-d", strtotime($participant['startDate']));
                    $endDate = date("Y-m-d", strtotime($participant['endDate']));
                    if ($participant['monTimeGet'] != null) {
                        $monday = 1;
                    } else {
                        $monday = 0;
                    }
                    if ($participant['tueTimeGet'] != null) {
                        $tuesday = 1;
                    } else {
                        $tuesday = 0;
                    }
                    if ($participant['wedTimeGet'] != null) {
                        $wednesday = 1;
                    } else {
                        $wednesday = 0;
                    }
                    if ($participant['thuTimeGet'] != null) {
                        $thursday = 1;
                    } else {
                        $thursday = 0;
                    }
                    if ($participant['friTimeGet'] != null) {
                        $friday = 1;
                    } else {
                        $friday = 0;
                    }
                    if ($participant['satTimeGet'] != null) {
                        $saturday = 1;
                    } else {
                        $saturday = 0;
                    }
                    if ($participant['sunTimeGet'] != null) {
                        $sunday = 1;
                    } else {
                        $sunday = 0;
                    }
                    $participantAddressQuery->bind_param('sssiisssssssss', $participantId, $addressId, $created, $marge, $maxTravelTime, $startDate, $endDate, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);
                    $participantAddressQuery->execute();
                    $participantAddressQuery->close();
                    return true;
                } else {
                    print_r($conn->error);
                    print_r('hier3');
                    return false;
                }
            }

            case 1:
            {

                $coordinates = coordinateTrait::getCoordinates($participant['postalCodeDeliver'], $participant['houseNumberDeliver'], $participant['houseNumberAdditionDeliver'] ?? null);
                if ($coordinates == false) {
                    $coordinates = coordinateTrait::getCoordinates($participant['postalCodeDeliver'], $participant['houseNumberDeliver'], null);
                    if ($coordinates == false) {
                        return false;
                    }
                }
                //Check if a poi exists of this address
                if ($checkPoiQuery = $conn->prepare('SELECT * FROM pois WHERE postalCode=? AND houseNumber=? AND labelId=?')) {
                    $var1 = $participant['postalCodeDeliver'];
                    $var2 = $participant['houseNumberDeliver'];
                    $var3 = $label;
                    $checkPoiQuery->bind_param('sss', $var1, $var2, $var3);
                    $checkPoiQuery->execute();
                    $result = $checkPoiQuery->get_result();
                    $rowCountPoi = mysqli_num_rows($result);

                    //If it does, save the ID
                    if ($rowCountPoi) {
                        if ($row = $result->fetch_assoc()) {
                            $poiId = $row['poiId'];
                        }
                    } else {
                        $poiId = null;
                    }
                }
                $checkPoiQuery->free_result();
                $checkPoiQuery->close();

                //Insertion in database
                if ($createAddressQuery = $conn->prepare('INSERT INTO addresses (addressId,addressName,street,houseNumber,houseNumberAddition,city,postalCode,latitude,longitude,companyId,poiId,active,created_at,status,labelId) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ')) {
                    $addressId = idCreationTrait::requestTokenId();
                    //Save the ID of the first address

                    $addressName = "School adres";
                    $street = $participant['streetDeliver'];
                    $houseNumber = $participant['houseNumberDeliver'];
                    $houseNumberAddition = $participant['houseNumberAdditionDeliver'];
                    $city = $participant['cityDeliver'];
                    $postalCode = $participant['postalCodeDeliver'];
                    $lat = $coordinates['lat'];
                    $lng = $coordinates['lng'];
                    $active = 1;
                    $created = date("Y-m-d h:i:s", time());
                    $createAddressQuery->bind_param('sssssssssssisis', $addressId, $addressName, $street, $houseNumber, $houseNumberAddition, $city, $postalCode, $lat, $lng, $companyId, $poiId, $active, $created, $status, $label);
                    $createAddressQuery->execute();
                    print_r($conn->error);
                } else {
                    print_r($conn->error);
                    print_r('hier5');
                    return false;
                }
                //Insertion in connection table
                if ($participantAddressQuery = $conn->prepare('INSERT INTO participant_address ( participantId,addressId,active, created_at,marge,maxTravelTime,startDate,endDate,monday,tuesday,wednesday,thursday,friday,saturday,sunday) VALUES(?,?,1,?,?,?,?,?,?,?,?,?,?,?,?)')) {
                    $created = date("Y-m-d h:i:s", time());
                    $marge = $participant['marge'];
                    $maxTravelTime = $participant['maxTravelTime'];
                    $startDate = date("Y-m-d", strtotime($participant['startDate']));
                    $endDate = date("Y-m-d", strtotime($participant['endDate']));
                    if ($participant['monTimeGet'] != null) {
                        $monday = 1;
                    } else {
                        $monday = 0;
                    }
                    if ($participant['tueTimeGet'] != null) {
                        $tuesday = 1;
                    } else {
                        $tuesday = 0;
                    }
                    if ($participant['wedTimeGet'] != null) {
                        $wednesday = 1;
                    } else {
                        $wednesday = 0;
                    }
                    if ($participant['thuTimeGet'] != null) {
                        $thursday = 1;
                    } else {
                        $thursday = 0;
                    }
                    if ($participant['friTimeGet'] != null) {
                        $friday = 1;
                    } else {
                        $friday = 0;
                    }
                    if ($participant['satTimeGet'] != null) {
                        $saturday = 1;
                    } else {
                        $saturday = 0;
                    }
                    if ($participant['sunTimeGet'] != null) {
                        $sunday = 1;
                    } else {
                        $sunday = 0;
                    }
                    $participantAddressQuery->bind_param('sssiisssssssss', $participantId, $addressId, $created, $marge, $maxTravelTime, $startDate, $endDate, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);
                    $participantAddressQuery->execute();
                    $participantAddressQuery->close();
                    return true;
                } else {
                    print_r($conn->error);

                    print_r('hier6');
                    return false;
                }
            }
        }
        return false;
    }

    private static function checkIfConnectionExists($conn, $addressId, $participantId)
    {
        //If the address exist it still needs to be check if the connection table is up to date
        //Look for a connection

        if ($participantAddressCheckQuery = $conn->prepare('SELECT * FROM participant_address WHERE participantId=? AND addressId=? ')) {

            $participantAddressCheckQuery->bind_param('ss', $participantId, $addressId);
            $participantAddressCheckQuery->execute();
            $result = $participantAddressCheckQuery->get_result();
            $rowCountConnectionDelivery = mysqli_num_rows($result);
            $participantAddressCheckQuery->close();
            if ($rowCountConnectionDelivery == 0) {

                //Connection does not exist
                return false;
            }
            return true;

        }
        print_r($conn->error);
        return 500;
    }

    private static function createConnection($conn, $participant, $addressId, $status, $participantId)
    {
        switch ($status) {
            case 0:
            {
                //Insertion in connection table
                if ($participantAddressQuery = $conn->prepare('INSERT INTO participant_address ( participantId,addressId,active, created_at,marge,maxTravelTime,startDate,endDate,monday,tuesday,wednesday,thursday,friday,saturday,sunday) VALUES(?,?,1,?,?,?,?,?,?,?,?,?,?,?,?)')) {
                    $created = date("Y-m-d h:i:s", time());
                    $marge = $participant['marge'];
                    $maxTravelTime = $participant['maxTravelTime'];
                    $startDate = date("Y-m-d", strtotime($participant['startDate']));
                    $endDate = date("Y-m-d", strtotime($participant['endDate']));
                    if ($participant['monTimeGet'] != null) {
                        $monday = 1;
                    } else {
                        $monday = 0;
                    }
                    if ($participant['tueTimeGet'] != null) {
                        $tuesday = 1;
                    } else {
                        $tuesday = 0;
                    }
                    if ($participant['wedTimeGet'] != null) {
                        $wednesday = 1;
                    } else {
                        $wednesday = 0;
                    }
                    if ($participant['thuTimeGet'] != null) {
                        $thursday = 1;
                    } else {
                        $thursday = 0;
                    }
                    if ($participant['friTimeGet'] != null) {
                        $friday = 1;
                    } else {
                        $friday = 0;
                    }
                    if ($participant['satTimeGet'] != null) {
                        $saturday = 1;
                    } else {
                        $saturday = 0;
                    }
                    if ($participant['sunTimeGet'] != null) {
                        $sunday = 1;
                    } else {
                        $sunday = 0;
                    }
                    $participantAddressQuery->bind_param('sssiisssssssss', $participantId, $addressId, $created, $marge, $maxTravelTime, $startDate, $endDate, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);
                    $participantAddressQuery->execute();
                    $participantAddressQuery->close();
                    return true;
                } else {
                    print_r($conn->error);
                    return 500;
                }
            }

            case 1:
            {
                //Insertion in connection table
                if ($participantAddressQuery = $conn->prepare('INSERT INTO participant_address ( participantId,addressId,active, created_at,marge,maxTravelTime,startDate,endDate,monday,tuesday,wednesday,thursday,friday,saturday,sunday) VALUES(?,?,1,?,?,?,?,?,?,?,?,?,?,?,?)')) {
                    $created = date("Y-m-d h:i:s", time());
                    $marge = $participant['marge'];
                    $maxTravelTime = $participant['maxTravelTime'];
                    $startDate = date("Y-m-d", strtotime($participant['startDate']));
                    $endDate = date("Y-m-d", strtotime($participant['endDate']));
                    if ($participant['monTimeDeliver'] != null) {
                        $monday = 1;
                    } else {
                        $monday = 0;
                    }
                    if ($participant['tueTimeDeliver'] != null) {
                        $tuesday = 1;
                    } else {
                        $tuesday = 0;
                    }
                    if ($participant['wedTimeDeliver'] != null) {
                        $wednesday = 1;
                    } else {
                        $wednesday = 0;
                    }
                    if ($participant['thuTimeDeliver'] != null) {
                        $thursday = 1;
                    } else {
                        $thursday = 0;
                    }
                    if ($participant['friTimeDeliver'] != null) {
                        $friday = 1;
                    } else {
                        $friday = 0;
                    }
                    if ($participant['satTimeDeliver'] != null) {
                        $saturday = 1;
                    } else {
                        $saturday = 0;
                    }
                    if ($participant['sunTimeDeliver'] != null) {
                        $sunday = 1;
                    } else {
                        $sunday = 0;
                    }
                    $participantAddressQuery->bind_param('sssiisssssssss', $participantId, $addressId, $created, $marge, $maxTravelTime, $startDate, $endDate, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);
                    $participantAddressQuery->execute();
                    $participantAddressQuery->close();
                    return true;
                } else {
                    print_r($conn->error);
                    return 500;
                }
            }
        }
        return false;
    }


    private
    static function createParticipant($conn, $participant, $companyId, $label)
    {

        if ($createParticipantQuery = $conn->prepare('INSERT INTO participants (participantId,name,companyId,active,created_at,labelId,participantNumber,phoneNumber,phoneNumber2,email,birthDate,gender,mon_arrival_time,tue_arrival_time,wed_arrival_time,thu_arrival_time,fri_arrival_time,sat_arrival_time,sun_arrival_time,mon_departure_time,tue_departure_time,wed_departure_time,thu_departure_time,fri_departure_time,sat_departure_time,sun_departure_time) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ')) {
            $birthDate = date("Y-m-d", strtotime($participant['birthDate']));
            $participant['participantId'] = idCreationTrait::requestTokenId();
            $participant['name'] = $participant['firstName'] . ". " . $participant['insertion'] . ". " . $participant['familyName'];
            $active = 1;
            $created = date("Y-m-d h:i:s", time());
            $participantNumber = $participant['participantNumber'];
            $phone1 = $participant['telephone1'];
            $phone2 = $participant['telephone2'];
            $email = $participant['email'];
            $gender = $participant['gender'];
            if ($participant['monTimeGet'] == null) {
                $mondayGet = null;
            } else {
                $mondayGet = $participant['monTimeGet'];
            }
            if ($participant['tueTimeGet'] == null) {
                $tuesdayGet = null;
            } else {
                $tuesdayGet = $participant['tueTimeGet'];
            }
            if ($participant['wedTimeGet'] == null) {
                $wednesdayGet = null;
            } else {
                $wednesdayGet = $participant['wedTimeGet'];
            }
            if ($participant['thuTimeGet'] == null) {
                $thursdayGet = null;
            } else {
                $thursdayGet = $participant['thuTimeGet'];
            }
            if ($participant['friTimeGet'] == null) {
                $fridayGet = null;
            } else {
                $fridayGet = $participant['friTimeGet'];
            }
            if ($participant['satTimeGet'] == null) {
                $saturdayGet = null;
            } else {
                $saturdayGet = $participant['satTimeGet'];
            }
            if ($participant['sunTimeGet'] == null) {
                $sundayGet = null;
            } else {
                $sundayGet = $participant['sunTimeGet'];
            }

            if ($participant['monTimeDeliver'] == null) {
                $mondayDel = null;
            } else {
                $mondayDel = $participant['monTimeDeliver'];
            }
            if ($participant['tueTimeDeliver'] == null) {
                $tuesdayDel = null;
            } else {
                $tuesdayDel = $participant['tueTimeDeliver'];
            }
            if ($participant['wedTimeDeliver'] == null) {
                $wednesdayDel = null;
            } else {
                $wednesdayDel = $participant['wedTimeDeliver'];
            }
            if ($participant['thuTimeDeliver'] == null) {
                $thursdayDel = null;
            } else {
                $thursdayDel = $participant['thuTimeDeliver'];
            }
            if ($participant['friTimeDeliver'] == null) {
                $fridayDel = null;
            } else {
                $fridayDel = $participant['friTimeDeliver'];
            }
            if ($participant['satTimeDeliver'] == null) {
                $saturdayDel = null;
            } else {
                $saturdayDel = $participant['satTimeDeliver'];
            }
            if ($participant['sunTimeDeliver'] == null) {
                $sundayDel = null;
            } else {
                $sundayDel = $participant['sunTimeDeliver'];
            }


            $createParticipantQuery->bind_param('sssissssssssssssssssssssss', $participant['participantId'], $participant['name'], $companyId, $active, $created, $label, $participantNumber, $phone1, $phone2, $email, $birthDate, $gender, $mondayGet, $tuesdayGet, $wednesdayGet, $thursdayGet, $fridayGet, $saturdayGet, $sundayGet, $mondayDel, $tuesdayDel, $wednesdayDel, $thursdayDel, $fridayDel, $saturdayDel, $sundayDel);
            $createParticipantQuery->execute();
            $createParticipantQuery->close();
            return $participant;
        }
        return false;
    }


    public
    static function createParticipantsWithAddresses($participantList, $label, $companyId): bool
    {
        $conn = connection::getConnection();
        //Each person will be checked for possible new addresses
        foreach ($participantList as $participant) {

            $searchedParticipant = self::checkIfParticipantExists($conn, $label, $participant, $companyId);
            if ($searchedParticipant == false) {
                //false means no participant
                $createdParticipant = self::createparticipant($conn, $participant, $companyId, $label);
                if ($createdParticipant == false) {
                    //false means failed to create
                    print_r("createparticipant");
                    return "createparticipant";
                }
                //check address
                //Fetch
                $checkedAddress = self::checkIfAddressExists($conn, $label, $participant, 0);
                if ($checkedAddress == false) {
                    //false means no address
                    $createdAddress = self::createAddress($conn, $participant, $label, $companyId, 0, $createdParticipant['participantId']);
                    if ($createdAddress == false) {
                        //false means failed to create
                        print_r("address");
                        return 'create address';
                    }
                    //created
                } else if ($checkedAddress === 500) {
                    //error in switch
                    print_r("address");
                    return "check address";
                } else {
                    //Means there is an address
                    $checkedConnection = self::checkIfConnectionExists($conn, $checkedAddress, $createdParticipant['participantId']);
                    if ($checkedConnection == false) {
                        self::createConnection($conn, $participant, $checkedAddress, 0, $createdParticipant['participantId']);
                    } else if ($checkedConnection === 500) {
                        //500 means query fault
                        print_r("connection");
                        return "check connection";
                    }
                    //connection exists or has been made
                }

                ////////////////////////////////////////////////////////////////////////////////////////////////////
                //check address
                //Delivery
                $checkedAddress = self::checkIfAddressExists($conn, $label, $participant, 1);
                if ($checkedAddress == false) {
                    //false means no address
                    $createdAddress = self::createAddress($conn, $participant, $label, $companyId, 1, $createdParticipant['participantId']);
                    if ($createdAddress == false) {
                        //false means failed to create
                        print_r('rip');
                        return "create fails";
                    }
                    //created
                } else {
                    //Means there is an address

                    $checkedConnection = self::checkIfConnectionExists($conn, $checkedAddress, $createdParticipant['participantId']);
                    if ($checkedConnection == false) {
                        self::createConnection($conn, $participant, $checkedAddress, 1, $createdParticipant['participantId']);
                    } else if ($checkedConnection === 500) {
                        //500 means query fault
                        print_r("connection");
                        return "check connection";
                    }
                    //connection exists or hs been made
                }
            } else if ($searchedParticipant === 500) {
                //500 means query or fetch fault
                print_r('rip');
                return "fetch fout";
            } else {
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //participant bestaat
                //update participant
                if (self::updateParticipant($conn, $participant, $searchedParticipant) == false) {
                    print_r('rip');
                    return "update gaat niet goed";
                }

                //check address
                //Fetch
                $checkedAddress = self::checkIfAddressExists($conn, $label, $participant, 0);
                if ($checkedAddress == false) {
                    //false means no address
                    $createdAddress = self::createAddress($conn, $participant, $label, $companyId, 0, $searchedParticipant['participantId']);
                    if ($createdAddress == false) {
                        print_r('rip');
                        //false means failed to create
                        return 'create address';
                    }
                    //created
                } else if ($checkedAddress === 500) {
                    //error in switch
                    print_r('ripad');
                    return "check address";
                } else {
                    //Means there is an address
                    $checkedConnection = self::checkIfConnectionExists($conn, $checkedAddress, $searchedParticipant['participantId']);
                    if ($checkedConnection == false) {
                        self::createConnection($conn, $participant, $checkedAddress, 0, $searchedParticipant['participantId']);
                    } elseif ($checkedConnection === 500) {
                        print_r('rip');
                        return 'connection1';
                    }
                    //connection exists or has been made
                }

                ////////////////////////////////////////////////////////////////////////////////////////////////////
                //check address
                //Delivery
                $checkedAddress = self::checkIfAddressExists($conn, $label, $participant, 1);
                if ($checkedAddress == false) {
                    //false means no address
                    $createdAddress = self::createAddress($conn, $participant, $label, $companyId, 1, $searchedParticipant['participantId']);
                    if ($createdAddress == false) {
                        //false means failed to create
                        print_r('rip');
                        return "create fails";
                    }
                    //created
                } else {
                    //Means there is an address
                    $checkedConnection = self::checkIfConnectionExists($conn, $checkedAddress, $searchedParticipant['participantId']);
                    if ($checkedConnection == false) {
                        self::createConnection($conn, $participant, $checkedAddress, 1, $searchedParticipant['participantId']);
                    } else if ($checkedConnection === 500) {
                        print_r('rip');
                        //500 means query fault
                        return "check connection";
                    }
                    //connection exists or hs been made
                }
            }
        }
        return true;
    }

    public
    static function getParticipantsWithLabel($labelId)
    {
        $conn = connection::getConnection();
        $returnData = [];
        if ($getParticipant = $conn->prepare('SELECT * FROM participants WHERE labelId=?')) {

            $getParticipant->bind_param('s', $labelId);
            $getParticipant->execute();
            $participantResult = $getParticipant->get_result();
            $rowCount = mysqli_num_rows($participantResult);
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $participantResult->fetch_assoc()) {
                        $rowDataParticipant = [];
                        $rowDataParticipant["participantId"] = $row['participantId'];
                        $rowDataParticipant["participantNumber"] = $row['participantNumber'];
                        $rowDataParticipant["companyId"] = $row['companyId'];
                        $rowDataParticipant["labelId"] = $row['labelId'];
                        $rowDataParticipant["name"] = $row['name'];
                        $rowDataParticipant["phoneNumber"] = $row['phoneNumber'];
                        $rowDataParticipant["phoneNumber2"] = $row['phoneNumber2'];
                        $rowDataParticipant["email"] = $row['email'];
                        $rowDataParticipant["birthDate"] = $row['birthDate'];
                        $rowDataParticipant["gender"] = $row['gender'];
                        $rowDataParticipant["monArrivalTime"] = $row['mon_arrival_time'];
                        $rowDataParticipant["tueArrivalTime"] = $row['tue_arrival_time'];
                        $rowDataParticipant["wedArrivalTime"] = $row['wed_arrival_time'];
                        $rowDataParticipant["thuArrivalTime"] = $row['thu_arrival_time'];
                        $rowDataParticipant["friArrivalTime"] = $row['fri_arrival_time'];
                        $rowDataParticipant["satArrivalTime"] = $row['sat_arrival_time'];
                        $rowDataParticipant["sunArrivalTime"] = $row['sun_arrival_time'];
                        $rowDataParticipant["monDepartureTime"] = $row['mon_departure_time'];
                        $rowDataParticipant["tueDepartureTime"] = $row['tue_departure_time'];
                        $rowDataParticipant["wedDepartureTime"] = $row['wed_departure_time'];
                        $rowDataParticipant["thuDepartureTime"] = $row['thu_departure_time'];
                        $rowDataParticipant["friDepartureTime"] = $row['fri_departure_time'];
                        $rowDataParticipant["satDepartureTime"] = $row['sat_departure_time'];
                        $rowDataParticipant["sunDepartureTime"] = $row['sun_departure_time'];
                    }
                    array_push($returnData, $rowDataParticipant);
                }
            }
        }
        //voor elk resultaat in hierboven
        for ($i = 0; $i < count($returnData); $i++) {

            if ($getParticipantAddresses = $conn->prepare('SELECT addresses.*,participant_address.* FROM addresses JOIN participant_address ON addresses.addressId = participant_address.addressId WHERE participant_address.participantId=? AND addresses.labelId=? ')) {
                $var1 = $returnData[$i]['participantId'];
                $var2 = $labelId;
                $getParticipantAddresses->bind_param('ss', $var1, $var2);
                $getParticipantAddresses->execute();
                $result = $getParticipantAddresses->get_result();
                $rowCount = mysqli_num_rows($result);
                $rowDataAddress = [];
                if ($rowCount) {
                    for ($x = 0; $x < $rowCount; $x++) {
                        if ($row = $result->fetch_assoc()) {
                            $rowData = [];
                            $rowData['addressId'] = $row['addressId'];
                            $rowData['addressName'] = $row['addressName'];
                            $rowData['street'] = $row['street'];
                            $rowData['houseNumber'] = $row['houseNumber'];
                            $rowData['houseNumberAddition'] = $row['houseNumberAddition'];
                            $rowData['city'] = $row['city'];
                            $rowData['postalCode'] = $row['postalCode'];
                            $rowData['country'] = $row['country'];
                            $rowData['latitude'] = $row['latitude'];
                            $rowData['longitude'] = $row['longitude'];
                            $rowData['phoneNumber'] = $row['phoneNumber'];
                            $rowData['contactPerson'] = $row['contactPerson'];
                            $rowData['companyId'] = $row['companyId'];
                            $rowData['poiId'] = $row['poiId'];
                            $rowData['active'] = $row['active'];
                            $rowData['labelId'] = $row['labelId'];
                            $rowData['status'] = $row['status'];
                            $rowData['marge'] = $row['marge'];
                            $rowData['maxTravelTime'] = $row['maxTravelTime'];
                            $rowData['startDate'] = $row['startDate'];
                            $rowData['endDate'] = $row['endDate'];
                            $rowData['monday'] = $row['monday'];
                            $rowData['tuesday'] = $row['tuesday'];
                            $rowData['wednesday'] = $row['wednesday'];
                            $rowData['thursday'] = $row['thursday'];
                            $rowData['friday'] = $row['friday'];
                            $rowData['saturday'] = $row['saturday'];
                            $rowData['sunday'] = $row['sunday'];
                        }
                        array_push($rowDataAddress, $rowData);
                    }
                }
                $returnData[$i]['addresses'] = $rowDataAddress;
            }
        }
        return $returnData;


    }

    public static function createArrangedListOfParticipants($participantList)
    {
        $groupsArray = [];

        foreach ($participantList as $participant) {
            for ($i = 0; $i < count($participant['addresses']); $i++) {
                if ($participant['addresses'][$i]['poiId'] != null) {
                    if (count($groupsArray[$participant['addresses'][$i]['poiId']]) == 0) {

                        $groupsArray[$participant['addresses'][$i]['poiId']]['participants'] = [];

                        array_push($groupsArray[$participant['addresses'][$i]['poiId']]['participants'], $participant);

                    } else {
                        array_push($groupsArray[$participant['addresses'][$i]['poiId']]['participants'], $participant);
                    }
                }
            }
        }

        $daysArray = ['monday' => ['pois' => []], 'tuesday' => ['pois' => []], 'wednesday' => ['pois' => []], 'thursday' => ['pois' => []], 'friday' => ['pois' => []], 'saturday' => ['pois' => []], 'sunday' => ['pois' => []]];
        $dayForLoop = [0 => 'monday', 1 => 'tuesday', 2 => 'wednesday', 3 => 'thursday', 4 => 'friday', 5 => 'saturday', 6 => 'sunday'];


        foreach ($groupsArray as $poi) {
            $poiId = $poi['participants'][0]['addresses'][1]['poiId'];
            //Check all the participant from a certain poi
            for ($i = 0; $i < count($poi['participants']); $i++) {
                //Check every delivery address
                for ($x = 0; $x < count($poi['participants'][$i]['addresses']); $x++) {
                    //if it is an odd number
                    if ($x % 2 == 1) {
                        //loop trough the days of the week
                        for ($y = 0; $y < 7; $y++) {
                            //1 means the participant needs to participate that day
                            if ($poi['participants'][$i]['addresses'][$x][$dayForLoop[$y]] == 1) {
                                if (count($daysArray[$dayForLoop[$y]]['pois'][$poiId]) == 0) {
                                    //if 0 means that the poi doesnt exist in the array
                                    $daysArray[$dayForLoop[$y]]['pois'][$poiId]['participants'] = [];
                                    array_push($daysArray[$dayForLoop[$y]]['pois'][$poiId]['participants'], $poi['participants'][$i]);
                                } else {
                                    array_push($daysArray[$dayForLoop[$y]]['pois'][$poiId]['participants'], $poi['participants'][$i]);
                                }
                            }
                        }
                    }
                }
            }
        }
        $returnArray = self::createGroupsForRouting($daysArray);

        return $returnArray;

    }

    private static function createGroupsForRouting($arrangedParticipant)
    {
        $dayForLoop = [0 => 'monday', 1 => 'tuesday', 2 => 'wednesday', 3 => 'thursday', 4 => 'friday', 5 => 'saturday', 6 => 'sunday'];
        $groupsArray = ['monday' => ['groups' => []], 'tuesday' => ['groups' => []], 'wednesday' => ['groups' => []], 'thursday' => ['groups' => []], 'friday' => ['groups' => []], 'saturday' => ['groups' => []], 'sunday' => ['groups' => []]];


        for ($i = 0; $i < 7; $i++) {
            //voor elke dag
            $groups = $dayForLoop[$i] . $i;
            if ($arrangedParticipant[$dayForLoop[$i]]['pois'] != null) {
                foreach ($arrangedParticipant[$dayForLoop[$i]]['pois'] as $poi) {

                    if ($poi['participants'] != null) {
                        foreach ($poi['participants'] as $participant) {
                            //voor elke deelnemer in poi
                            if ($groupsArray[$dayForLoop[$i]]['groups'][$groups] == null) {
                                $groupsArray[$dayForLoop[$i]]['groups'][$groups]['participants'] = [];
                                array_push($groupsArray[$dayForLoop[$i]]['groups'][$groups]['participants'], $participant);
                            } else {
                                array_push($groupsArray[$dayForLoop[$i]]['groups'][$groups]['participants'], $participant);
                                if (count($groupsArray[$dayForLoop[$i]]['groups'][$groups]) == 6) {
                                    $groups++;
                                }
                            }
                        }
                    }
                    //leeg dus volgende poi
                    $groups++;

                }

            }
        }
        return $groupsArray;
    }


    public static function createFinalGroupsForSystem($groupList)
    {
        $conn = connection::getConnection();
        $dayForLoop = [0 => 'monday', 1 => 'tuesday', 2 => 'wednesday', 3 => 'thursday', 4 => 'friday', 5 => 'saturday', 6 => 'sunday'];


        //For every day of the week
        for ($i = 0; $i < 7; $i++) {
            $groupNumber = 1;

            //For every group in the list
            foreach ($groupList[$dayForLoop[$i]]['groups'] as $group) {
                $name = $groupList['groupName'];
                $destionationList = [];
                $chauffeurLat = $groupList['chauffeurLat'];
                $chauffeurLng = $groupList['chauffeurLng'];
                //chauffeur address
                array_push($destionationList, [$chauffeurLat, $chauffeurLng]);

                //for every
                for ($x = 0; $x < count($group['participants']); $x++) {
                    $lat = $group['participants'][$x]['addresses'][0]['latitude'];
                    $lng = $group['participants'][$x]['addresses'][0]['longitude'];
                    array_push($destionationList, [$lat, $lng]);
                }
                $poiLat = $group['participants'][0]['addresses'][1]['latitude'];
                $poiLng = $group['participants'][0]['addresses'][1]['longitude'];
                array_push($destionationList, [$poiLat, $poiLng]);
                array_push($destionationList, [$chauffeurLat, $chauffeurLng]);

                //Create address object
                if ($createGroupQuery = $conn->prepare('INSERT INTO `groups`(`groupId`, `companyId`, `groupName`, `route`, `periodStart`, `periodEnd`, `active`,created_at,day,labelId) VALUES (?,?,?,?,?,?,?,?,?,?)')) {
                    $groupId = idCreationTrait::requestTokenId();
                    $companyId = $group['participants'][0]['companyId'];
                    $name = $name . " " . $dayForLoop[$i] . " " . $groupNumber;
                    $groupRoute = json_encode(routingTrait::optimizeRoutingServiceRoute($destionationList));
                    $periodStart = $group['participants'][0]['addresses'][0]['startDate'];
                    $periodEnd = $group['participants'][0]['addresses'][0]['endDate'];
                    $active = 1;
                    $created = date("Y-m-d h:i:s", time());
                    $day = $dayForLoop[$i];
                    $labelId = $group['participants'][0]['labelId'];
                    $createGroupQuery->bind_param('ssssssisss', $groupId, $companyId, $name, $groupRoute, $periodStart, $periodEnd, $active, $created, $day, $labelId);
                    $createGroupQuery->execute();

                    foreach ($group['participants'] as $participant) {
                        //koppeltabel
                        if ($createGroupConnection = $conn->prepare('INSERT INTO participant_group(groupId,participantId,fromAddressId,toAddressId,active,created_at) VALUES(?,?,?,?,?,?) ')) {
                            $participantId = $participant['participantId'];
                            $fromAddressId = $participant['addresses'][0]['addressId'];
                            $toAddressId = $participant['addresses'][1]['addressId'];
                            $created = date("Y-m-d h:i:s", time());
                            $createGroupConnection->bind_param('ssssis', $groupId, $participantId, $fromAddressId, $toAddressId, $active, $created);
                            $createGroupConnection->execute();

                        } else {
                            return false;
                        }


                    }

                } else {
                    return false;
                }
                $groupNumber++;
            }
        }
        return true;
    }


}



