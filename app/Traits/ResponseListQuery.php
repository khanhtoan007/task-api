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
     * @param QueryBuilderInterface $queryBuilder
     * @param Builder|callable(): Builder $query
     * @param BaseIndexRequest $request
     * @param array<string> $searchFields
     * @param callable(Builder, array): Builder|null $customFilterCallback
     * @return LengthAwarePaginator
     */
    protected function paginateWithQueryBuilder(
        QueryBuilderInterface $queryBuilder,
        Builder|callable $query,
        BaseIndexRequest $request,
        array $searchFields,
        ?callable $customFilterCallback = null
    ): LengthAwarePaginator {
        return $queryBuilder->build(
            query: $query,
            filters: $request->getFilters(),
            searchFields: $searchFields,
            sortBy: $request->getSorting()['sort_by'],
            sortOrder: $request->getSorting()['sort_order'],
            perPage: $request->getPagination()['per_page'],
            page: $request->getPagination()['page'],
            customFilterCallback: $customFilterCallback
        );
    }
}

