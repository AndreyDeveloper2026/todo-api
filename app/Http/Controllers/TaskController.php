<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
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

    public function index(Request $request)
    {
        return TaskResource::collection(
            $this->service->getUserTasks($request->user())
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->service->create(
            $request->validated(),
            $request->user()
        );

        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->service->update(
            $task,
            $request->validated()
        );

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->service->delete($task);

        return response()->json(['message' => 'Deleted']);
    }
}
