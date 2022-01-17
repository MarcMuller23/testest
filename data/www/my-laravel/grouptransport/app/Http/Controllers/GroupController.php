<?php

namespace App\Http\Controllers;

use App\Http\traits\IdCreationTrait;
use App\Http\traits\RoutingTrait;
use App\Models\GroupModel;
use App\Models\Participant_GroupModel;
use App\Models\ParticipantModel;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function createGroup(Request $request)
    {
        $validateData = $request->validate([
            'companyId' => 'string|required',
            'labelId' => '',
            'groupName' => '',
            'route' => '',
            'periodStart' => '',
            'periodEnd' => '',
            'participants' => ''
        ]);

        //Create group with given parameters
        $newGroup = new GroupModel([
            'groupId' => IdCreationTrait::requestTokenId(),
            'companyId' => $validateData['companyId'],
            'labelId' => $validateData['labelId'],
            'groupName' => $validateData['groupName'],
            'route' => $validateData['route'],
            'periodStart' => $validateData['periodStart'],
            'periodEnd' => $validateData['periodEnd'],
            'active' => 1,
            'created_at' => date("Y-m-d h:i:s", time())
        ]);

        $newGroup->save();
        if (key_exists('participants', $validateData) && $validateData['participants'] != null) {

            foreach ($validateData['participants'] as $participant) {
                $participant_group = new Participant_GroupModel([
                    'groupId' => $newGroup['groupId'],
                    'participantId' => $participant['participantId'],
                    'fromAddressId' => $participant['fromAddressId'],
                    'toAddressId' => $participant['toAddressId'],
                    'active' => 1,
                    'created_at' => date("Y-m-d h:i:s", time())
                ]);
                $participant_group->save();
            }
        } else {
            return response()->json('Group was created without participants', 200);
        }
        return response()->json('Group was created with participants', 200);
    }

    public function updateGroup(Request $request)
    {
        $validateData = $request->validate([
            'companyId' => 'string|required',
            'labelId' => '',
            'groupName' => '',
            'route' => '',
            'periodStart' => '',
            'periodEnd' => ''
        ]);

        //Create group with given parameters
        $newGroup = new GroupModel([
            'groupId' => IdCreationTrait::requestTokenId(),
            'companyId' => $validateData['companyId'],
            'labelId' => $validateData['labelId'],
            'groupName' => $validateData['groupName'],
            'route' => $validateData['route'],
            'periodStart' => $validateData['periodStart'],
            'periodEnd' => $validateData['periodEnd'],
            'active' => 1,
            'updated_at' => date("Y-m-d h:i:s", time())
        ]);
        $group = GroupModel::Where('groupId', '=', $validateData['groupId'])->where('companyId', '=', $validateData['companyId'])->first();
        if ($group == null) {
            return response()->json('Group does not exist', 404);

        } else {

            $newGroup->save();
            return response()->json('Group has been updated', 200);
        }
    }

    public function addParticipant(Request $request)
    {
        $validateData = $request->validate([
            'groupId' => 'string|required',
            'participantId' => 'string|required',
            'fromAddressId' => 'string|required',
            'toAddressId' => 'string|required'
        ]);

        $existingCombo = Participant_GroupModel::Where('groupId', '=', $validateData['groupId'])->Where('participantId', '=', $validateData['participantId'])->get();

        //If participant is not part of the group
        if (count($existingCombo) == 0) {
            $participant_group = new Participant_GroupModel([
                'participantId' => $validateData['participantId'],
                'groupId' => $validateData['groupId'],
                'fromAddressId' => $validateData['fromAddressId'],
                'toAddressId' => $validateData['toAddressId'],
                'active' => 1]);
            $participant_group->save();

        } //The given participant was already connected
        else {
            return response()->json('Already part of this group');
        }
        return response()->json('Added to the group
       ');
    }
    public function removeParticipant(Request $request)
    {$validateData = $request->validate([
        'groupId' => 'string|required',
        'participantId' => 'string|required',
        'companyId' => 'string|required',
    ]);

        //Find the existing connection and delete
        $existingCombo = Participant_GroupModel::where('groupId', '=', $validateData['groupId'])->Where('participantId', '=', $validateData['participantId'])->first();
        $participant = ParticipantModel::where('participantId', '=', $validateData['participantId']);
        //Check if the combination of participants and group exists
        if ($existingCombo != null && $participant != null)
        {
            //If the connection exists, delete the connection
            $existingCombo->delete();
            return response()->json('Removed');
        }
        //The connection between participant and group was not found
        return response()->json("participant not found", 404);
    }

    public function getGroup(Request $request)
    {
        $validateData = $request->validate([
            'groupId' => 'string|required',
            'companyId' => 'string|required',
        ]);
        $group = GroupModel::where('groupId', '=', $validateData['groupId'])->Where('companyId', '=', $validateData['companyId'])->first();
        //If a group could be constructed form database
        if ($group != false)
        {
            //return response
            return response()->json($group, 200);
        } else
        {
            //Group could not be constructed
            return response()->json('group not found', 404);
        }
        //todo:participants er bij
    }

    public function getCompanyGroups(Request $request)
    {
        $validateData = $request->validate([
        'companyId' => 'string|required'
    ]);
        $groups = GroupModel::where('companyId', '=', $validateData['companyId'])->get()->orderBy('groupName');
        //If a group could be constructed form database
        if ($groups != false)
        {
            //return response
            return response()->json($groups, 200);
        } else
        {
            //Group could not be constructed
            return response()->json('no groups found', 404);
        }
        //todo:participants er bij
    }


    public function generateRoute()
    {
        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());

        $route = RoutingTrait::generateManualRoute($jsonRequest->destinations, $jsonRequest->groupId);
        if ($updateRouteQuery = $conn->prepare('UPDATE `groups` SET `route`=?,updated_at=? WHERE `groupId`=?')) {

            $var1 = json_encode($route);
            $var2 = $jsonRequest->groupId;
            $var3 = date("Y-m-d h:i:s", time());;
            $updateRouteQuery->bind_param('sss', $var1, $var3, $var2);
            $updateRouteQuery->execute();
        }
        $body = $response->getBody();
        $body->write('route has been generated successfully' . json_encode($route));
        return $response->withStatus(200);
        //TODO:
    }

    public function calculateDriverDeparture(Request $request)
    {
        $conn = connection::getConnection();
        $jsonRequest = json_decode($request->getBody());

        $time = routingTrait::calculateDriverDeparture($jsonRequest->address, $jsonRequest->groupId);

//TODO: response
        $body = $response->getBody();
        $body->write(json_encode($time));
        return $response->withStatus(200);
    }

    public function fileUpload(Request $request)
    {
        //TODO:response and clean
        $jsonRequest = json_decode($request->getBody());
        $url = $jsonRequest->url;
        $fileName = $jsonRequest->fileName;
        $type = $jsonRequest->type;
        $label = $jsonRequest->label;
        $category = $jsonRequest->category;
        $companyId = $jsonRequest->companyId;

        $content = groupTrait::downloadContent($url, $fileName);

        $hash = hash_file('md5', $content);

        if ($jsonRequest->hash == $hash) {
            switch ($category) {
//            case 'schoolList':
//                $participantList = groupTrait::convertFileSchoolList($content, $type);
//
//                $body = $response->getBody();
//                $body->write(json_encode($participantList));
//                return $response->withStatus(200)->withBody($body);

                case 'participantList':
                    $participantList = groupTrait::convertFileParticipantList($content, $type);
                    //upload the data
                    //pois maken
                    //Methode()
                    array_shift($participantList);

                    if (groupTrait::createPois($participantList, $label, $companyId) == false) {

                        $body = $response->getBody();
                        $body->write('Internal server error in grouptrait->createPoi');
                        return $response->withStatus(500)->withBody($body);
                    }

                    //participants met addressen maken en daar pois aan koppelen
                    //methode()
                    $var1 = groupTrait::createParticipantsWithAddresses($participantList, $label, $companyId);
                    print_r($var1);
                    if ($var1 == false) {
                        $body = $response->getBody();
                        $body->write('Internal server error in grouptrait->createParticipant');
                        return $response->withStatus(500)->withBody($body);
                    } else {
                        $body = $response->getBody();
                        $body->write('Uploaded');
                        return $response->withStatus(200)->withBody($body);
                    }
            }
        }
        $body = $response->getBody();
        $body->write('Corrupted file');
        return $response->withStatus(500)->withBody($body);
    }

    public function createGroupWithLabel(Request $request)
    {
        //TODO: response
        $jsonRequest = json_decode($request->getBody());
        $labelId = $jsonRequest->label;
        $participantArray = groupTrait::getParticipantsWithLabel($labelId);
        //People linked to poi
        $arrangedParticipant = groupTrait::createArrangedListOfParticipants($participantArray);
        //divide into groups

        $body = $response->getBody();
        $body->write(json_encode($arrangedParticipant));
        return $response->withBody($body)->withStatus(500);
    }

    public function createGroupWithLabelResponse(Request $request)
    {
        $jsonRequest = json_decode($request->getBody(), 1);
        if (groupTrait::createFinalGroupsForSystem($jsonRequest)) {

            $body = $response->getBody();
            $body->write('Created');
            return $response->withBody($body)->withStatus(500);
        }
        $body = $response->getBody();
        $body->write('Failed');
        return $response->withBody($body)->withStatus(500);
    }
}
