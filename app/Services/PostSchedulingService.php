<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostSchedulingService
{
    public function publishPost(Post $post)
    {
        try {
            foreach ($post->platforms as $platform) {
                $this->publishToPlatform($post, $platform);
            }

            $post->update(['status' => 'published']);
            
            Log::info('Post published successfully', [
                'post_id' => $post->id,
                'platforms' => $post->platforms->pluck('name')
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Post publishing failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    protected function publishToPlatform(Post $post, $platform)
    {
        // Mock publishing process
        try {
            // Simulate API call delay
            sleep(1);

            // Simulate random success/failure
            if (rand(0, 10) > 8) {
                throw new \Exception("Failed to publish to {$platform->name}");
            }

            $post->platforms()->updateExistingPivot($platform->id, [
                'status' => 'published',
                'published_at' => now()
            ]);

        } catch (\Exception $e) {
            $post->platforms()->updateExistingPivot($platform->id, [
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}