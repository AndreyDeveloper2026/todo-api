<?php

namespace App\Services;

use App\Contracts\EventBus;
use App\Domains\Task\Events\TaskCreatedEvent;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function __construct(
        private EventBus $eventBus
    ) {}

    public function create(array $data, User $user, Project $project): Task
    {
        $task = $project->tasks()->create([
            ...$data,
            'user_id' => $user->id,
        ]);

        $this->eventBus->publish(
            new TaskCreatedEvent(
                $task->id,
                $user->id,
                $project->id,
            )
        );

        logger()->info('3 EVENT SENT TO EVENT BUS');

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function getUserTasks(User $user)
    {
        return Task::forUser($user->id)
            ->latest()
            ->get();
    }

    public function getProjectTasks(Project $project, array $filters = [])
    {
        return Task::query()
            ->forProject($project)
            ->status($filters['status'] ?? null)
            ->search($filters['search'] ?? null)
            ->latest()
            ->paginate(20);
    }
}
