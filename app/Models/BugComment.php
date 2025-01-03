<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BugComment extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'user'
    ];

    protected $fillable = [
      'content'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (BugComment $bugComment) {
            $bugComment->user_id = Auth::id();
        });

        // Lors de la création d'un bug, on met à jour updated_at du projet
        static::created(function (BugComment $bugComment) {
            $bugComment->bug->project->touch();
        });
        static::updated(function (BugComment $bugComment) {
            $bugComment->bug->touch();
            $bugComment->bug->project->touch();
        });
    }


    public function bug(): BelongsTo
    {
        return $this->belongsTo(Bug::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}