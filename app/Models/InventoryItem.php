<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{

    protected $fillable = ['name', 'points'];

    protected $hidden = ['created_at', 'updated_at'];
}
