<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerPersonalReference extends Model
{
    protected $connection = 'gwdata';
    protected $table = 'referenciasPersonalesVoluntarios';
}
