<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurvivorInventory extends Model
{
    use HasFactory;

    protected $fillable = ['survivor_id', 'item_id'];

    protected $hidden = ['id', 'created_at', 'updated_at'];
}
