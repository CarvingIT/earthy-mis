<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('assignedUser')
            ->orderBy('due_date', 'asc')
            ->get();

        $todoTasks = $tasks->where('status', 'todo');
        $inProgressTasks = $tasks->where('status', 'in_progress');
        $completedTasks = $tasks->where('status', 'completed');

        // Fetch users to assign tasks
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        // Stats calculation
        $totalCount = $tasks->count();
        $completedCount = $completedTasks->count();
        $completionPercentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('tasks.index', compact(
            'todoTasks', 
            'inProgressTasks', 
            'completedTasks', 
            'users',
            'totalCount',
            'completedCount',
            'completionPercentage'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = auth()->id();

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Support simple quick-shifter state updates
        if ($request->has('status') && !$request->has('title')) {
            $validated = $request->validate([
                'status' => 'required|in:todo,in_progress,completed',
            ]);
            $task->update($validated);
            return redirect()->route('tasks.index')->with('success', 'Task status updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
