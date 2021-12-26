<?php

namespace User\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Http\Resources\AgentResource;
use User\Http\Resources\Auth\ProfileResource;
use User\Http\Resources\IpResource;
use User\Models\Agent;
use User\Models\Ip;
use User\Models\User;

class LoginHistoryResource extends JsonResource
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
            'ip'=>(!is_null($this->ip_id))?IpResource::make(Ip::find($this->ip_id)):null,
            'agent'=>(!is_null($this->agent_id))?AgentResource::make(Agent::find($this->agent_id)):null,
            'login_status'=>$this->login_status_string,
            'is_from_new_device'=>$this->is_from_new_device,
            'created_at'=>$this->created_at->timestamp,
        ];
    }
}
