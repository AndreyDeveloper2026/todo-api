<?php

namespace App\Services;

use App\Contracts\EventBus;
use App\Events\TaskCreated;
use App\Jobs\SendTaskCreatedNotificationJob;
use App\Jobs\TrackTaskCreatedAnalyticsJob;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Jobs\HandleTaskCreatedJob;

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
            new TaskCreated(
                taskId: $task->id,
                userId: $user->id,
                projectId: $project->id,
            )
        );

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
