<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $connection = 'gwdata';
    protected $table = 'beneficiaries';
}
