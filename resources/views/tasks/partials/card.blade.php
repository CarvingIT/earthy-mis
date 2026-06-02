<div class="task-card p-3.5 bg-white relative transition-all duration-350 border border-slate-100 rounded-xl shadow-sm hover:-translate-y-0.5 hover:shadow hover:border-slate-300/40">

    <div class="space-y-1.5">
        <!-- Priority and Options Row -->
        <div class="flex justify-between items-center gap-2">
            <!-- Priority Badge (Standardized and Compact) -->
            @if($task->priority === 'high')
                <span class="px-2 py-0.5 text-[0.68rem] font-bold tracking-wider uppercase rounded bg-rose-50 text-rose-600 border border-rose-100/50 shadow-sm">High</span>
            @elseif($task->priority === 'medium')
                <span class="px-2 py-0.5 text-[0.68rem] font-bold tracking-wider uppercase rounded bg-amber-50 text-amber-600 border border-amber-100/50 shadow-sm">Medium</span>
            @else
                <span class="px-2 py-0.5 text-[0.68rem] font-bold tracking-wider uppercase rounded bg-slate-50 text-slate-500 border border-slate-200/50 shadow-sm">Low</span>
            @endif

            <!-- Assignee Circle (Compact and Clean) -->
            @if($task->assignedUser)
                @php
                    $names = explode(' ', $task->assignedUser->name);
                    $initials = '';
                    foreach ($names as $name) {
                        $initials .= strtoupper(substr($name, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
                @endphp
                <span class="w-6.5 h-6.5 rounded-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-650 text-white font-bold text-[0.68rem] shadow-sm border border-white cursor-help" 
                      title="Responsibility: {{ $task->assignedUser->name }} ({{ $task->assignedUser->email }})">
                    {{ $initials }}
                </span>
            @else
                <span class="w-6.5 h-6.5 rounded-full flex items-center justify-center bg-slate-50 border border-slate-200/60 text-slate-400 font-bold text-[0.68rem] cursor-help" 
                      title="Unassigned / System Task">
                    --
                </span>
            @endif
        </div>

        <!-- Task Title (Standardized text-sm & leading-snug) -->
        <h4 class="font-bold text-slate-800 text-sm leading-snug hover:text-emerald-600 transition-colors cursor-pointer"
            @click="openDrawerForEdit({{ $task->toJson() }})">
            {{ $task->title }}
        </h4>

        <!-- Description (Standardized text-xs & line-clamp) -->
        @if($task->description)
            <p class="text-xs text-slate-400 font-medium leading-normal line-clamp-2 pb-0.5">
                {{ $task->description }}
            </p>
        @endif
    </div>

    <!-- Bottom Metadata Row (Due date & Actions) -->
    <div class="border-t border-slate-100 pt-2.5 flex justify-between items-center mt-2.5">
        <!-- Due Date Tag -->
        <div>
            @if($task->due_date)
                @php
                    $isOverdue = $task->due_date->isPast() && $task->status !== 'completed' && !$task->due_date->isToday();
                    $isToday = $task->due_date->isToday() && $task->status !== 'completed';
                @endphp
                
                @if($isOverdue)
                    <div class="flex items-center gap-1.5 text-xs text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-100/50">
                        <span class="pulsate-dot"></span>
                        <span>{{ $task->due_date->format('M d') }}</span>
                    </div>
                @elseif($isToday)
                    <div class="flex items-center gap-1.5 text-xs text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-100/50">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-sm" style="box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.2);"></span>
                        <span>Today</span>
                    </div>
                @else
                    <div class="flex items-center gap-1 text-[0.68rem] text-slate-500 font-bold bg-slate-50 px-2 py-0.5 rounded border border-slate-100/60">
                        <svg xmlns="http://www.w3.org/2050/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $task->due_date->format('M d') }}</span>
                    </div>
                @endif
            @else
                <span class="text-[0.68rem] text-slate-400 font-bold tracking-wider uppercase bg-slate-50 px-2 py-0.5 rounded border border-slate-100/60">No Deadline</span>
            @endif
        </div>

        <!-- Shift & Control Actions (Glassmorphic colored buttons) -->
        <div class="flex items-center gap-1.5">
            <!-- Shift Backward Action (Sky Blue) -->
            @if($column === 'in_progress')
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline m-0">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="todo">
                    <button type="submit" class="p-1 rounded-lg text-sky-600 bg-sky-50 hover:bg-sky-100 hover:text-sky-700 border border-sky-100/60 hover:border-sky-200 transition shadow-sm flex items-center justify-center" title="Move back to To Do">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </form>
            @elseif($column === 'completed')
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline m-0">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="p-1 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-700 border border-amber-100/60 hover:border-amber-200 transition shadow-sm flex items-center justify-center" title="Move back to In Progress">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </form>
            @endif

            <!-- Edit Trigger (Indigo) -->
            <button type="button" 
                    @click="openDrawerForEdit({{ $task->toJson() }})" 
                    class="p-1 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 border border-indigo-100/60 hover:border-indigo-200 transition shadow-sm flex items-center justify-center" 
                    title="Edit Task Details">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>

            <!-- Shift Forward Action -->
            @if($column === 'todo')
                <!-- Start Progress (Amber) -->
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline m-0">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="p-1 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-705 border border-amber-100/60 hover:border-amber-200 transition shadow-sm flex items-center justify-center" title="Start Working (In Progress)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            @elseif($column === 'in_progress')
                <!-- Tick / Resolve Task (Emerald Green) -->
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline m-0">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="p-1 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-700 border border-emerald-100/60 hover:border-emerald-200 transition shadow-sm flex items-center justify-center" title="Resolve Task (Complete)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </form>
            @endif

            <!-- Delete Form (Crimson Red) -->
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline m-0" onsubmit="return confirm('Delete this operational task?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-1 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 border border-rose-100/60 hover:border-rose-200 transition shadow-sm flex items-center justify-center" title="Delete Task permanent">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
