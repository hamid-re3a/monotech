<?php

namespace User\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\Auth\ProfileResource;
use User\Models\User;

class PasswordHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'actor' => !is_null($this->actor_id) ? ProfileResource::make(User::find($this->actor_id)) : null,
            'created_at' => $this->created_at->timestamp,
        ];
    }
}
