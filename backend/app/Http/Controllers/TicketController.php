<?php

namespace App\Http\Controllers;

use App\Http\ApiMessages;
use App\Http\ApiResponses;
use App\Http\Controllers\Controller;

use App\UseCases\Ports\ITicketUseCases;

use App\Http\DTO\Requests\QueryRequests;
use App\Http\DTO\Requests\TicketCloseRequest;
use App\Http\DTO\Requests\TicketStoreRequest;
use App\Http\DTO\Requests\TicketAssignExecutorRequest;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class TicketController extends Controller
{
    protected ITicketUseCases $ticketUseCases;

    public function __construct(ITicketUseCases $ticketUseCases)
    {
        $this->ticketUseCases = $ticketUseCases;
    }

    public function findById(int $ticketId): JsonResponse
    {
        $ticket = $this->ticketUseCases->findById($ticketId);

        return ApiResponses::data('', 200, $ticket->toArray());
    }

    public function store(TicketStoreRequest $request): JsonResponse
    {
        $ticket = $this->ticketUseCases->store(Auth::id(), $request->validated());

        return ApiResponses::data('', 201, $ticket->toArray());
    }

    public function findMyTickets(Request $request): JsonResponse
    {
        $paginationResponse = $this->ticketUseCases->findListUser(
            Auth::id(), 
            QueryRequests::validatePagination($request),
        );

        return ApiResponses::data(
            '',
            200, 
            $paginationResponse->itemsToArray(), 
            $paginationResponse->metaToArray(),
        );
    }

    public function findMyOperatorTickets(Request $request): JsonResponse
    {
        $paginationResponse = $this->ticketUseCases->findListOperator(
            Auth::id(), 
            QueryRequests::validatePagination($request),
        );

        return ApiResponses::data(
            '',
            200, 
            $paginationResponse->itemsToArray(), 
            $paginationResponse->metaToArray(),
        );
    }

    public function findNewTickets(Request $request): JsonResponse
    {
        $paginationResponse = $this->ticketUseCases->findListNew(
            QueryRequests::validatePagination($request),
        );

        return ApiResponses::data(
            '',
            200, 
            $paginationResponse->itemsToArray(), 
            $paginationResponse->metaToArray(),
        );
    }

    public function assignExecutor(TicketAssignExecutorRequest $request, int $ticketId): JsonResponse
    {
        $this->ticketUseCases->assignExecutor($ticketId, $request->validated());

        return ApiResponses::message(
            ApiMessages::EXECUTOR_ASSIGN,
            200, 
        );
    }

    public function closeTicket(TicketCloseRequest $request, int $ticketId): JsonResponse
    {
        $this->ticketUseCases->closeTicket($ticketId, $request->validated());

        return ApiResponses::message(
            ApiMessages::TICKET_CLOSED,
            200, 
        );
    }
}
