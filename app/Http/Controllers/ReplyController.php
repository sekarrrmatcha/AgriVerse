<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $thread->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('status', 'Balasan terkirim!');
    }
}
