<?php

namespace App\Services;

use App\Contracts\QueryBuilderInterface;
use App\Exceptions\ApiException;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\UpdateContestRequest;
use App\Models\Project;
use App\Models\Contest;
use App\Traits\ResponseListQuery;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ContestService
{
    use ResponseListQuery;

    public function __construct(
        private readonly QueryBuilderInterface $queryBuilder
    ) {}

    public function getAllContests(BaseIndexRequest $request): LengthAwarePaginator
    {
        $query = Contest::query();
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: $query,
            request: $request,
            searchFields: ['title'],
            customFilters: []
        );
    }

    public function getContestDetail(string $projectId): array
    {
        return Project::query()
            ->with('tasks')
            ->with([
                'projectMembers' => function ($query): void {
                    $query->select('id', 'project_id', 'user_id', 'role', 'joined_at', 'invited_by');
                },
                'projectMembers.user' => function ($query): void {
                    $query->select('id', 'name');
                },
            ])
            ->findOrFail($projectId)
            ->toArray();
    }

    public function createContest(UpdateContestRequest $updateContest)
    {
        try {
            DB::transaction(function () use ($updateContest) {
                $contest = Contest::query()->create([
                    'title' => $updateContest->title,
                    'contest_address' => $updateContest->contest_address,
                    'range' => $updateContest->range,
                ]);

                return $contest;
            });
        } catch (Exception $e) {
            Log::error('Failed to create contest: '.$e->getMessage());
            throw new ApiException('Failed to create contest', 500);
        }
    }


    public function destroy(Contest $contest): bool
    {
        try{
            $contest->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete contest: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateContest(Contest $contest, UpdateContestRequest $contestRequest)
    {
        try {
            $contest->update($contestRequest->toArray());
            return $contest->refresh();
        } catch (Exception $e) {
            Log::error('Failed to update contest: '.$e->getMessage());
            throw new ApiException('Failed to update contest', 500);
        }
    }
}
