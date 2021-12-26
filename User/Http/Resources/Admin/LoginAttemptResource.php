<?php

namespace User\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginAttemptResource extends JsonResource
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
            'id' => $this->id,
            'times' => $this->times,
            'duration' => $this->duration,
            'priority' => $this->priority,
            'blocking_duration' => $this->blocking_duration,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp
        ];
    }
}
