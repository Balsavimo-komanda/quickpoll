<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    protected $fillable = [
        'poll_id',
        'text'
    ];

    /**
     * An option belongs to a specific poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * An option can have many votes cast for it.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
