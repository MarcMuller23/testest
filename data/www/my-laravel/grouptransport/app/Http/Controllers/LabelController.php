<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function createLabel(Request $request)
    {

        $jsonRequest = json_decode($request->getBody());
        //Getting the connection
        $conn = connection::getConnection();
        if ($jsonRequest->name == null or $jsonRequest->companyId == null) {
            $body = $response->getBody();
            $body->write('No name given');
            return $response->withBody($body)->withStatus(500);
        }
        if ($labelQuery = $conn->prepare('INSERT INTO labels(labelId,companyId,name,created_at) VALUES (?,?,?,?); ')) {

            $var1 = idCreationTrait::requestTokenId();
            $var2 = $jsonRequest->companyId;
            $var3 = $jsonRequest->name;
            $var4 = date("Y-m-d h:i:s", time());;
            $labelQuery->bind_param('ssss', $var1, $var2, $var3,$var4);
            $labelQuery->execute();
            $labelQuery->close();
            if ($searchParticipantsQuery = $conn->prepare('SELECT * FROM labels WHERE labelId=?')) {
                $searchParticipantsQuery->bind_param('s', $var1,);
                $searchParticipantsQuery->execute();
                $result = $searchParticipantsQuery->get_result();
                $rowCount = mysqli_num_rows($result);
                $returnData = [];
                if ($rowCount) {
                    for ($i = 0; $i < $rowCount; $i++) {
                        if ($row = $result->fetch_assoc()) {
                            $rowData = [];
                            $rowData["labelId"] = $row['labelId'];
                            $rowData["companyId"] = $row['companyId'];
                            $rowData["name"] = $row['name'];
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
            $body->write('Query fault on get');
            return $response->withBody($body)->withStatus(500);
        }
        $body = $response->getBody();
        $body->write('Query fault');
        return $response->withBody($body)->withStatus(500);
    }

    public function getLabel(Request $request)
    {

        $jsonRequest = json_decode($request->getBody());
        //Getting the connection
        $conn = connection::getConnection();
        if ($jsonRequest->name == null or $jsonRequest->companyId == null) {
            $body = $response->getBody();
            $body->write('No name given');
            return $response->withBody($body)->withStatus(500);
        }
        if ($searchParticipantsQuery = $conn->prepare('SELECT * FROM labels WHERE labelId=?')) {
            $var1=$jsonRequest->labelId;
            $searchParticipantsQuery->bind_param('s', $var1,);
            $searchParticipantsQuery->execute();
            $result = $searchParticipantsQuery->get_result();
            $rowCount = mysqli_num_rows($result);
            $returnData = [];
            if ($rowCount) {
                for ($i = 0; $i < $rowCount; $i++) {
                    if ($row = $result->fetch_assoc()) {
                        $rowData = [];
                        $rowData["labelId"] = $row['labelId'];
                        $rowData["companyId"] = $row['companyId'];
                        $rowData["name"] = $row['name'];
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
        $body->write('Query fault');
        return $response->withBody($body)->withStatus(500);
    }
}
