<?php

declare(strict_types=1);

namespace App\Traits;

use App\Contracts\QueryBuilderInterface;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait ResponseListQuery
{
    /**
     * Build paginated query with filters, search, sorting
     *
     * @param  Builder|callable(): Builder  $query
     * @param  array<string>  $searchFields
     * @param  array<string, mixed>  $customFilters  Custom filters để bổ sung vào query (status, etc.)
     * @param  callable(Builder, array): Builder|null  $customFilterCallback
     */
    protected function paginateWithQueryBuilder(
        QueryBuilderInterface $queryBuilder,
        Builder|callable $query,
        BaseIndexRequest $request,
        array $searchFields,
        array $customFilters = [],
        ?callable $customFilterCallback = null
    ): LengthAwarePaginator {
        // Merge base filters (search) với custom filters từ service
        $filters = array_merge($request->getFilters(), $customFilters);

        return $queryBuilder->build(
            query: $query,
            filters: $filters,
            searchFields: $searchFields,
            sortBy: $request->getSorting()['sort_by'],
            sortOrder: $request->getSorting()['sort_order'],
            perPage: $request->getPagination()['per_page'],
            page: $request->getPagination()['page'],
            customFilterCallback: $customFilterCallback
        );
    }
}
