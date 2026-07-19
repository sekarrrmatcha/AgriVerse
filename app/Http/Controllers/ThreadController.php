<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::with('user')->withCount('replies')->latest()->paginate(10);

        return view('forum.index', compact('threads'));
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $thread = $request->user()->threads()->create($validated);

        return redirect()->route('forum.show', $thread)->with('status', 'Thread berhasil dibuat!');
    }

    public function show(Thread $thread)
    {
        $thread->load('replies.user', 'user');

        return view('forum.show', compact('thread'));
    }
}
