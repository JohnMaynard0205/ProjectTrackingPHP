<div>
    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header row --}}
    <div class="flex flex-wrap items-center gap-3 justify-between mb-6">
        <div class="flex items-center gap-3">
            {{-- Filter by status --}}
            <select wire:model.live="filterStatus"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="done">Done</option>
            </select>

            {{-- Filter by project --}}
            <select wire:model.live="filterProject"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        @if(!$showForm)
            <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Task
            </button>
        @endif
    </div>

    {{-- Create / Edit form --}}
    @if($showForm)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-800 mb-5">
                {{ $editingId ? 'Edit Task' : 'New Task' }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" placeholder="e.g. Design homepage mockup"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="2" placeholder="Optional details…"
                              class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                {{-- Project --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                    <select wire:model.live="projectId"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('projectId') border-red-400 @enderror">
                        <option value="">— Select project —</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('projectId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Team --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Team <span class="text-red-500">*</span></label>
                    <select wire:model.live="teamId"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('teamId') border-red-400 @enderror"
                            @disabled(!$projectId)>
                        <option value="">— Select team —</option>
                        @foreach($teamsForForm as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('teamId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Assigned To --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign To <span class="text-red-500">*</span></label>
                    <select wire:model="assignedTo"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('assignedTo') border-red-400 @enderror"
                            @disabled(!$teamId)>
                        <option value="">— Select member —</option>
                        @foreach($membersForForm as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('assignedTo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select wire:model="priority"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input wire:model="startDate" type="date"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                {{-- Due Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-red-500">*</span></label>
                    <input wire:model="dueDate" type="date"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('dueDate') border-red-400 @enderror">
                    @error('dueDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Status (only on edit) --}}
                @if($editingId)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model="status"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                @endif
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button wire:click="save"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    {{ $editingId ? 'Update Task' : 'Create Task' }}
                </button>
                <button wire:click="cancelForm"
                        class="px-5 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    {{-- Tasks table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($tasks->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-sm">No tasks found.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Task</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Project / Team</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Assigned To</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Due Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Priority</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tasks as $task)
                        @php
                            $exceeded = $task->isExceededDeadline();
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $exceeded ? 'bg-red-50 hover:bg-red-50' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $task->title }}</p>
                                @if($task->description)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $task->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <p>{{ $task->project->name }}</p>
                                <p class="text-xs text-gray-400">{{ $task->team->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $task->assignee->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="{{ $exceeded ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                                </span>
                                @if($exceeded)
                                    <span class="ml-1 text-xs text-red-500">Overdue</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $priorityBadge = match($task->priority) {
                                        'high'   => 'bg-red-100 text-red-700',
                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                        'low'    => 'bg-gray-100 text-gray-600',
                                        default  => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $priorityBadge }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusBadge = match($task->status) {
                                        'pending'     => 'bg-gray-100 text-gray-600',
                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                        'done'        => 'bg-green-100 text-green-700',
                                        default       => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <button wire:click="openEdit({{ $task->id }})"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-3 transition">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $task->id }})"
                                        wire:confirm="Delete this task?"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium transition">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
