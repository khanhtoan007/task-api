<?php

namespace App\Services;

use App\Http\Enums\ProjectStatusEnum;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;

final class ProjectService
{
    public function getProjects(): array
    {
        return Project::query()->get()->toArray();
    }
    public function getProject(string $projectId): array
    {
        return Project::query()->findOrFail($projectId)->toArray();
    }
    public function createProject(ProjectRequest $projectRequest): Project
    {

        return Project::query()->create([
            'name' => $projectRequest->name,
            'description' => $projectRequest->description,
            'start_date' => $projectRequest->start_date,
            'end_date' => $projectRequest->end_date,
            'status' => ProjectStatusEnum::DRAFT,
        ]);
    }
    public function updateProject(string $id, ProjectRequest $projectRequest): bool
    {
        $project = Project::query()->findOrFail($id);
        return $project->update($projectRequest->toArray());
    }
}
