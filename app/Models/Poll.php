<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Poll extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
    ];

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    protected static function booted(): void
    {
        static::creating(function (Poll $poll) {
            do {
                $code = strtoupper(Str::random(6));
            } while (static::where('code', $code)->exists());

            $poll->code = $code;
        });
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
