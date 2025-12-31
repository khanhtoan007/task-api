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

    public function createProject(ProjectRequest $projectRequest)
    {
        try {
            DB::transaction(function () use ($projectRequest) {
                // create owner in project_member
                ProjectMember::query()->create([
                    'project_id' => $projectRequest->id,
                    'user_id' => auth()->guard('api')->user()->id,
                    'role' => ProjectRoleEnum::OWNER,
                    'joined_at' => now(),
                    'invited_by' => auth()->guard('api')->user()->id,
                ]);
                $project = Project::query()->create([
                    'name' => $projectRequest->name,
                    'description' => $projectRequest->description,
                    'start_date' => $projectRequest->start_date,
                    'end_date' => $projectRequest->end_date,
                    'created_by' => auth()->guard('api')->user()->id,
                    'status' => ProjectStatusEnum::DRAFT,
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

    public function assignUser(string $id, AssignUserRequest $request): bool
    {
        try {
            $project = Project::query()->findOrFail($id);
            $user = User::query()->findOrFail($request->user_id);
            if ($project->created_by === $user->id) {
                throw new ApiException('You cannot assign yourself as a member', 400);
            }
            if (ProjectMember::query()->where('project_id', $project->id)->where('user_id', $user->id)->exists()) {
                throw new ApiException('User is already a member of the project', 400);
            }
            ProjectMember::query()->create([
                'project_id' => $id,
                'user_id' => $user->id,
                'role' => $request->role,
                'joined_at' => now(),
                'invited_by' => auth()->guard('api')->user()->id,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to assign user to project: '.$e->getMessage());
            throw $e;
        }
    }
}
