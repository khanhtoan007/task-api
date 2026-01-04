<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Enums\ProjectRoleEnum;
use App\Http\Enums\ProjectStatusEnum;
use App\Http\Requests\AssignUserRequest;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ProjectService
{
    public function getProjects(): array
    {
        return Project::query()->get(
            'id', 'name', 'description', 'start_date', 'end_date', 'created_by', 'status'
        )->with([
            'createdBy' => function ($query): void {
                $query->select('id', 'name', 'email');
            },
        ])->toArray();
    }

    public function getProject(string $projectId): array
    {
        return Project::query()
            ->with('tasks')
            ->with([
                'projectMembers' => function ($query): void {
                    $query->select('id', 'project_id', 'user_id', 'role', 'joined_at', 'invited_by');
                },
                'projectMembers.user' => function ($query): void {
                    $query->select('id', 'name');
                },
            ])
            ->findOrFail($projectId)
            ->toArray();
    }

    /**
     * @throws ApiException
     */
    public function createProject(ProjectRequest $projectRequest): Project
    {
        try {
            return DB::transaction(function () use ($projectRequest) {
                $project = Project::query()->create([
                    'name' => $projectRequest->name,
                    'description' => $projectRequest->description,
                    'start_date' => $projectRequest->start_date,
                    'end_date' => $projectRequest->end_date,
                    'created_by' => auth()->guard('api')->user()->id,
                    'status' => ProjectStatusEnum::DRAFT,
                ]);
                // create owner in project_member
                ProjectMember::query()->create([
                    'project_id' => $project->id,
                    'user_id' => auth()->guard('api')->user()->id,
                    'role' => ProjectRoleEnum::OWNER,
                    'joined_at' => now(),
                    'invited_by' => auth()->guard('api')->user()->id,
                ]);
                return $project;
            });
        } catch (Exception $e) {
            Log::error('Failed to create project: '.$e->getMessage());
            throw new ApiException('Failed to create project', 500);
        }
    }

    public function getProjectsByUser(): array
    {
        try {
            $projects = Project::query()->where('created_by', auth()->guard('api')->user()->id)
                ->with([
                    'createdBy' => function ($query): void {
                        $query->select('id', 'name', 'email');
                    },
                ])->get()->toArray();

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
