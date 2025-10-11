<?php

namespace App\Repositories\Adapters;

use App\Models\TicketType;
use App\Repositories\Ports\ITicketTypeRepository;

class TicketTypeRepository implements ITicketTypeRepository
{
    public function findByIds(array $ids): array
    {
        $types = TicketType::whereIn('id', array_unique($ids))->get()->keyBy('id');
        return array_map(fn($id) => $types[$id] ?? null, $ids);
    }

}
