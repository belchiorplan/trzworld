<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfectedReported extends Model
{

    protected $fillable = ['infected_survivor_id', 'reporting_survivor_id'];
}
