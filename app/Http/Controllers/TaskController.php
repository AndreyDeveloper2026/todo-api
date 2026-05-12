<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TaskService $service
    ) {}

    public function index(Project $project, Request $request)
    {
        $this->authorize('view', $project);

        return TaskResource::collection(
            $this->service->getProjectTasks(
                $project,
                $request->all()
            )
        );
    }

    public function store(
        StoreTaskRequest $request,
        Project $project
    )
    {
        $this->authorize('createTask', $project);

        $task = $this->service->create(
            $request->validated(),
            $request->user(),
            $project
        );

        return new TaskResource($task);
    }

    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        $this->ensureTaskBelongsToProject($task, $project);

        return new TaskResource($task);
    }

    public function update(
        UpdateTaskRequest $request,
        Project $project,
        Task $task
    )
    {
        $this->authorize('update', $project);

        $this->ensureTaskBelongsToProject($task, $project);

        $task = $this->service->update(
            $task,
            $request->validated()
        );

        return new TaskResource($task);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $project);

        $this->ensureTaskBelongsToProject($task, $project);

        $this->service->delete($task);

        return response()->json(['message' => 'Deleted']);
    }

    private function ensureTaskBelongsToProject(Task $task, Project $project): void
    {
        if ($task->project_id !== $project->id) {
            abort(403, 'Task does not belong to this project');
        }
    }
}
