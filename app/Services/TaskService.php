<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\QueryBuilderInterface;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Task;
use App\Traits\ResponseListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class TaskService
{
    use ResponseListQuery;

    /**
     * Fields to search in
     */
    protected array $searchFields = ['title', 'description'];

    public function __construct(
        private readonly QueryBuilderInterface $queryBuilder
    ) {}

    /**
     * Get paginated tasks with filters and sorting
     */
    public function getAllTasks(BaseIndexRequest $request, Builder $query): LengthAwarePaginator
    {
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: $query,
            request: $request,
            searchFields: $this->searchFields,
            customFilterCallback: fn (Builder $q, array $f) => $this->applyCustomFilters($q, $f)
        );
    }

    /**
     * Create a new task
     */
    public function createTask(array $data): Task
    {
        $data['created_by'] = auth()->id();
        return Task::query()->create($data);
    }

    /**
     * Get task by ID
     */
    public function show(Task $task): ?Task
    {
        return $task->load(['createdBy', 'assignedTo', 'project', 'subTasks']);
    }

    /**
     * Apply custom filters (status, etc.)
     */
    private function applyCustomFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }
}
