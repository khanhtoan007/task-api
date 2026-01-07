<?php

namespace App\Services;

use App\Contracts\QueryBuilderInterface;
use App\Exceptions\ApiException;
use App\Http\Enums\ProjectRoleEnum;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\AssignUserRequest;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Traits\ResponseListQuery;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ProjectService
{
    use ResponseListQuery;

    public function __construct(
        private readonly QueryBuilderInterface $queryBuilder
    ) {}

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
                $project = Project::query()->create([
                    'name' => $projectRequest->name,
                    'description' => $projectRequest->description,
                    'start_date' => $projectRequest->start_date,
                    'end_date' => $projectRequest->end_date,
                    'created_by' => auth()->guard('api')->user()->id,
                    'status' => $projectRequest->status,
                ]);
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

    public function getProjectsByUser(BaseIndexRequest $request): LengthAwarePaginator
    {
        $query = Project::query()
        ->with(['createdBy:id,name,email'])
        ->where('created_by', auth()->guard('api')->user()->id);
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: $query,
            request: $request,
            searchFields: ['name', 'description', 'created_by'],
            customFilters: []
        );
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

    public function destroy(string $id): bool
    {
        try{
            $project = Project::query()->findOrFail($id);
            $project->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete project: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateProject(Project $project, ProjectRequest $projectRequest)
    {
        try {
            $project->update($projectRequest->toArray());
            return $project->refresh();
        } catch (Exception $e) {
            Log::error('Failed to update project: '.$e->getMessage());
            throw new ApiException('Failed to update project', 500);
        }
    }
}
