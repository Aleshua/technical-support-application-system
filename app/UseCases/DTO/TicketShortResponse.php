<?php

namespace App\UseCases\DTO;

use App\Models\Ticket;
use App\Models\TicketType;


class TicketShortResponse
{
    private string $type;
    private string $status;
    private int $customerId;

    public function __construct(Ticket $ticket, TicketType $type)
    {
        $this->type = $type->label;
        $this->status = $ticket->status;
        $this->customerId = $ticket->customer_id;
    }

    public function toArray(): array {
        return [
            "type"=> $this->type,
            "status"=> $this->status,
            "customerId"=> $this->customerId
        ];
    }
}
