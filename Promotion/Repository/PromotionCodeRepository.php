<?php


namespace Promotion\Repository;


use Prettus\Repository\Eloquent\BaseRepository;
use Promotion\Models\PromotionCode;

class PromotionCodeRepository  extends BaseRepository
{

    /**
     * @inheritDoc
     */
    public function model()
    {
        return PromotionCode::class;
    }
}
