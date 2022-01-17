<?php

namespace App\Http\Controllers;

use App\Http\traits\addressTrait;
use App\Http\traits\coordinateTrait;
use App\Http\traits\idCreationTrait;
use App\Models\AddressModel;
use App\Models\Participant_AddressModel;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function addAddress(Request $request)
    {

        $validateData = $request->validate([
            'companyId' => 'string|required',
            'poiId' => '',
            'labelId' => '',
            'addressName' => '',
            'street' => 'string|required',
            'houseNumber' => 'string|required',
            'houseNumberAddition' => '',
            'city' => 'string|required',
            'postalCode' => 'string|required',
            'country' => '',
            'latitude' => '',
            'longitude' => '',
            'contactPerson' => '',
            'phoneNumber' => '',
            'active' => '',
            'status' => '',
            'marge' => '',
            'maxTravelTime' => '',
            'startDate' => '',
            'endDate' => '',
            'monday' => '',
            'tuesday' => '',
            'wednesday' => '',
            'thursday' => '',
            'friday' => '',
            'saturday' => '',
            'sunday' => ''
        ]);

        $coordinates = CoordinateTrait::getCoordinates($validateData['postalCode'], $validateData['houseNumber'], $validateData['houseNumberAddition'] ?? null);
        if ($coordinates == false) {
            $coordinates = CoordinateTrait::getCoordinates($validateData['postalCode'], $validateData['houseNumber'], null);
        }

        $address = new addressModel([
            'addressId' => IdCreationTrait::requestTokenId(),
            'companyId' => $validateData['companyId'],
            'poiId' => $validateData['poiId'],
            'labelId' => $validateData['labelId'],
            'addressName' => $validateData['addressName'],
            'street' => $validateData['street'],
            'houseNumber' => $validateData['houseNumber'],
            'houseNumberAddition' => $validateData['houseNumberAddition'],
            'city' => $validateData['city'],
            'postalCode' => $validateData['postalCode'],
            'country' => $validateData['country'],
            'latitude' => $coordinates['lat'] ?? null,
            'longitude' => $coordinates['lng'] ?? null,
            'contactPerson' => $validateData['contactPerson'],
            'phoneNumber' => $validateData['phoneNumber'],
            'active' => $validateData['active'],
            'status' => $validateData['status'],
            'created_at' => date("Y-m-d h:i:s", time()),
            'updated_at' => date("Y-m-d h:i:s", time())
        ]);
        $address->save();

        //Check address object
        if ($address != false) {
            //Create the connection record to connect address to participant
            $connection = new Participant_AddressModel([
                'participantId' => $validateData['participantId'],
                'addressId' => $address['addressId'],
                'marge' => $address['addressId'],
                'maxTravelTime' => $address['addressId'],
                'startDate' => $address['addressId'],
                'endDate' => $address['addressId'],
                'monday' => $address['addressId'],
                'tuesday' => $address['addressId'],
                'wednesday' => $address['addressId'],
                'thursday' => $address['addressId'],
                'friday' => $address['addressId'],
                'satruday' => $address['addressId'],
                'sunday' => $address['addressId'],
                'active' => 1,
                'created_at' => date("Y-m-d h:i:s", time())
            ]);
            $connection->save();
            return response()->json('Succesfully added');
        } else { //Check if query is valid
            return response()->json('Internal server error', 500);
        }
    }

    public function updateAddress(Request $request)
    {
        $validateData = $request->validate([
            'addressId' => 'string|required',
            'companyId' => 'string|required',
            'poiId' => '',
            'labelId' => '',
            'addressName' => '',
            'street' => 'string|required',
            'houseNumber' => 'string|required',
            'houseNumberAddition' => '',
            'city' => 'string|required',
            'postalCode' => 'string|required',
            'country' => '',
            'latitude' => '',
            'longitude' => '',
            'contactPerson' => '',
            'phoneNumber' => '',
            'active' => '',
            'status' => '',
            'marge' => '',
            'maxTravelTime' => '',
            'startDate' => '',
            'endDate' => '',
            'monday' => '',
            'tuesday' => '',
            'wednesday' => '',
            'thursday' => '',
            'friday' => '',
            'saturday' => '',
            'sunday' => ''
        ]);
        //Creating address in database
        $coordinates = CoordinateTrait::getCoordinates($validateData['postalCode'], $validateData['houseNumber'], $validateData['houseNumberAddition'] ?? null);
        if ($coordinates == false) {
            $coordinates = CoordinateTrait::getCoordinates($validateData['postalCode'], $validateData['houseNumber'], null);
        }

        $address = new addressModel([
            'addressId' => $validateData['addressId'],
            'companyId' => $validateData['companyId'],
            'poiId' => $validateData['poiId'],
            'labelId' => $validateData['labelId'],
            'addressName' => $validateData['addressName'],
            'street' => $validateData['street'],
            'houseNumber' => $validateData['houseNumber'],
            'houseNumberAddition' => $validateData['houseNumberAddition'],
            'city' => $validateData['city'],
            'postalCode' => $validateData['postalCode'],
            'country' => $validateData['country'],
            'latitude' => $coordinates['lat'] ?? null,
            'longitude' => $coordinates['lng'] ?? null,
            'contactPerson' => $validateData['contactPerson'],
            'phoneNumber' => $validateData['phoneNumber'],
            'active' => $validateData['active'],
            'status' => $validateData['status'],
            'updated_at' => date("Y-m-d h:i:s", time())
        ]);
        $address->save();
    }

    public function disableAddress(Request $request)
    {
        $validateData = $request->validate([
            'addressId' => 'string|required',
            'companyId' => 'string|required',
        ]);

        //Retrieve the address
        $address = AddressModel::where('addressId', '=', $validateData['addressId'])->where('companyId', '=', $validateData['companyId'])->first();

        //Check is an address was retrieved
        if ($address != null) {
            //Disable address and save + return
            $address->active = 0;
            $address->updated_at = date("Y-m-d h:i:s", time());
            $address->save();
            return response()->json('Updated');
        } else {
            //The address could not be retrieved so doesn't exist
            return response()->json('Address not found', 404);
        }
    }

    public function enableAddress(Request $request)
    {
        $validateData = $request->validate([
            'addressId' => 'string|required',
            'companyId' => 'string|required',
        ]);

        //Retrieve the address
        $address = AddressModel::where('addressId', '=', $validateData['addressId'])->where('companyId', '=', $validateData['companyId'])->first();

        //Check is an address was retrieved
        if ($address != null) {
            //Disable address and save + return
            $address->active = 1;
            $address->updated_at = date("Y-m-d h:i:s", time());
            $address->save();
            return response()->json('Updated');
        } else {
            //The address could not be retrieved so doesn't exist
            return response()->json('Address not found', 404);
        }
    }
}
