<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    protected $fillable = ['name', 'type', 'requirements'];

    protected $casts = [
        'requirements' => 'array'
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)
            ->withPivot('status', 'error_message', 'published_at')
            ->withTimestamps();
    }

    public function validateContent(string $content): bool
    {
        $requirements = $this->requirements;
        
        if ($this->type === 'twitter' && strlen($content) > 280) {
            return false;
        }
        
        if ($this->type === 'linkedin' && strlen($content) > 3000) {
            return false;
        }
        
        // Add other platform-specific validations
        return true;
    }
}