<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'player_id',
        'character_id',
        'game',
        'emblem_path',
        'background_path',
        'last_played_at',
    ];

    protected $dates = [
        'last_played_at',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function completions()
    {
        return $this->hasMany(Completion::class);
    }
}
