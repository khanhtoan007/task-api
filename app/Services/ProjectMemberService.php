<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\AssignUserRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class ProjectMemberService
{
    public function getMembers(Project $project): Collection
    {
        return $project->members;
    }

    /**
     * @throws ApiException
     */
    public function assignUser(Project $project, AssignUserRequest $request): bool
    {
        try {
            $user = User::query()->findOrFail($request->user_id);
            if ($project->created_by === $user->id) {
                throw new ApiException('You cannot assign yourself as a member', 400);
            }
            if (ProjectMember::query()->where('project_id', $project->id)->where('user_id', $user->id)->exists()) {
                throw new ApiException('User is already a member of the project', 400);
            }
            ProjectMember::query()->create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role' => $request->role,
                'invited_by' => auth()->guard('api')->user()->id,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to assign user to project: '.$e->getMessage());
            throw $e;
        }
    }
    public function update(AssignUserRequest $request, ProjectMember $projectMember): ProjectMember
    {
        $projectMember->update([
            'role' => $request->role,
        ]);
        return $projectMember;
    }
    public function remove(ProjectMember $projectMember): bool
    {
        return $projectMember->delete();
    }
}
