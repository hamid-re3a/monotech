<?php
namespace User\Repository;

use User\Models\EmailContentSetting;

class EmailContentRepository
{
    private $entity_model = EmailContentSetting::class;
    private $service_entity;

    public function __construct()
    {
        $this->service_entity = new $this->entity_model;
    }

    public function lists()
    {
        return $this->service_entity->query()->get();
    }

    public function update($key, $fields)
    {
        $setting = $this->service_entity->query()->where('key', $key)->first();
        if(!$setting)
            throw new \Exception(trans('user.responses.invalid-email-key'));

        $setting->update([
            'is_active' => isset($fields['is_active']) ? $fields['is_active'] : $setting->value,
            'subject' => isset($fields['subject']) ? $fields['subject'] : $setting->subject,
            'from' => isset($fields['from']) ? $fields['from'] : $setting->from,
            'from_name' => isset($fields['from_name']) ? $fields['from_name'] : $setting->from_name,
            'body' => isset($fields['body']) ? $fields['body'] : $setting->body,
            'variables' => isset($fields['variables']) ? $fields['variables'] : $setting->variables,
            'type' => isset($fields['type']) ? $fields['type'] : $setting->type,
        ]);

        return $setting->fresh();
    }
}
