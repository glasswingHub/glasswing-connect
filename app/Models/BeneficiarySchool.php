<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiarySchool extends Model
{
    protected $connection = 'gwdata';
    protected $table = 'school_beneficiaries';
}
