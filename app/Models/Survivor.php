<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survivor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'age', 'gender_id', 'latitude', 'longitude', 'is_infected'];

    protected $hidden = ['id', 'created_at', 'updated_at'];
}
