<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'membership_id',
        'display_name',
    ];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }
}
