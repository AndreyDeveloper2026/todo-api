<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectService
{
    public function create(array $data, User $user): Project
    {
        return Project::create([
            ...$data,
            'user_id' => $user->id,
        ]);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    public function getUserProjects(User $user)
    {
        return $user->projects()->latest()->get();
    }
}
