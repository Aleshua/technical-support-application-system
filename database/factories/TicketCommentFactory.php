<?php

namespace Database\Factories;

use App\Models\TicketComment;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketCommentFactory extends Factory
{
    protected $model = TicketComment::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'text' => $this->faker->sentence(12),
        ];
    }
}
