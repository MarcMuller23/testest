<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoiController extends Controller
{
    public function getAllPoi(Request $request)
    {

        $conn = connection::getConnection();
        if ($searchPoisQuery = $conn->prepare('SELECT * FROM pois  ')) {
            $searchPoisQuery->execute();
            $result = $searchPoisQuery->get_result();
            $rowCount = mysqli_num_rows($result);
            $returnData = [];
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $result->fetch_assoc()) {
                        $rowData = [];
                        $rowData["poiId"] = $row['poiId'];
                        $rowData["companyId"] = $row['companyId'];
                        $rowData["poiName"] = $row['poiName'];
                        $rowData["street"] = $row['street'];
                        $rowData["houseNumber"] = $row['houseNumber'];
                        $rowData["houseNumberAddition"] = $row['houseNumberAddition'];
                        $rowData["city"] = $row['city'];
                        $rowData["postalCode"] = $row['postalCode'];
                        $rowData["country"] = $row['country'];
                        $rowData["phoneNumber"] = $row['phoneNumber'];
                        $rowData["contactPerson"] = $row['contactPerson'];
                        $rowData["openTime"] = $row['openTime'];
                        $rowData["closeTime"] = $row['closeTime'];
                        $rowData["remark"] = $row['remark'];
                        $rowData["active"] = $row['active'];
                        array_push($returnData, $rowData);
                    }
                }
            }
            $result->free_result();
            $searchPoisQuery->close();
            $body = $response->getBody();
            $body->write(json_encode($returnData));
            return $response->withBody($body)->withStatus(200);
        } else {
            $body = $response->getBody();
            $body->write('Query fault on select');
            return $response->withBody($body)->withStatus(500);
        }
    }

    public function createPoi(Request $request)
    {

        $jsonRequest = json_decode($request->getBody(), true);
        //Getting the connection
        $conn = connection::getConnection();
        $poi = poiTrait::getPoiObjectPost($jsonRequest);
        if ($poi == false) {
            $body = $response->getBody();
            $body->write('Poi object could not be created');
            return $response->withBody($body)->withStatus(500);

        } else if ($poiQuery = $conn->prepare('INSERT INTO pois (poiId,companyId,poiName,street,houseNumber,houseNumberAddition,city,postalCode,country,latitude,longitude,phoneNumber,contactPerson,openTime,closeTime,remark,active,created_at,labelId)
              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?); ')
        ) {
            $var1 = $poi->poiId;
            $var2 = $poi->companyId;
            $var3 = $poi->poiName;
            $var4 = $poi->street;
            $var5 = $poi->houseNumber;
            $var6 = $poi->houseNumberAddition;
            $var7 = $poi->city;
            $var8 = $poi->postalCode;
            $var9 = $poi->country;
            $var10 = $poi->latitude;
            $var11 = $poi->longitude;
            $var12 = $poi->phoneNumber;
            $var13 = $poi->contactPerson;
            $var14 = $poi->openTime;
            $var15 = $poi->closeTime;
            $var16 = $poi->remark;
            $var17 = $poi->active;
            $var18 = date("Y-m-d h:i:s", time());
            $var19=$poi->labelId;
            $poiQuery->bind_param('ssssssssssssssssiss', $var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10, $var11, $var12, $var13, $var14, $var15, $var16, $var17, $var18,$var19);
            $poiQuery->execute();
            $poiQuery->close();


            $body = $response->getBody();
            $body->write('Poi has been created');
            return $response->withBody($body)->withStatus(200);

        } else {
            $body = $response->getBody();
            $body->write('Query fault on into');
            return $response->withBody($body)->withStatus(500);
        }

    }

    public function updatePoi(Request  $request)
    {

        $jsonRequest = json_decode($request->getBody(), true);
        //Getting the connection
        $conn = connection::getConnection();
        $poi = poiTrait::getPoiObjectPut($jsonRequest);

        if ($poi == null or $poi->poiId == null) {
            $body = $response->getBody();
            $body->write('Poi object could not be updated');
            return $response->withBody($body)->withStatus(500);
        } else if ($poiQuery = $conn->prepare('UPDATE pois SET companyId=?,poiName=?,street=?,houseNumber=?,houseNumberAddition=?,city=?,postalCode=?,country=?,latitude=?,longitude=?,phoneNumber=?,contactPerson=?,openTime=?,closeTime=?,remark=?,active=?,updated_at=?,labelId=? WHERE poiId=?; ')
        ) {
            $var1 = $poi->poiId;
            $var2 = $poi->companyId;
            $var3 = $poi->poiName;
            $var4 = $poi->street;
            $var5 = $poi->houseNumber;
            $var6 = $poi->houseNumberAddition;
            $var7 = $poi->city;
            $var8 = $poi->postalCode;
            $var9 = $poi->country;
            $var10 = $poi->latitude;
            $var11 = $poi->longitude;
            $var12 = $poi->phoneNumber;
            $var13 = $poi->contactPerson;
            $var14 = $poi->openTime;
            $var15 = $poi->closeTime;
            $var16 = $poi->remark;
            $var17 = $poi->active;
            $var18 = date("Y-m-d h:i:s", time());
            $var19 = $poi->labelId;
            $poiQuery->bind_param('sssssssssssssssisss', $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10, $var11, $var12, $var13, $var14, $var15, $var16, $var17, $var18,$var19, $var1);
            $poiQuery->execute();
            $body = $response->getBody();
            $body->write('Poi has been updated');
            return $response->withBody($body)->withStatus(200);
        }
        $body = $response->getBody();
        $body->write('Query fault on insert');
        return $response->withBody($body)->withStatus(500);

    }

    public function disablePoi(Request $request)
    {

        $jsonRequest = json_decode($request->getBody());
        $conn = connection::getConnection();
        $poi = new stdClass();
        $poi->poiId = $jsonRequest->poiId;
        if ($poi->poiId == null) {
            $body = $response->getBody();
            $body->write('Poi object could not be updated');
            return $response->withBody($body)->withStatus(500);
        } else if ($poiQuery = $conn->prepare('UPDATE pois SET active=0,updated_at=? WHERE poiId=?; ')
        ) {
            $var1 = date("Y-m-d h:i:s", time());
            $var2 = $poi->poiId;

            $poiQuery->bind_param('ss', $var1, $var2);
            $poiQuery->execute();
            $body = $response->getBody();
            $body->write('Poi has been deactivated');
            return $response->withBody($body)->withStatus(200);

        }
        $body = $response->getBody();
        $body->write('Query fault on update');
        return $response->withBody($body)->withStatus(500);
    }

    public function enablePoi(Request $request)
    {

        $jsonRequest = json_decode($request->getBody());
        $conn = connection::getConnection();
        $poi = new stdClass();
        $poi->poiId = $jsonRequest->poiId;
        if ($poi->poiId == null) {
            $body = $response->getBody();
            $body->write('Poi object could not be updated');
            return $response->withBody($body)->withStatus(500);
        } else if ($poiQuery = $conn->prepare('UPDATE pois SET active=1,updated_at=? WHERE poiId=?; ')
        ) {
            $var1 = date("Y-m-d h:i:s", time());
            $var2 = $poi->poiId;

            $poiQuery->bind_param('s', $var1);
            $poiQuery->execute();
            $body = $response->getBody();
            $body->write('Poi has been deactivated');
            return $response->withBody($body)->withStatus(200);

        }
        $body = $response->getBody();
        $body->write('Query fault on update');
        return $response->withBody($body)->withStatus(500);
    }

    public function getPoi(Request $request)
    {
        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());

        if ($jsonRequest->poiId == null) {
            $body = $response->getBody();
            $body->write('No poiId given');
            return $response->withBody($body)->withStatus(500);
        }
        if ($searchPoiQuery = $conn->prepare('SELECT * FROM pois WHERE poiId=? ')) {
            $var1 = $jsonRequest->poiId;

            $searchPoiQuery->bind_param('s', $var1);
            $searchPoiQuery->execute();

            $poi = $searchPoiQuery->get_result();
            $rowCount = mysqli_num_rows($poi);
            $returnData = [];

            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $poi->fetch_assoc()) {
                        $rowData = [];
                        $rowData["poiId"] = $row['poiId'];
                        $rowData["companyId"] = $row['companyId'];
                        $rowData["poiName"] = $row['poiName'];
                        $rowData["street"] = $row['street'];
                        $rowData["houseNumber"] = $row['houseNumber'];
                        $rowData["houseNumberAddition"] = $row['houseNumberAddition'];
                        $rowData["city"] = $row['city'];
                        $rowData["postalCode"] = $row['postalCode'];
                        $rowData["country"] = $row['country'];
                        $rowData["phoneNumber"] = $row['phoneNumber'];
                        $rowData["contactPerson"] = $row['contactPerson'];
                        $rowData["openTime"] = $row['openTime'];
                        $rowData["closeTime"] = $row['closeTime'];
                        $rowData["remark"] = $row['remark'];
                        $rowData["active"] = $row['active'];
                        $rowData["labelId"] = $row['labelId'];
                        array_push($returnData, $rowData);
                    }
                }
            }
            $poi->free_result();
            $searchPoiQuery->close();
            $body = $response->getBody();
            $body->write(json_encode($returnData));
            return $response->withBody($body)->withStatus(200);
        } else {
            $body = $response->getBody();
            $body->write('Query fault on select');
            return $response->withBody($body)->withStatus(500);
        }
    }

    public function getCompanyPois()
    {
        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());
        if ($jsonRequest->companyId == null) {
            $body = $response->getBody();
            $body->write('No companyId given');
            return $response->withBody($body)->withStatus(500);
        }
        if ($searchCompanyPois = $conn->prepare('SELECT * FROM pois WHERE companyId=?  ')) {
            $var1 = $jsonRequest->companyId;
            $searchCompanyPois->bind_param('s', $var1);
            $searchCompanyPois->execute();
            $result = $searchCompanyPois->get_result();
            $rowCount = mysqli_num_rows($result);
            $returnData = [];
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $result->fetch_assoc()) {
                        $rowData = [];
                        $rowData["poiId"] = $row['poiId'];
                        $rowData["companyId"] = $row['companyId'];
                        $rowData["poiName"] = $row['poiName'];
                        $rowData["street"] = $row['street'];
                        $rowData["houseNumber"] = $row['houseNumber'];
                        $rowData["houseNumberAddition"] = $row['houseNumberAddition'];
                        $rowData["city"] = $row['city'];
                        $rowData["postalCode"] = $row['postalCode'];
                        $rowData["country"] = $row['country'];
                        $rowData["phoneNumber"] = $row['phoneNumber'];
                        $rowData["contactPerson"] = $row['contactPerson'];
                        $rowData["openTime"] = $row['openTime'];
                        $rowData["closeTime"] = $row['closeTime'];
                        $rowData["remark"] = $row['remark'];
                        $rowData["active"] = $row['active'];
                        $rowData["labelId"] = $row['labelId'];
                        array_push($returnData, $rowData);
                    }
                }
            }
            $result->free_result();
            $searchCompanyPois->close();
            $body = $response->getBody();
            $body->write(json_encode($returnData));
            return $response->withBody($body)->withStatus(200);
        } else {
            $body = $response->getBody();
            $body->write('Query fault on select');
            return $response->withBody($body)->withStatus(500);
        }
    }


}
