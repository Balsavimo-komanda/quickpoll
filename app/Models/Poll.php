<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'title'
    ];

    /**
     * A poll has many options.
     */
    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * A poll has many votes across all options.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
