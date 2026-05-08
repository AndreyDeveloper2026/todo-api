<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function create(array $data, User $user): Task
    {
        return Task::create([
            ...$data,
            'user_id' => $user->id,
        ]);
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
}
