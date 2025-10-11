<?php

namespace App\UseCases\DTO;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketType;


class TicketFullResponse
{
    private int $id;
    private string $type;
    private string $status;
    private int $customerId;
    private ?int $executorId;
    private string $description;
    private string $createdAt;
    private string $updatedAt;

    private ?string $comment;

    public function __construct(Ticket $ticket, TicketType $type, ?TicketComment $ticketComment)
    {
        $this->type = $type->label;
        $this->status = $ticket->status;
        $this->customerId = $ticket->customer_id;
        $this->executorId = $ticket->executor_id;
        $this->description = $ticket->description;
        $this->createdAt = $ticket->created_at->toIso8601String();
        $this->updatedAt = $ticket->updated_at->toIso8601String();
        $this->comment = $ticketComment === null ? null: $ticketComment->text;
    }

    public function toArray(): array
    {
        return [
            "type" => $this->type,
            "status" => $this->status,
            "customer_id" => $this->customerId,
            "executor_id" => $this->executorId,
            "description" => $this->description,
            "created_at" => $this->createdAt,
            "updated_at" => $this->updatedAt,
            "comment" => [
                "text" => $this->comment,
            ]
        ];
    }
}
