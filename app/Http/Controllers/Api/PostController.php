<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $query = Post::with(['platforms', 'user'])
            ->where('user_id', auth()->id());

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('scheduled_time', Carbon::parse($request->date));
        }

        // Check daily post limit
        $todayPosts = Post::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->count();

        $posts = $query->latest('scheduled_time')->paginate(10);

        return response()->json([
            'data' => $posts,
            'meta' => [
                'total_posts' => $posts->total(),
                'daily_limit_remaining' => max(0, 10 - $todayPosts),
                'current_time' => '2025-05-22 05:48:07'
            ]
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(PostRequest $request)
    {
        // Check daily post limit
        $todayPosts = Post::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayPosts >= 10) {
            return response()->json([
                'message' => 'Daily post limit (10) exceeded'
            ], 422);
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imagePath ? Storage::url($imagePath) : null,
            'scheduled_time' => Carbon::parse($request->scheduled_time),
            'status' => $request->draft ? 'draft' : 'scheduled'
        ]);

        if ($request->platform_ids) {
            $post->platforms()->attach($request->platform_ids);
        }

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post->load('platforms')
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return response()->json([
            'data' => $post->load('platforms'),
            'meta' => [
                'current_time' => '2025-05-22 05:48:07'
            ]
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Post $post, Request $request)
    {
        $this->authorize('update', $post);

        if ($post->status === 'published') {
            return response()->json([
                'message' => 'Published posts cannot be updated'
            ], 422);
        }

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image_url) {
                Storage::delete(str_replace('/storage/', 'public/', $post->image_url));
            }
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->image_url = Storage::url($imagePath);
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'scheduled_time' => Carbon::parse($request->scheduled_time),
            'status' => $request->draft ? 'draft' : 'scheduled'
        ]);

        if ($request->has('platform_ids')) {
            $post->platforms()->sync($request->platform_ids);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post->load('platforms')
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        // Delete associated image if exists
        if ($post->image_url) {
            Storage::delete(str_replace('/storage/', 'public/', $post->image_url));
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }

    /**
     * Display a listing of scheduled posts.
     */
    public function scheduledPosts(Request $request)
    {
        $posts = Post::with(['platforms', 'user'])
            ->where('user_id', auth()->id())
            ->where('status', 'scheduled')->get();
            
        return response()->json([
            'data' => $posts
        ]);
    }
}