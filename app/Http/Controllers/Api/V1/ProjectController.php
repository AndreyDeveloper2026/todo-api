<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Services\ProjectService;


class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $service
    ) {}

    public function index(Request $request)
    {
        return $this->service->getUserProjects($request->user());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->service->create(
            $validated,
            $request->user()
        );
    }

    public function show(Project $project)
    {
        return $project;
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->service->update($project, $validated);
    }

    public function destroy(Project $project)
    {
        $this->service->delete($project);

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}
