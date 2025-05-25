<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessScheduledPosts extends Command
{
    protected $signature = 'posts:process-scheduled';
    protected $description = 'Process scheduled posts that are due for publication';

    public function handle()
    {
        $now = Carbon::now();

        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_time', '<=', $now)
            ->with('platforms')
            ->get();



        foreach ($posts as $post) {
            $this->info("Processing post #{$post->id}");

            foreach ($post->platforms as $platform) {
                // Mock publishing process
                $success = rand(0, 1); // Simulate random success/failure
                
                $post->platforms()->updateExistingPivot(
                    $platform->id,
                    ['status' => $success ? 'published' : 'failed']
                );

                $this->info("Published to {$platform->name}: " . ($success ? 'Success' : 'Failed'));
            }

            $post->status = 'published';
            $post->save();

        }

        $this->info('Finished processing scheduled posts');
    }
}