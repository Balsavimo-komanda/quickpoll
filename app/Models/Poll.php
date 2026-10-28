<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Poll extends Model
{
    protected $fillable = [
        'title'
    ];

    protected static function booted(): void
    {
        static::creating(function (Poll $poll) {
            do {
                $code = strtoupper(Str::random(6));
            } while (static::where('code', $code)->exists());

            $poll->code = $code;
        });
    }

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
