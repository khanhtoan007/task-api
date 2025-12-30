<?php

namespace App\Services;

use App\Exceptions\ApiException;
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
        return Project::query()->with('tasks')->findOrFail($projectId)->toArray();
    }

    public function createProject(ProjectRequest $projectRequest): Project
    {

        return Project::query()->create([
            'name' => $projectRequest->name,
            'description' => $projectRequest->description,
            'start_date' => $projectRequest->start_date,
            'end_date' => $projectRequest->end_date,
            'created_by' => auth()->guard('api')->user()->id,
            'status' => ProjectStatusEnum::DRAFT,
        ]);
    }

    public function getProjectsByUser(): array
    {
        try {
            $projects = Project::query()->where('created_by', auth()->guard('api')->user()->id)->get()->toArray();
            return $projects;
        } catch (ApiException $e) {
            throw $e;
        }
    }

    public function updateProject(string $id, ProjectRequest $projectRequest): bool
    {
        $project = Project::query()->findOrFail($id);

        return $project->update($projectRequest->toArray());
    }
}
