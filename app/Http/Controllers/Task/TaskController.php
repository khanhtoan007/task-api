<?php

namespace App\Http\Controllers\Task;

use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\Task\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *   path="/api/tasks",
 *   summary="List tasks",
 *   @OA\Response(response=200, description="OK")
 * )
 */
final class TaskController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly TaskService $taskService
    ) {
    }

    /**
     * Get paginated list of tasks with filters and sorting
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
    public function create(TaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $task = $this->taskService->createTask($validated);

        return $this->successResponse(
            data: new TaskResource($task),
            message: 'Task created successfully',
            code: 201
        );
    }
}