<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\AssignUserRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Services\ProjectMemberService;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

final class ProjectMemberController
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(private readonly ProjectMemberService $projectMemberService) {}

    /**
     * @throws AuthorizationException
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('canViewMembers', $project);
        return $this->successResponse(
            data: $this->projectMemberService->getMembers($project),
            message: 'Members list',
        );
    }

    /**
     * @OA\Post(
     *   path="/api/projects/{id}/assign-user",
     *   tags={"Projects"},
     *   summary="Assign user to project",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *
     *   @OA\Response(response=200, description="OK")
     * )
     *
     * @throws ApiException
     * @throws AuthorizationException
     */
    public function store(AssignUserRequest $request, Project $project): JsonResponse
    {
        $this->authorize('canAssign', $project);

        return $this->successResponse(
            data: $this->projectMemberService->assignUser($project, $request),
            message: 'User assigned to project successfully'
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function update(AssignUserRequest $request, Project $project, ProjectMember $member): JsonResponse
    {
       $this->authorize('update', $member);
        return $this->successResponse(
            data: $this->projectMemberService->update($request, $member),
            message: 'User role update successfully'
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Project $project, ProjectMember $member): JsonResponse
    {
        $this->authorize('delete', $member);
        return $this->successResponse(
            message: 'User remove from project '.$this->projectMemberService->remove($member) ? 'successfully' : 'failed',
            code: 200
        );
    }
}
