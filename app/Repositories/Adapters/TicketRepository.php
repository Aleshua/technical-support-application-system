<?php

namespace App\Repositories\Adapters;

use Log;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Repositories\DTO\RepoPaginationRequest;
use App\Repositories\DTO\RepoPaginationResponse;
use App\Repositories\Ports\ITicketRepository;

use Illuminate\Support\Facades\Gate;

class TicketRepository implements ITicketRepository
{
    public function findById($id): Ticket
    {
        $ticket = Ticket::find($id);

        Gate::authorize('related', $ticket);

        return $ticket;
    }

    public function findCommentByTicketId(int $id): TicketComment
    {
        return TicketComment::where('ticket_id', $id)->first();
        ;
    }

    public function save(array $data): Ticket
    {
        $ticket = new Ticket($data);

        Gate::authorize('save', $ticket);

        $ticket->save();

        return $ticket;
    }

    public function saveComment(int $ticketId, array $comment): TicketComment
    {
        $comment['ticket_id'] = $ticketId;

        $ticketComment = new TicketComment($comment);

        $ticketComment->save();

        return $ticketComment;
    }

    public function update(int $ticketId, array $data): void
    {
        $ticket = Ticket::findOrFail($ticketId);

        Gate::authorize('related', $ticket);

        $ticket->update($data);
    }

    public function findListUser(int $userId, RepoPaginationRequest $pagination): RepoPaginationResponse
    {
        Gate::authorize('me', [Ticket::class, $userId]);

        $query = Ticket::where('customer_id', $userId);

        $total = $query->count();

        $items = $query
            ->limit($pagination->getLimit())
            ->offset($pagination->getOffset())
            ->get();

        return new RepoPaginationResponse($total, $items);
    }

    public function findListOperator(int $operatorId, RepoPaginationRequest $pagination): RepoPaginationResponse
    {
        Gate::authorize('operator', [Ticket::class]);
        Gate::authorize('me', [Ticket::class, $operatorId]);
        
        $query = Ticket::where('executor_id', $operatorId);

        $total = $query->count();

        $items = $query
            ->limit($pagination->getLimit())
            ->offset($pagination->getOffset())
            ->get();

        return new RepoPaginationResponse($total, $items);
    }

    public function findListNew(RepoPaginationRequest $pagination): RepoPaginationResponse
    {
        Gate::authorize('operator', [Ticket::class]);

        $query = Ticket::where('status', 'new');

        $total = $query->count();

        $items = $query
            ->limit($pagination->getLimit())
            ->offset($pagination->getOffset())
            ->get();

        return new RepoPaginationResponse($total, $items);
    }
}
