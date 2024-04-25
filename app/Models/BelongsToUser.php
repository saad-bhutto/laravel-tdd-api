<?php

namespace App\Models;

use App\Models\User;

trait BelongsToUser
{
    /**
     * Relation with user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
