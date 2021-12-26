<?php
namespace User\Repository;

use User\Models\Translate;

class TranslateRepository
{
    private $entity_name = Translate::class;

    public function list()
    {
        $gateway_service_entity = new $this->entity_name;
        return $gateway_service_entity->whereNotNull('value')->simplePaginate();
    }
}
