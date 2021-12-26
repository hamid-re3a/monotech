<?php

namespace User\Observers;

use User\Mail\User\UserAccountActivatedEmail;
use User\Jobs\EmailJob;
use User\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        if(empty($user->member_id)) {
            //User member_id field
            $member_id = mt_rand(121212121,999999999);
            while ($user->where('member_id', $member_id)->count())
                $member_id = mt_rand(121212121,999999999);
            $user->member_id = $member_id;
        }
    }

    public function updating(User $user)
    {
        if(auth()->check()) {
            $data = $user->getOriginal();
            $attributes = array_merge($data,[
                'actor_id' => request()->user()->id,
                'user_id' => $data['id']
            ]);
            $history = $user->userHistories()->create($attributes);


            if($user->isDirty('block_type')){

                if($data['block_type'] != USER_BLOCK_TYPE_AUTOMATIC)
                    EmailJob::dispatch(new UserAccountActivatedEmail($user,$history),$user->email);

            }
        }
    }


}
