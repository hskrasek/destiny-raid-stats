<?php

namespace App\Destiny2;

use App\Completion;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $connection = 'destiny2';

    protected $table = 'DestinyActivityDefinition';

    protected $casts = [
        'json' => 'json',
    ];

    public function scopeByBungieId($query, $id)
    {
        return $query->whereId($id)->orWhere('id', $id - 4294967296);
    }

    public function completions()
    {
        return $this->hasMany(Completion::class, 'activity_hash');
    }
}
