<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
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
    public function index(TaskIndexRequest $request): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks($request);
        return $this->successResponse(
            data: [
                'tasks' => TaskResource::collection($tasks->items()),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                ],
            ],
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
    public function update(string $id, TaskRequest $taskRequest): JsonResponse
    {
        return $this->successResponse(
            data: $this->taskService->updateTask($id, $taskRequest->array()),
            message: 'Task updated successfully',
            code: 204
        );
    }
}
