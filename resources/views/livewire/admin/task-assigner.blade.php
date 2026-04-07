<div>
    <div class="mb-6">
        <h1 class="text-lg font-semibold text-gray-900">Task oversight</h1>
        <p class="text-sm text-gray-500 mt-1">
            View all tasks across projects. Assignments are managed by team leads from
            <span class="font-medium text-gray-600">Lead → Manage Tasks</span>.
        </p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <select wire:model.live="filterStatus"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In progress</option>
            <option value="done">Done</option>
        </select>

        <select wire:model.live="filterProject"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All projects</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tasks table (read-only) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($tasks->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-sm">No tasks match your filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[800px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Task</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Project / team</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Assigned to</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Created by</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Due date</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Priority</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tasks as $task)
                            @php
                                $exceeded = $task->isExceededDeadline();
                            @endphp
                            <tr class="hover:bg-gray-50 transition {{ $exceeded ? 'bg-red-50/80 hover:bg-red-50' : '' }}">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $task->title }}</p>
                                    @if($task->description)
                                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $task->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <p>{{ $task->project->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $task->team->name }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $task->assignee->name }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $task->creator?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $exceeded ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                        {{ $task->due_date->format('M d, Y') }}
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
