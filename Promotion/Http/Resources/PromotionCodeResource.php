<?php

namespace Promotion\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'code'=>$this->code,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'amount'=>$this->amount,
            'quota'=>$this->quota,
        ];
    }
}
