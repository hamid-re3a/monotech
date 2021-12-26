<?php

namespace User\Http\Resources\General;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileDetailsResource extends JsonResource
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
            'member_id' => $this->member_id,
            'full_name' => $this->full_name,
        ];
    }
}
