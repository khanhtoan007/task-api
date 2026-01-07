# Index Request Pattern - Single Request Class với Trait

## Pattern Overview

**Một `BaseIndexRequest` chung** cho tất cả list endpoints, không cần tạo class riêng cho mỗi domain. Custom filters được handle trong **service layer** qua trait `ResponseListQuery`.

## Cấu trúc

```
BaseIndexRequest (final class)
  └── HasIndexRequest (trait) - pagination, sorting, search logic

ResponseListQuery (trait) - bổ sung query logic vào service
  └── paginateWithQueryBuilder() - với customFilters parameter

Service Layer
  └── Extract custom filters từ request
  └── Pass vào paginateWithQueryBuilder()
```

## Response Format

Tất cả list endpoints trả về format nhất quán với pagination fields ở root level:

```json
{
  "success": true,
  "message": "Tasks retrieved successfully",
  "data": [...],
  "page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7,
  "from": 1,
  "to": 15
}
```

## Cách sử dụng

### 1. Controller - Dùng BaseIndexRequest trực tiếp

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;

final class TaskController
{
    use ApiResponseTrait;

    public function index(BaseIndexRequest $request): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks($request);

        return $this->paginatedResponse(
            paginator: $tasks,
            items: TaskResource::collection($tasks->items()),
            message: 'Tasks retrieved successfully'
        );
    }
}
```

### 2. Service - Bổ sung custom filters vào query

```php
<?php

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

    protected array $searchFields = ['title', 'description'];

    public function getAllTasks(BaseIndexRequest $request): LengthAwarePaginator
    {
        // Extract custom filters từ request (status, priority, etc.)
        $customFilters = [];
        if ($request->has('status') && $request->filled('status')) {
            $customFilters['status'] = $request->input('status');
        }

        // Pass custom filters vào paginateWithQueryBuilder
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: Task::query(),
            request: $request,
            searchFields: $this->searchFields,
            customFilters: $customFilters, // Bổ sung custom filters ở đây
            customFilterCallback: fn (Builder $q, array $f) => $this->applyCustomFilters($q, $f)
        );
    }

    /**
     * Apply custom filters logic (nếu cần complex logic)
     */
    private function applyCustomFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Có thể thêm logic phức tạp hơn ở đây
        // if (isset($filters['date_from'])) {
        //     $query->where('created_at', '>=', $filters['date_from']);
        // }

        return $query;
    }
}
```

### 3. Service đơn giản (không có custom filters)

```php
<?php

namespace App\Services;

use App\Http\Requests\BaseIndexRequest;
use App\Models\Project;
use App\Traits\ResponseListQuery;

final class ProjectService
{
    use ResponseListQuery;

    public function getProjectsByUser(BaseIndexRequest $request): LengthAwarePaginator
    {
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: Project::query()->where('created_by', auth()->id()),
            request: $request,
            searchFields: ['name', 'description'],
            customFilters: [] // Không có custom filters
        );
    }
}
```

## API Usage Examples

### Basic pagination

```
GET /api/tasks?page=1&per_page=20
```

### With search

```
GET /api/tasks?search=meeting
```

### With custom filters (từ service)

```
GET /api/tasks?status=pending
```

### With sorting

```
GET /api/tasks?sort_by=created_at&sort_order=desc
```

### Combine all

```
GET /api/tasks?page=1&per_page=10&search=meeting&status=pending&sort_by=created_at&sort_order=desc
```

## Lợi ích

1. **Không cần tạo 100 file IndexRequest** - chỉ 1 `BaseIndexRequest` chung
2. **Custom filters trong service** - dễ maintain, logic gần data layer
3. **Response format nhất quán** - FE dễ implement, có pagination fields ở root
4. **DRY** - Trait reuse logic, không lặp code
5. **Flexible** - Dễ dàng thêm custom filters cho từng service
6. **Type-safe** - Vẫn type-safe với Laravel FormRequest
7. **Simple** - Pattern đơn giản, dễ hiểu

## Custom Filters Pattern

### Cách 1: Simple filters (khuyến nghị)

```php
$customFilters = [];
if ($request->has('status') && $request->filled('status')) {
    $customFilters['status'] = $request->input('status');
}
```

### Cách 2: Multiple filters với helper

```php
$customFilters = [];
$fields = ['status', 'priority', 'assignee_id'];
foreach ($fields as $field) {
    if ($request->has($field) && $request->filled($field)) {
        $customFilters[$field] = $request->input($field);
    }
}
```

### Cách 3: Complex logic trong customFilterCallback

```php
customFilterCallback: function (Builder $q, array $f) {
    if (isset($f['status'])) {
        $q->where('status', $f['status']);
    }
    if (isset($f['date_from']) && isset($f['date_to'])) {
        $q->whereBetween('created_at', [$f['date_from'], $f['date_to']]);
    }
    return $q;
}
```

## Validation

`BaseIndexRequest` validate:

- `page`: integer, min:1
- `per_page`: integer, min:1, max:100
- `search`: string, max:255
- `sort_by`: string, max:255
- `sort_order`: string, in:asc,desc

Custom fields validation nên được handle trong **service layer** nếu cần, hoặc tạo separate FormRequest cho các endpoint đặc biệt.

## So sánh với pattern cũ

| Aspect          | Pattern cũ               | Pattern mới                |
| --------------- | ------------------------ | -------------------------- |
| Files           | 100 IndexRequest files   | 1 BaseIndexRequest         |
| Custom filters  | Trong IndexRequest class | Trong Service layer        |
| Response format | Nested pagination        | Flat pagination fields     |
| Flexibility     | Phải extend class        | Chỉ cần thêm trong service |
| Maintenance     | Sửa 100 files            | Sửa 1 file + service       |
