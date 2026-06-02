<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Operations Control</p>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Task Pipelines</h2>
            </div>
            <p class="max-w-xl text-sm font-medium text-slate-500">Coordinate composting operations, JCB equipment schedules, logistics tasks, and user assignments.</p>
        </div>
    </x-slot>

    <style>
        * { box-sizing: border-box; }

        .task-shell {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f8f4 48%, #f8fafc 100%);
            min-height: calc(100vh - 4rem);
        }

        /* Redesigned to Soft Gutter styling like Linear/Jira */
        .kanban-col {
            border: 1px solid rgba(15, 23, 42, .05);
            background: rgba(241, 245, 249, 0.45);
            border-radius: 1.25rem;
            transition: background-color .18s ease, border-color .18s ease;
            position: relative;
            overflow: hidden;
            padding-top: 1.75rem !important;
        }

        .kanban-col:hover {
            background: rgba(241, 245, 249, 0.6);
            border-color: rgba(15, 23, 42, .08);
        }

        .kanban-col::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--col-accent);
        }

        /* Animations */
        .reveal-node {
            opacity: 0;
            transform: translate3d(0, 18px, 0);
            transition: opacity .48s ease, transform .48s ease;
            will-change: opacity, transform;
        }

        .reveal-node.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        /* Premium Primary button */
        .btn-task-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #059669, #10b981);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
            transition: transform .18s ease, box-shadow .18s ease;
            border: none;
            cursor: pointer;
        }

        .btn-task-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.3);
        }

        /* Glassy Backdrop Overlay */
        .modal-overlay {
            background-color: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(6px);
        }

        .alert-success {
            position: relative;
            padding: 14px 18px;
            border-radius: 1rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #047857;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.05);
            margin-bottom: 1.5rem;
        }

        .alert-close {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #047857;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
            font-weight: 700;
        }

        .pulsate-dot {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6);
            animation: pulsate 1.6s infinite;
        }

        @keyframes pulsate {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }
    </style>

    <div class="py-10 task-shell" x-data="taskBoard()" @keydown.escape.window="showDrawer = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert -->
            @if (session('success'))
                <div class="alert-success reveal-node is-visible" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button class="alert-close" onclick="this.parentElement.style.display='none'" aria-label="Dismiss">&times;</button>
                </div>
            @endif

            <!-- Dashboard Stats & Quick Bar -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between reveal-node" id="statsRow" style="--reveal-delay: 0ms">
                <!-- Progress Stat Card (Metric Card Style) -->
                <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-5 shadow-sm relative overflow-hidden max-w-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none"></div>
                    <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-emerald-500 to-teal-400"></div>

                    <div class="relative z-10 flex items-center justify-center shrink-0">
                        <svg class="w-14 h-14 transform -rotate-90">
                            <circle cx="28" cy="28" r="24" stroke="rgba(241, 248, 244, 1)" stroke-width="4.5" fill="transparent" />
                            <circle cx="28" cy="28" r="24" stroke="#059669" stroke-width="4.5" fill="transparent" 
                                    stroke-dasharray="150.8" 
                                    stroke-dashoffset="{{ 150.8 - (150.8 * $completionPercentage) / 100 }}" 
                                    stroke-linecap="round" 
                                    style="transition: stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1);"/>
                        </svg>
                        <span class="absolute text-sm font-black text-emerald-800">{{ $completionPercentage }}%</span>
                    </div>

                    <div class="relative z-10 min-w-0">
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">Checklist Progress</h3>
                        <p class="text-xs text-slate-400/90 font-medium mt-0.5">Resolved <strong class="text-slate-700">{{ $completedCount }}</strong> of your <strong class="text-slate-700">{{ $totalCount }}</strong> operational tasks.</p>
                        <div class="w-48 bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2 border border-slate-200/50">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-700" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Add Button Container (Beautifully proportioned) -->
                <div class="flex items-center shrink-0">
                    <button @click="openDrawerForCreate()" class="btn-task-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Task
                    </button>
                </div>
            </div>

            <!-- Kanban Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 reveal-node" id="kanbanGrid" style="--reveal-delay: 100ms">
                
                <!-- COLUMN 1: TO DO -->
                <div class="kanban-col p-5 flex flex-col min-h-[300px]" style="--col-accent: linear-gradient(135deg, #0284c7, #22d3ee)">
                    <div class="flex justify-between items-center pb-3.5 mb-5 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-400 shadow-sm" style="box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2);"></span>
                            <h3 class="font-bold text-slate-800 text-sm tracking-tight">To Do</h3>
                            <span class="px-2 py-0.5 text-xs font-black rounded-full bg-slate-200/70 text-slate-600 border border-slate-300/40">{{ $todoTasks->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($todoTasks as $task)
                            @include('tasks.partials.card', ['task' => $task, 'column' => 'todo'])
                        @empty
                            <div class="h-36 rounded-2xl border-2 border-dashed border-slate-250/60 flex flex-col justify-center items-center text-center p-5 bg-slate-100/20">
                                <svg class="w-8 h-8 text-slate-350 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">No Tasks Waiting</span>
                                <span class="text-[0.68rem] text-slate-450 mt-1 font-medium">Pipeline is currently clear.</span>
                            </div>
                        @endforelse

                        <!-- Subtle Add Card Button to anchor visual spacing -->
                        <button @click="openDrawerForCreateWithStatus('todo')" 
                                class="w-full py-3 border border-dashed border-slate-300/60 rounded-xl flex items-center justify-center gap-2 text-xs text-slate-400 hover:text-emerald-650 hover:border-emerald-250 hover:bg-emerald-50/20 transition shadow-sm bg-white/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="font-bold">Add Task</span>
                        </button>
                    </div>
                </div>

                <!-- COLUMN 2: IN PROGRESS -->
                <div class="kanban-col p-5 flex flex-col min-h-[300px]" style="--col-accent: linear-gradient(135deg, #f59e0b, #f97316)">
                    <div class="flex justify-between items-center pb-3.5 mb-5 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm" style="box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.25);"></span>
                            <h3 class="font-bold text-slate-800 text-sm tracking-tight">In Progress</h3>
                            <span class="px-2 py-0.5 text-xs font-black rounded-full bg-amber-100/80 text-amber-700 border border-amber-200/40">{{ $inProgressTasks->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($inProgressTasks as $task)
                            @include('tasks.partials.card', ['task' => $task, 'column' => 'in_progress'])
                        @empty
                            <div class="h-36 rounded-2xl border-2 border-dashed border-slate-250/60 flex flex-col justify-center items-center text-center p-5 bg-slate-100/20">
                                <svg class="w-8 h-8 text-slate-350 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">No Active Tasks</span>
                                <span class="text-[0.68rem] text-slate-450 mt-1 font-medium">Shift tasks here to start work.</span>
                            </div>
                        @endforelse

                        <!-- Subtle Add Card Button to anchor visual spacing -->
                        <button @click="openDrawerForCreateWithStatus('in_progress')" 
                                class="w-full py-3 border border-dashed border-slate-300/60 rounded-xl flex items-center justify-center gap-2 text-xs text-slate-400 hover:text-emerald-655 hover:border-emerald-250 hover:bg-emerald-50/20 transition shadow-sm bg-white/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="font-bold">Add Task</span>
                        </button>
                    </div>
                </div>

                <!-- COLUMN 3: COMPLETED -->
                <div class="kanban-col p-5 flex flex-col min-h-[300px]" style="--col-accent: linear-gradient(135deg, #059669, #84cc16)">
                    <div class="flex justify-between items-center pb-3.5 mb-5 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm" style="box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.25);"></span>
                            <h3 class="font-bold text-slate-800 text-sm tracking-tight">Completed</h3>
                            <span class="px-2 py-0.5 text-xs font-black rounded-full bg-emerald-100/80 text-emerald-700 border border-emerald-200/40">{{ $completedTasks->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($completedTasks as $task)
                            @include('tasks.partials.card', ['task' => $task, 'column' => 'completed'])
                        @empty
                            <div class="h-36 rounded-2xl border-2 border-dashed border-slate-250/60 flex flex-col justify-center items-center text-center p-5 bg-slate-100/20">
                                <svg class="w-8 h-8 text-slate-350 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">No Items Completed</span>
                                <span class="text-[0.68rem] text-slate-450 mt-1 font-medium">Daily goals await resolution.</span>
                            </div>
                        @endforelse

                        <!-- Subtle Add Card Button to anchor visual spacing -->
                        <button @click="openDrawerForCreateWithStatus('completed')" 
                                class="w-full py-3 border border-dashed border-slate-300/60 rounded-xl flex items-center justify-center gap-2 text-xs text-slate-400 hover:text-emerald-655 hover:border-emerald-250 hover:bg-emerald-50/20 transition shadow-sm bg-white/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="font-bold">Add Task</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Centered Premium Overlay Modal View (Wider 2-Column Horizontal Ratio & Vertical Centering) -->
        <div x-show="showDrawer" 
             class="fixed inset-0 z-[1001] flex items-center justify-center p-4 sm:p-6 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-205"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
            <!-- Glassy Backdrop Overlay -->
            <div class="fixed inset-0 modal-overlay transition-opacity" @click="showDrawer = false"></div>

            <!-- Modal box styled and structured for horizontal layout and centered presentation -->
            <div x-show="showDrawer"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-180"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-200/80 z-10 my-auto">
                
                <!-- Top Accent line -->
                <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-emerald-500 to-teal-400"></div>

                <!-- Modal Header -->
                <div class="px-6 pt-6 pb-4 flex justify-between items-start border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight" x-text="isEditing ? 'Modify Operations Task' : 'Register Pipeline Task'"></h3>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Configure properties to update operations workflows.</p>
                    </div>
                    <button @click="showDrawer = false" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg hover:bg-slate-50 border border-slate-100">
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form Content in a Highly Polished 2-Column Grid -->
                <form :action="isEditing ? '{{ url('tasks') }}/' + task.id : '{{ route('tasks.store') }}'" 
                      method="POST"
                      class="m-0">
                    @csrf
                    <input type="hidden" name="_method" :value="isEditing ? 'PUT' : 'POST'">

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- LEFT COLUMN: Content & Inputs -->
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="task_title" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Task Title *</label>
                                <input type="text" 
                                       name="title" 
                                       id="task_title" 
                                       x-model="task.title"
                                       required 
                                       class="w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2 px-3.5 font-medium text-slate-800 transition" 
                                       placeholder="e.g. Schedule Windrow turning">
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="task_desc" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Detailed Notes</label>
                                <textarea name="description" 
                                          id="task_desc" 
                                          x-model="task.description"
                                          rows="4" 
                                          class="w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2 px-3.5 font-medium text-slate-800 transition" 
                                          placeholder="Reference JCB work, vehicle trips, or cargo weight details..."></textarea>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Metadata & Settings -->
                        <div class="space-y-4">
                            <!-- Assignee -->
                            <div>
                                <label for="task_assignee" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Assign Responsibility</label>
                                <select name="assigned_to" 
                                        id="task_assignee" 
                                        x-model="task.assigned_to"
                                        class="w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2 font-semibold text-slate-700 transition">
                                    <option value="">-- Unassigned / System --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="task_due" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Due Deadline</label>
                                <input type="date" 
                                       name="due_date" 
                                       id="task_due" 
                                       x-model="task.due_date"
                                       class="w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2 px-3 font-medium text-slate-800 transition">
                                </div>

                                <!-- Priority & Status side-by-side -->
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Priority -->
                                    <div>
                                        <label for="task_priority" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Priority</label>
                                        <select name="priority" 
                                                id="task_priority" 
                                                x-model="task.priority"
                                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2 font-semibold text-slate-700 transition">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="task_status" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pipeline Status</label>
                                        <select name="status" 
                                                id="task_status" 
                                                x-model="task.status"
                                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2 font-semibold text-slate-700 transition">
                                            <option value="todo">To Do</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Footer Actions -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-between gap-3 border-t border-slate-100 rounded-b-2xl">
                            <button type="button" 
                                    @click="showDrawer = false" 
                                    class="w-1/2 justify-center inline-flex items-center px-4 py-2 border border-slate-200 text-slate-750 bg-white rounded-xl font-bold text-sm hover:bg-slate-50 transition shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="w-1/2 justify-center inline-flex items-center px-4 py-2 text-white bg-slate-900 hover:bg-emerald-700 rounded-xl font-bold text-sm shadow-md transition">
                                Save Task
                            </button>
                        </div>
                    </form>
                </div>
            
        </div>
    </div>

    <!-- Scripts to feed Alpine data structure -->
    <script>
        function taskBoard() {
            return {
                showDrawer: false,
                isEditing: false,
                task: {
                    id: null,
                    title: '',
                    description: '',
                    priority: 'medium',
                    status: 'todo',
                    due_date: '',
                    assigned_to: ''
                },

                openDrawerForCreate() {
                    this.isEditing = false;
                    this.task = {
                        id: null,
                        title: '',
                        description: '',
                        priority: 'medium',
                        status: 'todo',
                        due_date: '',
                        assigned_to: ''
                    };
                    this.showDrawer = true;
                },

                openDrawerForCreateWithStatus(targetStatus) {
                    this.isEditing = false;
                    this.task = {
                        id: null,
                        title: '',
                        description: '',
                        priority: 'medium',
                        status: targetStatus,
                        due_date: '',
                        assigned_to: ''
                    };
                    this.showDrawer = true;
                },

                openDrawerForEdit(taskData) {
                    this.isEditing = true;
                    this.task = {
                        id: taskData.id,
                        title: taskData.title,
                        description: taskData.description || '',
                        priority: taskData.priority,
                        status: taskData.status,
                        due_date: taskData.due_date ? taskData.due_date.split('T')[0] : '',
                        assigned_to: taskData.assigned_to || ''
                    };
                    this.showDrawer = true;
                }
            };
        }

        // Initialize IntersectionObserver animation reveals matching dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08 });

            document.querySelectorAll('.reveal-node').forEach(el => observer.observe(el));
        });
    </script>
</x-app-layout>
