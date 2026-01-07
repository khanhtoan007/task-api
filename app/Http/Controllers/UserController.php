<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\CreateUserContestRequest;
use App\Http\Requests\ProjectRequest;
use App\Http\Requests\UpdateUserContestRequest;
use App\Models\User;
use App\Services\ProjectService;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

final readonly class UserController
{
    use ApiResponseTrait;

    public function __construct(
        private ProjectService $projectService,
        private UserService $userService
    ) {}

    /**
     * @OA\Get(
     *   path="/api/projects",
     *   tags={"Projects"},
     *   summary="Get list of projects",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index(BaseIndexRequest $request): JsonResponse
    {
        $projects = $this->projectService->getProjectsByUser($request);

        return $this->paginatedResponse(
            paginator: $projects,
            message: 'Projects fetched successfully'
        );
    }

    /**
     * @OA\Post(
     *   path="/api/projects",
     *   tags={"Projects"},
     *   summary="Create new project",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function store(ProjectRequest $projectRequest): JsonResponse
    {
        return $this->successResponse(
            data: $this->projectService->createProject($projectRequest),
            message: 'Project created successfully',
            code: 201
        );
    }

    /**
     * @OA\Get(
     *   path="/api/projects/{id}",
     *   tags={"Projects"},
     *   summary="Get project detail",
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
     */
    public function show(string $id): JsonResponse
    {
        $project = $this->projectService->getProject($id);

        return $this->successResponse(
            data: $project,
            message: 'Project fetched successfully'
        );
    }

    public function update(User $user, UpdateUserContestRequest $userRequest): JsonResponse
    {
        $userRequest->validated();
        return $this->successResponse(
            data: $this->userService->updateUser($user, $userRequest),
            message: 'User updated successfully'
        );
    }




        /**
     * @OA\Get(
     *   path="/api/projects",
     *   tags={"Projects"},
     *   summary="Get list of projects",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function getAllContestUser(BaseIndexRequest $request): JsonResponse
    {
        $users = $this->userService->getAllUsers($request);

        return $this->paginatedResponse(
            paginator: $users,
            message: 'Users fetched successfully'
        );
    }

    public function delete(User $user): JsonResponse
    {
        return $this->successResponse(
            data: $this->userService->destroy($user->id),
            message: 'User deleted successfully'
        );
    }

    public function createUserContest(CreateUserContestRequest $createUser): JsonResponse
    {
        return $this->successResponse(
            data: $this->userService->createUserContest($createUser),
            message: 'User contest created successfully',
            code: 201
        );
    }

}
