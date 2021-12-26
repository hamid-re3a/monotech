<?php

namespace User\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'value' => $this->value,
            'description' => $this->description,
            'category' => $this->category,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp
        ];
    }
}
