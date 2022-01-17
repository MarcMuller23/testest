<?php

namespace App\Http\traits;

use App\Http\traits\coordinateTrait;
use App\Http\traits\idCreationTrait;
use Cake\Validation\Validator;
use stdClass;

trait PoiTrait
{
    public static function getPoiObjectPost($jsonRequest)
    {
        $validator = new Validator();
        $validator
            ->notEmptyArray('street', 'This field is required')
            ->notEmptyArray('houseNumber', 'This field is required')
            ->notEmptyArray('city', 'This field is required')
            ->notEmptyArray('postalCode', 'This field is required')
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('country', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            $newParticipantId = idCreationTrait::requestTokenId();
            //Create address variable
            $poi = new stdClass();
            $poi->poiId = $newParticipantId;
            $poi->companyId = $jsonRequest->companyId;
            $poi->poiName = $jsonRequest->poiName;
            $poi->street = $jsonRequest->street;
            $poi->houseNumber = $jsonRequest->houseNumber;
            $poi->houseNumberAddition = $jsonRequest->houseNumberAddition;
            $poi->city = $jsonRequest->city;
            $poi->postalCode = $jsonRequest->postalCode;
            $poi->country = $jsonRequest->country;
            $poi->phoneNumber = $jsonRequest->phoneNumber;
            $poi->contactPerson = $jsonRequest->contactPerson;
            $poi->openTime = $jsonRequest->openTime;
            $poi->closeTime = $jsonRequest->closeTime;
            $poi->remark = $jsonRequest->remark;
            $poi->active = 1;
            $poi->labelId = $jsonRequest->labelId;
            $coordinates = coordinateTrait::getCoordinates($poi->postalCode, $poi->houseNumber, $poi->houseNumberAddition ?? null);

            if ($coordinates == false) {
                $coordinates = coordinateTrait::getCoordinates($poi->postalCode, $poi->houseNumber, null);
                if ($coordinates == false) {
                    return false;
                }

            }
            $poi->latitude = $coordinates['lat'];
            $poi->longitude = $coordinates['lng'];
            return $poi;
        }
    }

    public static function getPoiObjectPut($jsonRequest)
    {

        $validator = new Validator();
        $validator
            ->notEmptyArray('street', 'This field is required')
            ->notEmptyArray('houseNumber', 'This field is required')
            ->notEmptyArray('city', 'This field is required')
            ->notEmptyArray('postalCode', 'This field is required')
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('poiId', 'This field is required')
            ->notEmptyArray('country', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            $poi = new stdClass();
            $poi->poiId = $jsonRequest->poiId;
            $poi->companyId = $jsonRequest->companyId;
            $poi->poiName = $jsonRequest->poiName;
            $poi->street = $jsonRequest->street;
            $poi->houseNumber = $jsonRequest->houseNumber;
            $poi->houseNumberAddition = $jsonRequest->houseNumberAddition;
            $poi->city = $jsonRequest->city;
            $poi->postalCode = $jsonRequest->postalCode;
            $poi->country = $jsonRequest->country;
            $poi->phoneNumber = $jsonRequest->phoneNumber;
            $poi->contactPerson = $jsonRequest->contactPerson;
            $poi->openTime = $jsonRequest->openTime;
            $poi->closeTime = $jsonRequest->closeTime;
            $poi->remark = $jsonRequest->remark;
            $poi->active = 1;
            $poi->labelId = $jsonRequest->labelId;
            $coordinates = coordinateTrait::getCoordinates($poi->postalCode, $poi->houseNumber, $poi->houseNumberAddition ?? null);

            if ($coordinates == false) {
                $coordinates = coordinateTrait::getCoordinates($poi->postalCode, $poi->houseNumber, null);
                if ($coordinates == false) {
                    return false;
                }

            }
            $poi->latitude = $coordinates['lat'];
            $poi->longitude = $coordinates['lng'];
            return $poi;
        }
    }

}
