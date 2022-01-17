<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addressId',
        'companyId',
        'poiId',
        'labelId',
        'addressName',
        'street',
        'houseNumber',
        'houseNumberAddition',
        'city',
        'postalCode',
        'country',
        'latitude',
        'longitude',
        'contactPerson',
        'phoneNumber',
        'active',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
    protected $primaryKey = 'addressId';

    protected $table = "addresses";

    public $incrementing = false;
}
