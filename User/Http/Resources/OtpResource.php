<?php

namespace User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\Auth\ProfileResource;
use User\Models\Agent;
use User\Models\Ip;
use User\Models\User;

class OtpResource extends JsonResource
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
            'user'=>(!is_null($this->user_id))?ProfileResource::make(User::find($this->user_id)): null,
            'ip'=>(!is_null($this->ip_id))?IpResource::make(Ip::find($this->ip_id)):null,
            'agent'=>(!is_null($this->agent_id))?AgentResource::make(Agent::find($this->agent_id)):null,
            'type'=>$this->type,
            'created_at'=>$this->created_at->timestamp,
        ];
    }
}
