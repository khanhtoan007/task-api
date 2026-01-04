<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

final readonly class ProjectController
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(private ProjectService $projectService) {}

    /**
     * @OA\Get(
     *   path="/api/projects",
     *   tags={"Projects"},
     *   summary="Get list of projects",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     * @throws ApiException
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            data: $this->projectService->getProjectsByUser(),
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
     *
     * @throws ApiException
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
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);
        return $this->successResponse(
            data: $project,
            message: 'Project fetched successfully'
        );
    }

    /**
     * @OA\Put(
     *   path="/api/projects/{id}",
     *   tags={"Projects"},
     *   summary="Update project",
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
    public function update(Project $project, ProjectRequest $projectRequest): JsonResponse
    {
        $this->authorize('update', $project);
        return $this->successResponse(
            data: $this->projectService->updateProject($project, $projectRequest),
            message: 'Project updated successfully'
        );
    }
}
