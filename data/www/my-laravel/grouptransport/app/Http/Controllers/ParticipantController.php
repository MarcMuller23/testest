<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function createParticipant(Request $request)
    {
        $jsonRequest = json_decode($request->getBody(), true);
        //Getting the connection
        $conn = connection::getConnection();
        //Create address object
        $participant = participantTrait::getParticipantObjectPost($jsonRequest);


        if ($participant == false) {
            $body = $response->getBody();
            $body->write('Could not create participant object');
            return $response->withBody($body)->withStatus(500);
        }
        if ($participantQuery = $conn->prepare('INSERT INTO participants (participantId,name,phoneNumber,companyId,active,created_at,labelId,participantNumber,phoneNumber2,email,birthDate,gender,mon_arrival_time,tue_arrival_time,wed_arrival_time,thu_arrival_time,fri_arrival_time,sat_arrival_time,sun_arrival_time,mon_departure_time,tue_departure_time,wed_departure_time,thu_departure_time,fri_departure_time,sat_departure_time,sun_departure_time) VALUES (?,?,?,?,1,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?); ')) {
            $var1 = $participant->participantId;
            $var2 = $participant->name;
            $var3 = $participant->phoneNumber;
            $var4 = $participant->companyId;
            $var5 = date("Y-m-d h:i:s", time());;
            $var6 =$participant->labelId;
            $var7 =$participant->participantNumber;
            $var8 =$participant->phoneNumber2;
            $var9 =$participant->email;
            $var10 =$participant->birthDate;
            $var11 =$participant->gender;
            $var12 =$participant->monArrivalTime;
            $var13 =$participant->tueArrivalTime;
            $var14 =$participant->wedArrivalTime;
            $var15 =$participant->thuArrivalTime;
            $var16 =$participant->friArrivalTime;
            $var17 =$participant->satArrivalTime;
            $var18 =$participant->sunArrivalTime;
            $var19 =$participant->monDepartureTime;
            $var20 =$participant->tueDepartureTime;
            $var21 =$participant->wedDepartureTime;
            $var22 =$participant->thuDepartureTime;
            $var23 =$participant->friDepartureTime;
            $var24 =$participant->satDepartureTime;
            $var25 =$participant->sunDepartureTime;
            $participantQuery->bind_param('sssssssssssssssssssssssss', $var1, $var2, $var3, $var4,$var5,$var6,$var7,$var8,$var9,$var10,$var11,$var12,$var13,$var14,$var15,$var16,$var17,$var18,$var19,$var20,$var21,$var22,$var23,$var24,$var25);
            $participantQuery->execute();
            $participantQuery->close();

            $body = $response->getBody();
            $body->write('Participant created');
            return $response->withBody($body)->withStatus(200);
        } else {
            $body = $response->getBody();
            $body->write('Query fault in creating participant');
            return $response->withBody($body)->withStatus(500);
        }
    }

    public function updateParticipant(Request $request)
    {
        $jsonRequest = json_decode($request->getBody(), true);
        //Getting the connection
        $conn = connection::getConnection();
        //Create address object
        $participant = participantTrait::getParticipantObjectPut($jsonRequest);
        if ($participant == false) {
            $body = $response->getBody();
            $body->write('Could not create participant object');
            return $response->withBody($body)->withStatus(500);
        }
        if ($participantQuery = $conn->prepare('UPDATE participants SET name=?,phoneNumber=?,companyId=?,active=1,updated_at=?,labelId=?,participantNumber=?,phoneNumber=?,email=?,birthDate=?,gender=?,mon_arrival_time=?,tue_arrival_time=?,wed_arrival_time=?,thu_arrival_time=?,fri_arrival_time=?,sat_arrival_time=?,sun_arrival_time=?,mon_departure_time=?,tue_departure_time=?,wed_departure_time=?,thu_departure_time=?,fri_departure_time=?,sat_departure_time=?,sun_departure_time=? WHERE participantId=?; ')) {
            $var1 = $participant->name;
            $var2 = $participant->phoneNumber;
            $var3 = $participant->companyId;
            $var4 = $participant->participantId;
            $var5 = date("Y-m-d h:i:s", time());;
            $var6 =$participant->labelId;
            $var7 =$participant->participantNumber;
            $var8 =$participant->phoneNumber2;
            $var9 =$participant->email;
            $var10 =$participant->birthDate;
            $var11 =$participant->gender;
            $var12 =$participant->monArrivalTime;
            $var13 =$participant->tueArrivalTime;
            $var14 =$participant->wedArrivalTime;
            $var15 =$participant->thuArrivalTime;
            $var16 =$participant->friArrivalTime;
            $var17 =$participant->satArrivalTime;
            $var18 =$participant->sunArrivalTime;
            $var19 =$participant->monDepartureTime;
            $var20 =$participant->tueDepartureTime;
            $var21 =$participant->wedDepartureTime;
            $var22 =$participant->thuDepartureTime;
            $var23 =$participant->friDepartureTime;
            $var24 =$participant->satDepartureTime;
            $var25 =$participant->sunDepartureTime;
            $participantQuery->bind_param('sssssssssssssssssssssssss', $var1, $var2, $var3,$var5,$var6,$var7,$var8,$var9,$var10,$var11,$var12,$var13,$var14,$var15,$var16,$var17,$var18,$var19,$var20,$var21,$var22,$var23,$var24,$var25,$var4);
            $participantQuery->execute();
            $participantQuery->close();
            $body = $response->getBody();
            $body->write('Participant updated');
            return $response->withBody($body)->withStatus(200);

        } else {
            $body = $response->getBody();
            $body->write('Query fault on update');
            return $response->withBody($body)->withStatus(500);
        }


    }

    public function enableParticipant(Request $request)
    {
        $jsonRequest = json_decode($request->getBody());
        //Getting the connection
        $conn = connection::getConnection();
        if ($jsonRequest->participantId == null) {
            $body = $response->getBody();
            $body->write('No participantId given');
            return $response->withBody($body)->withStatus(500);
        }
        if ($participantQuery = $conn->prepare('UPDATE participants SET active=1,updated_at=? WHERE participantId=?; ')) {
            $var1 = $jsonRequest->participantId;
            $var2 = date("Y-m-d h:i:s", time());
            $participantQuery->bind_param('ss', $var2,$var1);
            $participantQuery->execute();
            $participantQuery->close();

            $body = $response->getBody();
            $body->write('Participant updated');
            return $response->withBody($body)->withStatus(200);

        } else {
            $body = $response->getBody();
            $body->write('Query fault on update');
            return $response->withBody($body)->withStatus(500);

        }


    }

    public function disableParticipant(Request $request)
    {
        $jsonRequest = json_decode($request->getBody());
        //Getting the connection
        $conn = connection::getConnection();
        //Create address object
        if ($jsonRequest->participantId == null) {
            $body = $response->getBody();
            $body->write('No participantId given');
            return $response->withBody($body)->withStatus(500);
        }
        if ($participantQuery = $conn->prepare('UPDATE participants SET active=0,updated_at=? WHERE participantId=?; ')) {
            $var1 = $jsonRequest->participantId;
            $var2 = date("Y-m-d h:i:s", time());
            $participantQuery->bind_param('ss', $var2,$var1);
            $participantQuery->execute();
            $participantQuery->close();

            $body = $response->getBody();
            $body->write('Participant updated');
            return $response->withBody($body)->withStatus(200);

        } else {
            $body = $response->getBody();
            $body->write('Query fault on update');
            return $response->withBody($body)->withStatus(500);

        }
    }

    public function searchParticipant(Request $request)
    {

        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());
        $participant = new stdClass();
        $participant->companyId = $jsonRequest->companyId;
        $participant->search = $jsonRequest->search;
        if ($participant->companyId != null and $participant->search != null) {
            if ($searchParticipantsQuery = $conn->prepare('SELECT * FROM participants WHERE name LIKE ? AND companyId=?')) {
                $var1 = '%' . $jsonRequest->search . '%';
                $var2 = $jsonRequest->companyId;
                $searchParticipantsQuery->bind_param('ss', $var1, $var2);
                $searchParticipantsQuery->execute();
                $result = $searchParticipantsQuery->get_result();
                $rowCount = mysqli_num_rows($result);
                $returnData = [];
                if ($rowCount) {
                    for ($i = 0; $i < $rowCount; $i++) {
                        if ($row = $result->fetch_assoc()) {
                            $rowData = [];
                            $rowData["participantId"] = $row['participantId'];
                            $rowData["name"] = $row['name'];
                            $rowData["phoneNumber"] = $row['phoneNumber'];
                            $rowData["companyId"] = $row['companyId'];
                            $rowData["active"] = $row['active'];
                            $rowData["labelId"] = $row['labelId'];
                            $rowData["participantNumber"] = $row['participantNumber'];
                            $rowData["phoneNumber2"] = $row['phoneNumber2'];
                            $rowData["email"] = $row['email'];
                            $rowData["birthDate"] = $row['birthDate'];
                            $rowData["gender"] = $row['gender'];
                            $rowData["monArrivalTime"] = $row['mon_arrival_Time'];
                            $rowData["tueArrivalTime"] = $row['tue_arrival_Time'];
                            $rowData["wedArrivalTime"] = $row['wed_arrival_Time'];
                            $rowData["thuArrivalTime"] = $row['thu_arrival_Time'];
                            $rowData["friArrivalTime"] = $row['fri_arrival_Time'];
                            $rowData["satArrivalTime"] = $row['sat_arrival_Time'];
                            $rowData["sunArrivalTime"] = $row['sun_arrival_Time'];
                            $rowData["monDepartureTime"] = $row['mon_departure_time'];
                            $rowData["tueDepartureTime"] = $row['tue_departure_time'];
                            $rowData["wedDepartureTime"] = $row['wed_departure_time'];
                            $rowData["thuDepartureTime"] = $row['thu_departure_time'];
                            $rowData["friDepartureTime"] = $row['fri_departure_time'];
                            $rowData["satDepartureTime"] = $row['sat_departure_time'];
                            $rowData["sunDepartureTime"] = $row['sun_departure_time'];
                            array_push($returnData, $rowData);
                        }
                    }
                }
                $result->free_result();
                $searchParticipantsQuery->close();
                $body = $response->getBody();
                $body->write(json_encode($returnData));
                return $response->withBody($body)->withStatus(200);
            }
            $body = $response->getBody();
            $body->write('Query fault on select');
            return $response->withBody($body)->withStatus(500);
        }
        $body = $response->getBody();
        $body->write('Incomplete data in request');
        return $response->withBody($body)->withStatus(500);
    }

    public function getParticipant(Request $request)
    {
        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());
        $participant = new stdClass();
        $participant->companyId = $jsonRequest->companyId;
        $participant->participantId = $jsonRequest->participantId;
        if ($participant->companyId != null and $participant->participantId != null) {
            if ($searchParticipantsQuery = $conn->prepare('SELECT * FROM participants WHERE participantId= ? AND companyId=?')) {
                $var1 = $participant->participantId;
                $var2 = $participant->companyId;
                $searchParticipantsQuery->bind_param('ss', $var1, $var2);
                $searchParticipantsQuery->execute();
                $result = $searchParticipantsQuery->get_result();
                $rowCount = mysqli_num_rows($result);
                $returnData = [];
                if ($rowCount) {
                    for ($i = 0; $i < $rowCount; $i++) {
                        if ($row = $result->fetch_assoc()) {
                            $rowData = [];
                            $rowData["participantId"] = $row['participantId'];
                            $rowData["name"] = $row['name'];
                            $rowData["phoneNumber"] = $row['phoneNumber'];
                            $rowData["companyId"] = $row['companyId'];
                            $rowData["active"] = $row['active'];
                            $rowData["labelId"] = $row['labelId'];
                            $rowData["participantNumber"] = $row['participantNumber'];
                            $rowData["phoneNumber2"] = $row['phoneNumber2'];
                            $rowData["email"] = $row['email'];
                            $rowData["birthDate"] = $row['birthDate'];
                            $rowData["gender"] = $row['gender'];
                            $rowData["monArrivalTime"] = $row['mon_arrival_Time'];
                            $rowData["tueArrivalTime"] = $row['tue_arrival_Time'];
                            $rowData["wedArrivalTime"] = $row['wed_arrival_Time'];
                            $rowData["thuArrivalTime"] = $row['thu_arrival_Time'];
                            $rowData["friArrivalTime"] = $row['fri_arrival_Time'];
                            $rowData["satArrivalTime"] = $row['sat_arrival_Time'];
                            $rowData["sunArrivalTime"] = $row['sun_arrival_Time'];
                            $rowData["monDepartureTime"] = $row['mon_departure_time'];
                            $rowData["tueDepartureTime"] = $row['tue_departure_time'];
                            $rowData["wedDepartureTime"] = $row['wed_departure_time'];
                            $rowData["thuDepartureTime"] = $row['thu_departure_time'];
                            $rowData["friDepartureTime"] = $row['fri_departure_time'];
                            $rowData["satDepartureTime"] = $row['sat_departure_time'];
                            $rowData["sunDepartureTime"] = $row['sun_departure_time'];
                            array_push($returnData, $rowData);
                        }
                    }
                }
                $result->free_result();
                $searchParticipantsQuery->close();
                if ($getParticipantAddresses = $conn->prepare('SELECT addresses.* FROM addresses JOIN participant_address ON addresses.addressId = participant_address.addressId WHERE participant_address.participantId=?')) {
                    $var1 = $jsonRequest->participantId;
                    $getParticipantAddresses->bind_param('s', $var1);
                    $getParticipantAddresses->execute();
                    $result = $getParticipantAddresses->get_result();
                    $rowCount = mysqli_num_rows($result);
                    $addressData['addresses'] = [];
                    if ($rowCount) {
                        for ($i = 0; $i < $rowCount; $i++) {
                            if ($row = $result->fetch_assoc()) {
                                $addressList = [];
                                $addressList["addressId"] = $row['addressId'];
                                $addressList["addressName"] = $row['addressName'];
                                $addressList["street"] = $row['street'];
                                $addressList["houseNumber"] = $row['houseNumber'];
                                $addressList["houseNumberAddition"] = $row['houseNumberAddition'];
                                $addressList["city"] = $row['city'];
                                $addressList["postalCode"] = $row['postalCode'];
                                $addressList["country"] = $row['country'];
                                $addressList["latitude"] = $row['latitude'];
                                $addressList["longitude"] = $row['longitude'];
                                $addressList["phoneNumber"] = $row['phoneNumber'];
                                $addressList["contactPerson"] = $row['contactPerson'];
                                $addressList["companyId"] = $row['companyId'];
                                $addressList["poiId"] = $row['poiId'];
                                $addressList["active"] = $row['active'];
                                array_push($addressData['addresses'], $addressList);
                            }
                        }
                    }
                    array_push($returnData, $addressData);
                    $result->free_result();
                    $getParticipantAddresses->close();
                    $body = $response->getBody();
                    $body->write(json_encode($returnData));
                    return $response->withBody($body)->withStatus(200);
                }
                $body = $response->getBody();
                $body->write('Fault in query on select addresses ');
                return $response->withBody($body)->withStatus(500);
            }
            $body = $response->getBody();
            $body->write('Fault in query on select participants');
            return $response->withBody($body)->withStatus(500);
        }
        $body = $response->getBody();
        $body->write('Incomplete data in request');
        return $response->withBody($body)->withStatus(500);
    }

    public function getCompanyParticipants(Request $request)
    {
        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());
        $participant = new stdClass();
        $participant->companyId = $jsonRequest->companyId;
        $participant->detailed = $jsonRequest->detailed;
        if ($participant->companyId != null) {
            if ($participant->detailed == 0) {
                if ($searchParticipantsQuery = $conn->prepare('SELECT * FROM participants WHERE companyId=? ORDER BY name DESC')) {
                    $var1 = $jsonRequest->companyId;
                    $searchParticipantsQuery->bind_param('s', $var1);
                    $searchParticipantsQuery->execute();
                    $result = $searchParticipantsQuery->get_result();
                    $rowCount = mysqli_num_rows($result);
                    $returnData = [];
                    if ($rowCount) {
                        for ($i = 0; $i < $rowCount; $i++) {
                            if ($row = $result->fetch_assoc()) {
                                $rowData = [];
                                $rowData["participantId"] = $row['participantId'];
                                $rowData["name"] = $row['name'];
                                $rowData["phoneNumber"] = $row['phoneNumber'];
                                $rowData["companyId"] = $row['companyId'];
                                $rowData["active"] = $row['active'];
                                $rowData["labelId"] = $row['labelId'];
                                $rowData["participantNumber"] = $row['participantNumber'];
                                $rowData["phoneNumber2"] = $row['phoneNumber2'];
                                $rowData["email"] = $row['email'];
                                $rowData["birthDate"] = $row['birthDate'];
                                $rowData["gender"] = $row['gender'];
                                $rowData["monArrivalTime"] = $row['mon_arrival_Time'];
                                $rowData["tueArrivalTime"] = $row['tue_arrival_Time'];
                                $rowData["wedArrivalTime"] = $row['wed_arrival_Time'];
                                $rowData["thuArrivalTime"] = $row['thu_arrival_Time'];
                                $rowData["friArrivalTime"] = $row['fri_arrival_Time'];
                                $rowData["satArrivalTime"] = $row['sat_arrival_Time'];
                                $rowData["sunArrivalTime"] = $row['sun_arrival_Time'];
                                $rowData["monDepartureTime"] = $row['mon_departure_time'];
                                $rowData["tueDepartureTime"] = $row['tue_departure_time'];
                                $rowData["wedDepartureTime"] = $row['wed_departure_time'];
                                $rowData["thuDepartureTime"] = $row['thu_departure_time'];
                                $rowData["friDepartureTime"] = $row['fri_departure_time'];
                                $rowData["satDepartureTime"] = $row['sat_departure_time'];
                                $rowData["sunDepartureTime"] = $row['sun_departure_time'];
                                array_push($returnData, $rowData);
                            }
                        }
                    }
                    $result->free_result();
                    $searchParticipantsQuery->close();
                    $body = $response->getBody();
                    $body->write(json_encode($returnData));
                    return $response->withBody($body)->withStatus(200);
                } else {
                    $body = $response->getBody();
                    $body->write('Fault in query on select, detailed=0');
                    return $response->withBody($body)->withStatus(500);
                }
            } else if ($searchParticipantsQuery = $conn->prepare('SELECT * FROM participants WHERE companyId=? ORDER BY name DESC')) {
                $var1 = $jsonRequest->companyId;
                $searchParticipantsQuery->bind_param('s', $var1);
                $searchParticipantsQuery->execute();
                $result = $searchParticipantsQuery->get_result();
                $rowCount = mysqli_num_rows($result);
                $returnData = [];
                if ($rowCount) {
                    for ($i = 0; $i < $rowCount; $i++) {
                        if ($row = $result->fetch_assoc()) {
                            $rowData = [];
                            $rowData["participantId"] = $row['participantId'];
                            $rowData["name"] = $row['name'];
                            $rowData["phoneNumber"] = $row['phoneNumber'];
                            $rowData["companyId"] = $row['companyId'];
                            $rowData["active"] = $row['active'];
                            $rowData["labelId"] = $row['labelId'];
                            $rowData["participantNumber"] = $row['participantNumber'];
                            $rowData["phoneNumber2"] = $row['phoneNumber2'];
                            $rowData["email"] = $row['email'];
                            $rowData["birthDate"] = $row['birthDate'];
                            $rowData["gender"] = $row['gender'];
                            $rowData["monArrivalTime"] = $row['mon_arrival_Time'];
                            $rowData["tueArrivalTime"] = $row['tue_arrival_Time'];
                            $rowData["wedArrivalTime"] = $row['wed_arrival_Time'];
                            $rowData["thuArrivalTime"] = $row['thu_arrival_Time'];
                            $rowData["friArrivalTime"] = $row['fri_arrival_Time'];
                            $rowData["satArrivalTime"] = $row['sat_arrival_Time'];
                            $rowData["sunArrivalTime"] = $row['sun_arrival_Time'];
                            $rowData["monDepartureTime"] = $row['mon_departure_time'];
                            $rowData["tueDepartureTime"] = $row['tue_departure_time'];
                            $rowData["wedDepartureTime"] = $row['wed_departure_time'];
                            $rowData["thuDepartureTime"] = $row['thu_departure_time'];
                            $rowData["friDepartureTime"] = $row['fri_departure_time'];
                            $rowData["satDepartureTime"] = $row['sat_departure_time'];
                            $rowData["sunDepartureTime"] = $row['sun_departure_time'];
                            array_push($returnData, $rowData);
                        }
                    }
                }
                $result->free_result();
                $searchParticipantsQuery->close();
                $body = $response->getBody();
                $body->write(json_encode($returnData));
                return $response->withBody($body)->withStatus(200);
            }
            $body = $response->getBody();
            $body->write('Fault in query on select, detailed=1');
            return $response->withBody($body)->withStatus(500);
        }
        $body = $response->getBody();
        $body->write('Incomplete data in request');
        return $response->withBody($body)->withStatus(500);
    }

}
