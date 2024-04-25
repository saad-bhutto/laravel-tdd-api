<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mod extends Model
{
    use HasFactory;
    use BelongsToUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'game_id',
    ];

    /**
     * Relation with game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Relation with user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
