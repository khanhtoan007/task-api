<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface QueryBuilderInterface
{
    /**
     * Apply search to query builder
     *
     * @param Builder $query
     * @param string $search
     * @param array<string> $searchFields
     * @return Builder
     */
    public function applySearch(Builder $query, string $search, array $searchFields): Builder;

    /**
     * Apply filters to query builder
     *
     * @param Builder $query
     * @param array<string, mixed> $filters
     * @param callable|null $customFilterCallback
     * @return Builder
     */
    public function applyFilters(Builder $query, array $filters, ?callable $customFilterCallback = null): Builder;

    /**
     * Apply sorting to query builder
     *
     * @param Builder $query
     * @param string $sortBy
     * @param string $sortOrder
     * @return Builder
     */
    public function applySorting(Builder $query, string $sortBy, string $sortOrder): Builder;

    /**
     * Apply pagination to query builder
     *
     * @param Builder $query
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function applyPagination(Builder $query, int $perPage, int $page): LengthAwarePaginator;

    /**
     * Build complete query with all features
     *
     * @param Builder|callable(): Builder $query Query builder instance or callable that returns Builder
     * @param array<string, mixed> $filters
     * @param array<string> $searchFields
     * @param string $sortBy
     * @param string $sortOrder
     * @param int $perPage
     * @param int $page
     * @param callable|null $customFilterCallback
     * @return LengthAwarePaginator
     */
    public function build(
        Builder|callable $query,
        array $filters,
        array $searchFields,
        string $sortBy,
        string $sortOrder,
        int $perPage,
        int $page,
        ?callable $customFilterCallback = null
    ): LengthAwarePaginator;
}

