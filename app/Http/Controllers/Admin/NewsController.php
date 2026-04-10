<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = Post::latest('id')->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . time(),
            'is_published' => true,
            'published_at' => now(),
            'user_id' => auth()->id() ?? \App\Models\User::first()?->id ?? 1,
        ]);

        return redirect()->route('admin.news.index')->with('success', 'News published successfully.');
    }

    public function destroy(Post $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully.');
    }
}
