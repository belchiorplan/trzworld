<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SurvivorInventory extends Model
{
    use HasFactory;

    protected $fillable = ['survivor_id', 'item_id', 'quantity'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function item(): HasOne
    {
        return $this->hasOne(InventoryItem::class, 'id', 'item_id');
    }

    public function survivor(): HasOne
    {
        return $this->hasOne(Survivor::class, 'id', 'survivor_id');
    }
}
