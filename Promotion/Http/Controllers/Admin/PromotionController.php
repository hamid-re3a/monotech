<?php

namespace Promotion\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Promotion\Http\Requests\Admin\CreatePromotionRequest;
use Promotion\Http\Resources\PromotionCodeResource;
use Promotion\Models\PromotionCode;
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
//    /**
//     * Get Promotion Codes
//     * @group
//     * BackOffice > Promotion
//     */
//    public function index()
//    {
//        return api()->success(trans('promotion.success'), PromotionCodeResource::collection($this->repository->all()));
//    }
//    /**
//     * Get Promotion Code
//     * @group
//     * BackOffice > Promotion
//     */
//    public function show(PromotionCode $promotionCode)
//    {
//        return api()->success(trans('promotion.success'), PromotionCodeResource::make($promotionCode));
//    }


    /**
     * Create New Promotion Code
     * @group
     * BackOffice > Promotion
     * @param CreatePromotionRequest $request
     */
    public function create(CreatePromotionRequest $request)
    {

        $data = $request->all();
        $data['code'] = Str::random(15);
        $promotion = $this->repository->create($data);

        return response()->json(["success"=>true,"data"=> PromotionCodeResource::make($promotion)],201)->setStatusCode(201);
    }
}
