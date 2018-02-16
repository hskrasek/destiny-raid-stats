<?php

namespace App;

use App\Destiny1\Activity;
use Illuminate\Database\Eloquent\Model;

class Completion extends Model
{
    protected $fillable = [
        'activity_hash',
        'character_id',
        'completed',
        'assists',
        'deaths',
        'kills',
        'kills_deaths_ratio',
        'kills_deaths_assists',
        'activity_duration_seconds',
        'player_count',
        'period',
    ];

    protected $dates = [
        'period',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function destiny1Activity()
    {
        return $this->belongsTo(Activity::class, 'activity_hash');
    }

    public function destiny2Activity()
    {
        return $this->belongsTo(\App\Destiny2\Activity::class, 'activity_hash');
    }

    public function activity()
    {
        return $this->destiny1Activity ?? $this->destiny2Activity;
    }
}
