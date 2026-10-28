<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'visitor_id'
    ];

    /**
     * A vote belongs to a poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * A vote belongs to a specific poll option.
     */
    public function option()
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }
}
