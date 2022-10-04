<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;
    protected $table = 'games';

    const Status = [
        'Active' => 1,
        'Inactive' => 2,
        'Finished' => 3
    ];

    const GameType = [
        'ThreeIsToThree' => 1,
        'TenIsToTen' => 2
    ];

    protected $fillable = [
        'type',
        'status'
    ];

    public function getGameTypeLabelAttribute()
    {
        return array_flip(self::GameType)[$this->attributes['type']];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_users')->withPivot('id', 'house_no', 'recent_move');
    }

}
