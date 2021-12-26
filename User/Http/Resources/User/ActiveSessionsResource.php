<?php

namespace User\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ActiveSessionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $latestIp = $this->ips()->latest()->first();

        return [
            'id' => $this->id,
            'ip'=> (!empty($latestIp) AND !empty($latestIp->ip)) ? $latestIp->ip : 'Unknown',
            'country'=> (!empty($latestIp) AND !empty($latestIp->country)) ? $latestIp->country : 'Unknown',
            'state'=> (!empty($latestIp) AND !empty($latestIp->state)) ? $latestIp->state : 'Unknown',
            'device'=> !empty($this->device_type) ? $this->device_type : 'Unknown',
            'browser'=> !empty($this->browser) ? $this->browser . ' ' . $this->browser_version : 'Unknown',
            'location' => [
                'latitude' => (!empty($latestIp) AND !empty($latestIp->lat)) ? $latestIp->lat : 'Unknown',
                'longitude' => (!empty($latestIp) AND !empty($latestIp->lon)) ? $latestIp->lon : 'Unknown',
            ],
            'recent_activity' => $this->updated_at->timestamp
        ];
    }
}
