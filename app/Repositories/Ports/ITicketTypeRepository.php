<?php

namespace App\Repositories\Ports;

interface ITicketTypeRepository
{
    public function findByIds(array $ids): array;
}
