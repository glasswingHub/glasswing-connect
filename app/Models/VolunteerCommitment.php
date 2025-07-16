<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerCommitment extends Model
{
    protected $connection = 'gwdata';
    protected $table = 'commitment_voluntary';
}
