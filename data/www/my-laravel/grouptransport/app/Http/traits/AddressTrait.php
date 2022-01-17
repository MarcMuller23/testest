<?php

namespace App\Http\traits;

use App\Http\traits\coordinateTrait as coordinateTrait;
use App\Http\traits\idCreationTrait as idCreationTrait;
use Cake\Validation\Validator;
use stdClass;

trait AddressTrait
{
    public static function getAddressObjectPost($jsonRequest)
    {
        $validator = new Validator();
        $validator
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('street', 'This field is required')
            ->notEmptyArray('houseNumber', 'This field is required')
            ->notEmptyArray('city', 'This field is required')
            ->notEmptyArray('postalCode', 'This field is required')
            ->notEmptyArray('country', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            //Create address object
            $newAddressId = idCreationTrait::requestTokenId();
            $address = new stdClass();
            $address->addressId = $newAddressId;
            $address->addressName = $jsonRequest->addressName;
            $address->street = $jsonRequest->street;
            $address->houseNumber = $jsonRequest->houseNumber;
            $address->houseNumberAddition = $jsonRequest->houseNumberAddition;
            $address->city = $jsonRequest->city;
            $address->postalCode = $jsonRequest->postalCode;
            $address->country = $jsonRequest->country;
            $address->phoneNumber = $jsonRequest->phoneNumber;
            $address->contactPerson = $jsonRequest->contactPerson;
            $address->companyId = $jsonRequest->companyId;
            $address->poiId = $jsonRequest->poiId;
            $address->active = 1;
            $address->labelId = $jsonRequest->labelId;
            $address->status = $jsonRequest->status;
            $coordinates = coordinateTrait::getCoordinates($address->postalCode, $address->houseNumber, $address->houseNumberAddition ?? null);

            if ($coordinates == false) {
                $coordinates = coordinateTrait::getCoordinates($address->postalCode, $address->houseNumber, null);
                if ($coordinates == false) {
                    return false;
                }
            }
            $address->latitude = $coordinates['lat'];
            $address->longitude = $coordinates['lng'];
            $address->marge = $jsonRequest->marge;
            $address->maxTravelTime = $jsonRequest->maxTravelTime;
            $address->startDate = date("Y-m-d",strtotime($jsonRequest->startDate));
            $address->endDate = date("Y-m-d",strtotime($jsonRequest->endDate));
            $address->monday = $jsonRequest->monday;
            $address->tuesday = $jsonRequest->tuesday;
            $address->wednesday = $jsonRequest->wednesday;
            $address->thursday = $jsonRequest->thursday;
            $address->friday = $jsonRequest->friday;
            $address->saturday = $jsonRequest->saturday;
            $address->sunday = $jsonRequest->sunday;
            return $address;
        }
    }

    public static function getAddressObjectPut($jsonRequest)
    {

        $validator = new Validator();
        $validator
            ->notEmptyArray('street', 'This field is required')
            ->notEmptyArray('houseNumber', 'This field is required')
            ->notEmptyArray('city', 'This field is required')
            ->notEmptyArray('postalCode', 'This field is required')
            ->notEmptyArray('companyId', 'This field is required')
            ->notEmptyArray('addressId', 'This field is required');
        $errors = $validator->validate($jsonRequest);

        if ($errors) {
            return false;
        }
        {
            $jsonRequest = json_encode($jsonRequest);
            $jsonRequest = json_decode($jsonRequest);
            //Create address object
            $address = new stdClass();
            $address->addressId = $jsonRequest->addressId;
            $address->addressName = $jsonRequest->addressName;
            $address->street = $jsonRequest->street;
            $address->houseNumber = $jsonRequest->houseNumber;
            $address->houseNumberAddition = $jsonRequest->houseNumberAddition;
            $address->city = $jsonRequest->city;
            $address->postalCode = $jsonRequest->postalCode;
            $address->country = $jsonRequest->country;
            $address->phoneNumber = $jsonRequest->phoneNumber;
            $address->contactPerson = $jsonRequest->contactPerson;
            $address->companyId = $jsonRequest->companyId;
            $address->poiId = $jsonRequest->poiId;
            $address->active = 1;
            $address->labelId = $jsonRequest->labelId;
            $address->status = $jsonRequest->status;
            $address->participantId = $jsonRequest->participantId;
            $coordinates = coordinateTrait::getCoordinates($address->postalCode, $address->houseNumber, $address->houseNumberAddition ?? null);
            if ($coordinates == false) {
                $coordinates = coordinateTrait::getCoordinates($address->postalCode, $address->houseNumber, null);
                if ($coordinates == false) {
                    return false;
                }
            }
            $address->latitude = $coordinates['lat'];
            $address->longitude = $coordinates['lng'];
            $address->marge = $jsonRequest->marge;
            $address->maxTravelTime = $jsonRequest->maxTravelTime;
            $address->startDate = date("Y-m-d",strtotime($jsonRequest->startDate));
            $address->endDate = date("Y-m-d",strtotime($jsonRequest->endDate));
            $address->monday = $jsonRequest->monday;
            $address->tuesday = $jsonRequest->tuesday;
            $address->wednesday = $jsonRequest->wednesday;
            $address->thursday = $jsonRequest->thursday;
            $address->friday = $jsonRequest->friday;
            $address->saturday = $jsonRequest->saturday;
            $address->sunday = $jsonRequest->sunday;
            return $address;
        }
    }


}
