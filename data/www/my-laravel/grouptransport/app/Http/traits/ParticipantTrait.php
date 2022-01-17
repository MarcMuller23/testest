<?php


namespace App\Http\traits;


use App\Http\traits\idCreationTrait;
use Cake\Validation\Validator;
use stdClass;

trait ParticipantTrait
{
    public static function getParticipantObjectPost($jsonRequest)
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
            $newParticipantId = idCreationTrait::requestTokenId();
            //Create address variable
            $participant = new stdClass();
            $participant->participantId = $newParticipantId;
            $participant->name = $jsonRequest->name;
            $participant->phoneNumber = $jsonRequest->phoneNumber;
            $participant->companyId = $jsonRequest->companyId;
            $participant->active = 1;
            $participant->labelId = $jsonRequest->labelId;
            $participant->participantNumber = $jsonRequest->participantNumber;
            $participant->phoneNumber2 = $jsonRequest->phoneNumber2;
            $participant->email = $jsonRequest->email;
            $participant->birthDate = $jsonRequest->birthDate;
            $participant->gender = $jsonRequest->gender;
            $participant->mon_arrival_time = $jsonRequest->mon_arrival_time;
            $participant->tue_arrival_time = $jsonRequest->tue_arrival_time;
            $participant->wed_arrival_time = $jsonRequest->wed_arrival_time;
            $participant->thu_arrival_time = $jsonRequest->thu_arrival_time;
            $participant->fri_arrival_time = $jsonRequest->fri_arrival_time;
            $participant->sat_arrival_time = $jsonRequest->sat_arrival_time;
            $participant->sun_arrival_time = $jsonRequest->sun_arrival_time;
            $participant->mon_departure_time = $jsonRequest->mon_departure_time;
            $participant->tue_departure_time = $jsonRequest->tue_departure_time;
            $participant->wed_departure_time = $jsonRequest->wed_departure_time;
            $participant->thu_departure_time = $jsonRequest->thu_departure_time;
            $participant->fri_departure_time = $jsonRequest->fri_departure_time;
            $participant->sat_departure_time = $jsonRequest->sat_departure_time;
            $participant->sun_departure_time = $jsonRequest->sun_departure_time;
            return $participant;
        }
    }

    public static function getParticipantObjectPut($jsonRequest)
    {
        $validator = new Validator();
        $validator
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('participantId', 'This field is required');

        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            $newParticipantId = $jsonRequest->participantId;
            //Create address variable
            $participant = new stdClass();
            $participant->participantId = $newParticipantId;
            $participant->name = $jsonRequest->name;
            $participant->phoneNumber = $jsonRequest->phoneNumber;
            $participant->companyId = $jsonRequest->companyId;
            $participant->active = 1;
            $participant->labelId = $jsonRequest->labelId;
            $participant->participantNumber = $jsonRequest->participantNumber;
            $participant->phoneNumber2 = $jsonRequest->phoneNumber2;
            $participant->email = $jsonRequest->email;
            $participant->birthDate = $jsonRequest->birthDate;
            $participant->gender = $jsonRequest->gender;
            $participant->monArrivalTime = $jsonRequest->mon_arrival_time;
            $participant->tueArrivalTime = $jsonRequest->tue_arrival_time;
            $participant->wedArrivalTime = $jsonRequest->wed_arrival_time;
            $participant->thuArrivalTime = $jsonRequest->thu_arrival_time;
            $participant->friArrivalTime = $jsonRequest->fri_arrival_time;
            $participant->satArrivalTime = $jsonRequest->sat_arrival_time;
            $participant->sunArrivalTime = $jsonRequest->sun_arrival_time;
            $participant->monDepartureTime = $jsonRequest->mon_departure_time;
            $participant->tueDepartureTime = $jsonRequest->tue_departure_time;
            $participant->wedDepartureTime = $jsonRequest->wed_departure_time;
            $participant->thuDepartureTime = $jsonRequest->thu_departure_time;
            $participant->friDepartureTime = $jsonRequest->fri_departure_time;
            $participant->satDepartureTime = $jsonRequest->sat_departure_time;
            $participant->sunDepartureTime = $jsonRequest->sun_departure_time;
            return $participant;
        }
    }

}
