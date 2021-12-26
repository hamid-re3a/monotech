<?php

namespace User\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailContentResource extends JsonResource
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
            'subject' => $this->subject,
            'body' => $this->body,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp
        ];
    }
}
