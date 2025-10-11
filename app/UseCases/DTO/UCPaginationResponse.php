<?php

namespace App\UseCases\DTO;

use App\Repositories\DTO\RepoPaginationRequest;
use App\Repositories\DTO\RepoPaginationResponse;

class UCPaginationResponse
{
    private array $items;
    private int $total;
    private int $currentPage;
    private int $totalPages;

    public function __construct(
        RepoPaginationRequest $request,
        RepoPaginationResponse $response,
        array $changedItems = null,
    ) {
        $limit = $request->getLimit();
        $offset = $request->getOffset();
        $total = $response->getTotal();
        $items = $changedItems = null ? $response->getItems() : $changedItems;

        $this->total = $total;
        $this->items = $items;
        $this->currentPage = (int) floor($offset / $limit) + 1;
        $this->totalPages = (int) ceil($total / $limit);
    }

    public function itemsToArray(): array
    {
        return $this->items;
    }

    public function metaToArray(): array
    {
        return [
            "total" => $this->total,
            "currentPage" => $this->currentPage,
            "totalPages" => $this->totalPages,
        ];
    }
}
