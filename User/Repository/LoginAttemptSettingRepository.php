<?php
namespace User\Repository;

use phpseclib3\Math\PrimeField\Integer;
use User\Models\LoginAttemptSetting;

class LoginAttemptSettingRepository
{
    private $entity_model = LoginAttemptSetting::class;
    private $service_entity;

    public function __construct()
    {
        $this->service_entity = new $this->entity_model;
    }

    public function lists()
    {
        return $this->service_entity->query()->orderBy('priority')->get();
    }

    public function store(array $fields)
    {

        if(!isset($fields['times']) OR !isset($fields['duration']) OR !isset($fields['priority']) OR !isset($fields['blocking_duration']))
            throw new \Exception(trans('user.responses.global-error'));

        return $this->service_entity->query()->create([
            'times' => $fields['times'],
            'duration' => $fields['duration'],
            'priority' => isset($fields['priority']) ? $fields['priority'] : 0,
            'blocking_duration' => $fields['blocking_duration'],
        ]);

    }

    public function update($id, array $fields)
    {
        $setting = $this->service_entity->query()->where('id', $id)->first();
        if(!$setting)
            throw new \Exception(trans('user.responses.invalid-login-attempt-id'));

        $setting->update([
            'times' => isset($fields['times']) ? $fields['times'] : $setting->times,
            'duration' => isset($fields['duration']) ? $fields['duration'] : $setting->duration,
            'priority' => isset($fields['priority']) ? $fields['priority'] : $setting->priority,
            'blocking_duration' => isset($fields['blocking_duration']) ? $fields['category'] : $setting->blocking_duration,
        ]);

        return $setting->fresh();
    }

    public function delete($id)
    {
        $setting = $this->service_entity->query()->where('id', $id)->first();
        if(!$setting)
            throw new \Exception(trans('user.responses.invalid-login-attempt-id'));

        $setting->delete();

        return true;
    }
}
