<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskCommentController extends Controller
{
    /**
     * Store a newly created comment / attachment in storage.
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip',
        ]);

        if (empty($request->comment) && !$request->hasFile('attachment')) {
            return back()->withErrors(['comment' => 'Please enter a comment or attach a file.']);
        }

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('task_attachments', 'public');
        }

        $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        return redirect()->back()->with('success', 'Comment / file added successfully.');
    }

    /**
     * Download the attachment.
     */
    public function download(TaskComment $comment)
    {
        if (!$comment->file_path || !Storage::disk('public')->exists($comment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($comment->file_path, $comment->file_name);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(TaskComment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($comment->file_path && Storage::disk('public')->exists($comment->file_path)) {
            Storage::disk('public')->delete($comment->file_path);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
