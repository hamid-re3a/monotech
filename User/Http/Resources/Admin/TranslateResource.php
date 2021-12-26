<?php

namespace User\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslateResource extends JsonResource
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
            'key' => $this->key,
            'value' => $this->value
        ];
    }
}
