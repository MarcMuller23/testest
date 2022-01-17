<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupModel extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'groupId',
        'companyId',
        'labelId',
        'groupName',
        'route',
        'periodStart',
        'periodEnd',
        'active',
        'day',
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
    protected $primaryKey = 'groupId';

    protected $table = "groups";

    public $incrementing = false;
}
