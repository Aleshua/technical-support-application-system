<?php

namespace App\UseCases\Adapters;

use App\UseCases\DTO\CreateResponse;
use App\UseCases\DTO\TicketFullResponse;
use App\UseCases\DTO\TicketShortResponse;
use App\UseCases\DTO\UCPaginationResponse;

use App\UseCases\Ports\ITicketUseCases;

use App\Repositories\DTO\RepoPaginationRequest;

use App\Repositories\Ports\ITicketRepository;
use App\Repositories\Ports\ITicketTypeRepository;

use Illuminate\Support\Facades\DB;

class TicketUseCases implements ITicketUseCases
{
    protected ITicketRepository $ticketRepository;
    protected ITicketTypeRepository $ticketTypeRepository;

    public function __construct(ITicketRepository $ticketRepository, ITicketTypeRepository $ticketTypeRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
    }

    public function findById(int $id): TicketFullResponse
    {
        $ticket = $this->ticketRepository->findById($id);

        return new TicketFullResponse($ticket, $ticket->type, $ticket->comment);
    }

    public function store(int $customerId, array $data): CreateResponse
    {
        $data['customer_id'] = $customerId;

        $ticket = $this->ticketRepository->save($data);
        return new CreateResponse($ticket->id);
    }

    public function findListUser(int $userId, array $pagination): UCPaginationResponse
    {
        $repoPaginationRequest = new RepoPaginationRequest($pagination);

        $repoPaginationResponse = $this->ticketRepository->findListUser(
            $userId,
            $repoPaginationRequest,
        );
        $types = $this->ticketTypeRepository->findByIds(
            $repoPaginationResponse->getItems()->pluck('type_id')->toArray()
        );

        $result = [];

        $items = $repoPaginationResponse->getItems();
        for ($i = 0; $i < count($items); $i++) {
            $result[$i] = (new TicketShortResponse($items[$i], $types[$i]))->toArray();
        }

        return new UCPaginationResponse(
            $repoPaginationRequest,
            $repoPaginationResponse,
            $result,
        );
    }

    public function findListOperator(int $operatorid, array $pagination): UCPaginationResponse
    {
        $repoPaginationRequest = new RepoPaginationRequest($pagination);

        $repoPaginationResponse = $this->ticketRepository->findListOperator(
            $operatorid,
            $repoPaginationRequest,
        );
        $types = $this->ticketTypeRepository->findByIds(
            $repoPaginationResponse->getItems()->pluck('type_id')->toArray()
        );

        $result = [];

        $items = $repoPaginationResponse->getItems();
        for ($i = 0; $i < count($items); $i++) {
            $result[$i] = (new TicketShortResponse($items[$i], $types[$i]))->toArray();
        }

        return new UCPaginationResponse(
            $repoPaginationRequest,
            $repoPaginationResponse,
            $result,
        );
    }

    public function findListNew(array $pagination): UCPaginationResponse
    {
        $repoPaginationRequest = new RepoPaginationRequest($pagination);

        $repoPaginationResponse = $this->ticketRepository->findListNew(
            $repoPaginationRequest,
        );
        $types = $this->ticketTypeRepository->findByIds(
            $repoPaginationResponse->getItems()->pluck('type_id')->toArray()
        );

        $result = [];

        $items = $repoPaginationResponse->getItems();
        for ($i = 0; $i < count($items); $i++) {
            $result[$i] = (new TicketShortResponse($items[$i], $types[$i]))->toArray();
        }

        return new UCPaginationResponse(
            $repoPaginationRequest,
            $repoPaginationResponse,
            $result,
        );
    }

    public function assignExecutor(int $ticketId, array $operator): void
    {
        $this->ticketRepository->update($ticketId, [
            "executor_id" => $operator['operatorId'],
            'status' => 'open',
        ]);
    }

    public function closeTicket(int $ticketId, array $comment): void
    {
        DB::transaction(function () use ($ticketId, $comment) {
            $this->ticketRepository->update($ticketId, ["status" => 'closed']);

            if (isset($comment['text'])) {
                $this->ticketRepository->saveComment($ticketId, $comment);
            }
        });
    }
}
