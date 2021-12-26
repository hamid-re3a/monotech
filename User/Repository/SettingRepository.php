<?php
namespace User\Repository;

use User\Models\Setting;

class SettingRepository
{
    private $entity_model = Setting::class;
    private $service_entity;

    public function __construct()
    {
        $this->service_entity = new $this->entity_model;
    }

    public function lists()
    {
        return $this->service_entity->query()->whereNotNull('value')->get();
    }

    public function update($key, $fields)
    {
        $setting = $this->service_entity->query()->where('key', $key)->first();
        if(!$setting)
            throw new \Exception(trans('user.responses.invalid-setting-key'));

        $setting->update([
            'value' => isset($fields['value']) ? $fields['value'] : $setting->value,
            'description' => isset($fields['description']) ? $fields['description'] : $setting->description,
            'category' => isset($fields['category']) ? $fields['category'] : $setting->category,
        ]);

        return $setting->fresh();
    }
}
