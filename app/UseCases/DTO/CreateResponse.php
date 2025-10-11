<?php

namespace App\UseCases\DTO;

class CreateResponse
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function toArray(): array {
        return [
            "id"=> $this->id,
        ];
    }
}
