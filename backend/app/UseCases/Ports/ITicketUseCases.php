<?php

namespace App\UseCases\Ports;

use App\UseCases\DTO\CreateResponse;
use App\UseCases\DTO\TicketFullResponse;
use App\UseCases\DTO\UCPaginationResponse;

interface ITicketUseCases
{
    public function findById(int $id): TicketFullResponse;
    public function store(int $customerId, array $data): CreateResponse;
    public function findListUser(int $userId, array $pagination): UCPaginationResponse;
    public function findListOperator(int $operatorid, array $pagination): UCPaginationResponse;
    public function findListNew(array $pagination): UCPaginationResponse;
    public function assignExecutor(int $ticketId, array $operator): void;
    public function closeTicket(int $ticketId, array $comment): void;
}
