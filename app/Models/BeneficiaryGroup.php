<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryGroup extends Model
{
    protected $connection = 'gwdata';
    protected $table = 'group_beneficiary';
}
