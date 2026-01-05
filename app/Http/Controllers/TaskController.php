<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

final class TaskController
{
    use ApiResponseTrait, AuthorizesRequests;

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
    public function index(Project $project): JsonResponse
    {
        return $this->successResponse(
            data: $this->taskService->getProjectTasks($project),
            message: 'Tasks of project '.$project->name.' successfully retrieved.',
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
    public function store(TaskRequest $request, Project $project): JsonResponse
    {
        $validated = $request->validated();
        $task = $this->taskService->createTask($validated);

        return $this->successResponse(
            data: new TaskResource($task),
            message: 'Task created successfully',
            code: 201
        );
    }

    public function show(Project $project, Task $task): JsonResponse
    {
        return $this->successResponse(
            data : $this->taskService->getTaskById($task),
            message: 'Task retrieved successfully',
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function update(TaskRequest $taskRequest, Project $project, Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        return $this->successResponse(
            data: $this->taskService->updateTask($task, $taskRequest->array()),
            message: 'Task updated successfully',
            code: 204
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Project $project, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        return $this->successResponse(
            data: $this->taskService->deleteTask($task),
            message: 'Task deleted successfully',
        );
    }

    public function myTasks(TaskIndexRequest $request): JsonResponse
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
}
