<?php

namespace Promotion\Http\Controllers\Customer;


use Illuminate\Routing\Controller;
use Promotion\Http\Requests\Customer\AssignPromotionRequest;
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
    public function assign(AssignPromotionRequest $request)
    {
        $promotion = $this->repository->findByField('code',$request->code)->first();

        if ($promotion->assignee()->where('user_id',auth()->user()->id)->exists() || $promotion->assignee()->count() >= $promotion->quota) {
            return response()->json(["success" => false], 400)->setStatusCode(400);
        }
        $promotion->assignee()->attach(auth()->user()->id);
        auth()->user()->deposit($promotion->amount);
        return response()->json(["success" => true], 200)->setStatusCode(200);
    }
}
