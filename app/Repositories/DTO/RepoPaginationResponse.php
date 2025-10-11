<?php

namespace App\Repositories\DTO;

use Illuminate\Support\Collection;

class RepoPaginationResponse
{
    private Collection $items;

    private int $total;

    public function __construct(int $total, Collection $items)
    {
        $this->total = $total;
        $this->items = $items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getItemsArray(): array
    {
        return $this->items->toArray();
    }
}
