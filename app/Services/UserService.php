<?php

namespace App\Services;

use App\Contracts\QueryBuilderInterface;
use App\Exceptions\ApiException;
use App\Http\Enums\ProjectRoleEnum;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\AssignUserRequest;
use App\Http\Requests\CreateUserContestRequest;
use App\Http\Requests\ProjectRequest;
use App\Http\Requests\UpdateUserContestRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Traits\ResponseListQuery;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

final class UserService
{
    use ResponseListQuery;

    public function __construct(
        private readonly QueryBuilderInterface $queryBuilder
    ) {}

    public function getAllUsers(BaseIndexRequest $request): LengthAwarePaginator
    {
        $query = User::query(['name', 'dob', 'sex','phone','contest'])
        ->whereNotNull('contest')
        ->with(['contest:id,title,contest_address,range']);
        return $this->paginateWithQueryBuilder(
            queryBuilder: $this->queryBuilder,
            query: $query,
            request: $request,
            searchFields: ['name', 'email'],
            customFilters: []
        );
    }

    public function getUserDetail(string $projectId): array
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

    public function createUserContest(CreateUserContestRequest $userRequest)
    {
        try {
            DB::transaction(function () use ($userRequest) {
                $user = User::query()->create([
                    'password' => Hash::make('123123123'),
                    'email' => $userRequest->email,
                    'name' => $userRequest->name,
                    'sex' => $userRequest->sex,
                    'phone' => $userRequest->phone,
                    'dob' => $userRequest->dob,
                    'contest' => $userRequest->contest,
                ]);

                return $user;
            });
        } catch (Exception $e) {
            Log::error('Failed to create project: '.$e->getMessage());
            throw new ApiException('Failed to create project', 500);
        }
    }

    public function destroy(string $id): bool
    {
        try{
            $user = User::query()->findOrFail($id);
            $user->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete user: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateUser(User $user, UpdateUserContestRequest $userRequest)
    {
        try {
            $user->update([
                'name' => $userRequest->name,
                // 'email' => $userRequest->email,
                'sex' => $userRequest->sex,
                'phone' => $userRequest->phone,
                'dob' => $userRequest->dob,
                'contest' => $userRequest->contest,
            ]);
            return $user->refresh();
        } catch (Exception $e) {
            Log::error('Failed to update user: '.$e->getMessage());
            throw new ApiException('Failed to update user', 500);
        }
    }
}
