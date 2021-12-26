<?php

namespace User\Repository;

use Illuminate\Database\Eloquent\Builder;
use User\Models\User;
use User\Models\UserActivity;

class ActivityRepository
{
    private $entity_name = UserActivity::class;

    public function getPaginated(int $user_id = null)
    {

        /**@var $activities UserActivity*/
        $activities = new $this->entity_name;
        $activities = $activities->query()->orderByDesc('id')->with([
            'user',
            'ip',
            'agent'
        ]);
        if(!empty($user_id))
            $activities->where('user_id','=', $user_id);

        return $activities->paginate();

    }

}
