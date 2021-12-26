<?php

namespace Promotion\Http\Controllers\Customer;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Promotion\Http\Requests\Admin\AssignPromotionRequest;
use Promotion\Http\Resources\PromotionCodeResource;
use Promotion\Repository\PromotionCodeRepository;


class PromotionController extends Controller
{

    /**
     * @var PromotionCodeRepository
     */
    private $repository;

    public function __construct(PromotionCodeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Assign Promotion Code
     * @group
     * Customer > Promotion
     * @param AssignPromotionRequest $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function create(AssignPromotionRequest $request)
    {

        $promotion = $this->repository->findByField('code', $request->code);
        DB::beginTransaction();
        try {
            if ($promotion->assignees()->count() >= $promotion->quota) {
                return response()->json(["success" => false], 400)->setStatusCode(400);
            }
            $promotion->assignee()->attach(auth()->user()->id);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error(__CLASS__ . "@" . __METHOD__ . "=>" . $exception->getMessage());
        }


        return response()->json(["success" => true], 200)->setStatusCode(200);
    }
}
