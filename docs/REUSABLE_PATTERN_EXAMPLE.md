# Reusable Filter, Search, Pagination Pattern

## Cách sử dụng pattern này

### 1. Tạo IndexRequest cho resource mới

```php
<?php

namespace App\Http\Requests;

class IndexUserRequest extends BaseIndexRequest
{
    /**
     * Define fields có thể sort
     */
    protected function getAllowedSortFields(): array
    {
        return ['name', 'email', 'created_at', 'updated_at'];
    }

    /**
     * Thêm custom validation rules (nếu có)
     */
    protected function getCustomRules(): array
    {
        return [
            'role' => 'sometimes|string|in:admin,user',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Lấy custom filters từ request
     */
    protected function getCustomFilters(): array
    {
        $filters = [];

        if ($this->has('role') && $this->filled('role')) {
            $filters['role'] = $this->input('role');
        }

        if ($this->has('is_active') && $this->filled('is_active')) {
            $filters['is_active'] = $this->input('is_active');
        }

        return $filters;
    }

    /**
     * Default sort field
     */
    protected function getDefaultSortField(): string
    {
        return 'created_at';
    }
}
```

### 2. Tạo Service với ResponseListQuery trait

```php
<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\QueryBuilderInterface;
use App\Http\Requests\BaseIndexRequest;
use App\Models\User;
use App\Traits\ResponseListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class UserService
{
    use ResponseListQuery;

    public function __construct(
        private readonly QueryBuilderInterface $queryBuilder
    ) {
    }

    /**
     * Fields để search (dùng cho search parameter)
     */
    protected array $searchFields = ['name', 'email'];

    /**
     * Get paginated users với filters và sorting
     */
    public function getAllUsers(BaseIndexRequest $request): LengthAwarePaginator
    {
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: User::query(), // Có thể dùng callable: fn() => UserRepository::query() hoặc DB::table('users')
            request: $request,
            searchFields: $this->searchFields,
            customFilterCallback: fn (Builder $q, array $f) => $this->applyCustomFilters($q, $f)
        );
    }

    /**
     * Apply custom filters
     */
    private function applyCustomFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query;
    }
}
```

### 3. Controller sử dụng

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\IndexUserRequest;
use App\Http\Resources\UserResource;
use App\Services\User\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

final class UserController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function index(IndexUserRequest $request): JsonResponse
    {
        $users = $this->userService->getAllUsers($request);

        return $this->successResponse(
            data: [
                'users' => UserResource::collection($users->items()),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ],
            message: 'Users retrieved successfully'
        );
    }
}
```

### 4. API Examples

**Get users với pagination:**
```
GET /api/users?page=1&per_page=20
```

**Get users với search:**
```
GET /api/users?search=john
```
(Sẽ search trong các fields: name, email)

**Get users với filter:**
```
GET /api/users?role=admin&is_active=1
```

**Get users với sorting:**
```
GET /api/users?sort_by=name&sort_order=asc
```

**Combine tất cả:**
```
GET /api/users?page=1&per_page=10&search=john&role=admin&sort_by=created_at&sort_order=desc
```

## Lợi ích

1. **DRY (Don't Repeat Yourself)**: Logic pagination, sorting, search được reuse
2. **Consistency**: Tất cả list endpoints có cùng structure
3. **Easy to extend**: Chỉ cần override methods để thêm custom logic
4. **Type-safe**: Sử dụng type hints đầy đủ
5. **Testable**: Dễ test từng phần
6. **SOLID Principles**: Tách biệt concerns, dễ maintain và extend
7. **Flexible Query**: Hỗ trợ cả Eloquent Builder, Query Builder, hoặc callable (repositories, raw queries)

## Lưu ý

- Query có thể là `Builder` instance hoặc `callable` trả về `Builder`
- Hữu ích khi dùng với Repository pattern hoặc raw queries
- `QueryBuilderInterface` được bind tự động trong `AppServiceProvider`

