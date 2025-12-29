<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Services\ProjectService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

final readonly class ProjectController
{
    use ApiResponseTrait;

    public function __construct(private ProjectService $projectService) {}

    /**
     * @OA\Get(
     *   path="/api/projects",
     *   tags={"Projects"},
     *   summary="Get list of projects",
     *   @OA\Response(response=200, description="OK")
     * )
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
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string", format="uuid")
     *   ),
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

    /**
     * @OA\Put(
     *   path="/api/projects/{id}",
     *   tags={"Projects"},
     *   summary="Update project",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function update(string $id, ProjectRequest $projectRequest): JsonResponse
    {
        return $this->successResponse(
            data: $this->projectService->updateProject($id, $projectRequest),
            message: 'Project updated successfully'
        );
    }
}
