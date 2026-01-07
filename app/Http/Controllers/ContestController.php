<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\UpdateContestRequest;
use App\Models\Contest;
use App\Services\ContestService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

final readonly class ContestController
{
    use ApiResponseTrait;

    public function __construct(
        private ContestService $contestService
    ) {}

    /**
     * @OA\Get(
     *   path="/api/contests",
     *   tags={"Projects"},
     *   summary="Get list of contests",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index(BaseIndexRequest $request): JsonResponse
    {
        $contests = $this->contestService->getAllContests($request);

        return $this->paginatedResponse(
            paginator: $contests,
            message: 'Contests fetched successfully'
        );
    }

    /**
     * @OA\Post(
     *   path="/api/contests",
     *   tags={"Projects"},
     *   summary="Create new contest",
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function store(UpdateContestRequest $contestRequest): JsonResponse
    {
        return $this->successResponse(
            data: $this->contestService->createContest($contestRequest),
            message: 'Project created successfully',
            code: 201
        );
    }

    /**
     * @OA\Get(
     *   path="/api/contests/{id}",
     *   tags={"Projects"},
     *   summary="Get contest detail",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $contest = $this->contestService->getContestDetail($id);

        return $this->successResponse(
            data: $contest,
            message: 'Project fetched successfully'
        );
    }

    /**
     * @OA\Put(
     *   path="/api/contests/{id}",
     *   tags={"Projects"},
     *   summary="Update contest",
     *
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function update(Contest $contest, UpdateContestRequest $contestRequest): JsonResponse
    {
        return $this->successResponse(
            data: $this->contestService->updateContest($contest, $contestRequest),
            message: 'Project updated successfully'
        );
    }


    public function destroy(Contest $contest): JsonResponse
    {
        return $this->successResponse(
            data: $this->contestService->destroy($contest), 
            message: 'Contest deleted successfully'
        );
    }

}
