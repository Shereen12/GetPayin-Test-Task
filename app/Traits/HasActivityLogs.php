<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasActivityLogs
{
    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function logActivity(string $eventType, string $description, ?array $properties = null): ActivityLog
    {
        return ActivityLogger::log($eventType, $description, $this, $properties);
    }
}