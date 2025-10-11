<?php

namespace App\Repositories\Ports;

use App\Models\Ticket;
use App\Models\TicketComment;

use App\Repositories\DTO\RepoPaginationRequest;

use App\Repositories\DTO\RepoPaginationResponse;

interface ITicketRepository
{
    public function findById(int $id): Ticket;
    public function findCommentByTicketId(int $id): TicketComment;
    public function save(array $data): Ticket;
    public function saveComment(int $ticketId, array $comment): TicketComment;
    public function update(int $ticketId, array $data): void;
    public function findListUser(int $userId, RepoPaginationRequest $pagination): RepoPaginationResponse;
    public function findListOperator(int $operatorId, RepoPaginationRequest $pagination): RepoPaginationResponse;
    public function findListNew(RepoPaginationRequest $pagination): RepoPaginationResponse;
}
