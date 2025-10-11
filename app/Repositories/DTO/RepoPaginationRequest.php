<?php

namespace App\Repositories\DTO;

class RepoPaginationRequest
{
    private int $limit;
    private int $offset;

    public function __construct(array $pagination)
    {
        $limit = isset($pagination['limit']) ? (int)$pagination['limit'] : 10;
        $page = isset($pagination['page']) ? (int)$pagination['page'] : 1;

        if ($limit < 1) {
            $limit = 10;
        }

        if ($page < 1) {
            $page = 1;
        }

        $this->limit = $limit;
        $this->offset = ($page - 1) * $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
