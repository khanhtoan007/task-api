<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

final class TaskController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly TaskService $taskService
    ) {}

    /**
     * Get paginated list of tasks with filters and sorting
     */
    /**
     * @OA\Get(
     *   path="/api/tasks",
     *   summary="List tasks",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index(BaseIndexRequest $request): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks($request);

        return $this->paginatedResponse(
            paginator: $tasks,
            items: TaskResource::collection($tasks->items()),
            message: 'Tasks retrieved successfully'
        );
    }

    /**
     * Create a new task
     */
    /**
     * @OA\Post(
     *   path="/api/tasks",
     *   summary="Create a task",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $task = $this->taskService->createTask($validated);

        return $this->successResponse(
            data: new TaskResource($task),
            message: 'Task created successfully',
            code: 201
        );
    }

    public function show(string $id): JsonResponse
    {
        return $this->successResponse(
            data : $this->taskService->getTaskById($id),
            message: 'Task retrieved successfully',
        );
    }

    /**
     * Get tasks by project
     */
    /**
     * @OA\Get(
     *   path="/api/projects/{project}/tasks",
     *   summary="Get tasks by project",
     *   tags={"Tasks"},
     *
     *   @OA\Parameter(
     *     name="project",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function getTasksByProject(Project $project): JsonResponse
    {
        return $this->successResponse(
            data: $this->taskService->getTasksByProject($project),
            message: 'Tasks retrieved successfully',
        );
    }

    /**
     * Update a task
     */
    /**
     * @OA\Put(
     *   path="/api/tasks/{id}",
     *   summary="Update a task",
     *   tags={"Tasks"},
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function update(string $id, TaskRequest $taskRequest): JsonResponse
    {
        return $this->successResponse(
            data: $this->taskService->updateTask($id, $taskRequest->array()),
            message: 'Task updated successfully',
            code: 204
        );
    }
}
