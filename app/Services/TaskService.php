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
    public function getAllTasks(BaseIndexRequest $request): LengthAwarePaginator
    {
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: Task::query(),
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
        $data['created_by'] = auth()->user()->id;

        return Task::query()->create($data);
    }

    /**
     * Get task by ID
     */
    public function getTaskById(string $id): ?Task
    {
        return Task::query()->with(['createdBy', 'assignedTo', 'project', 'subTasks'])->findOrFail($id);
    }

    public function updateTask(string $id, array $data): bool
    {
        $task = Task::query()->findOrFail($id);

        return $task->update($data);
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
}
