<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasActivityLogs;
class Post extends Model
{
    use HasActivityLogs;
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_url',
        'scheduled_time',
        'status'
    ];

    protected $casts = [
        'scheduled_time' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_time', '<=', now())
            ->where('status', 'scheduled');
    }

    public function isReadyToPublish(): bool
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_time->isPast() &&
               $this->platforms->isNotEmpty();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            $post->logActivity(
                'post.created',
                'Created new post: ' . $post->title,
                ['scheduled_time' => $post->scheduled_time->toDateTimeString()]
            );
        });

        static::updated(function ($post) {
            $post->logActivity(
                'post.updated',
                'Updated post: ' . $post->title,
                ['changed_attributes' => $post->getDirty()]
            );
        });

        static::deleted(function ($post) {
            $post->logActivity(
                'post.deleted',
                'Deleted post: ' . $post->title
            );
        });
    }
}