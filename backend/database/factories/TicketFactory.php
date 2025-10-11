<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['new', 'open', 'closed']);

        $executor = null;

        if ($status !== 'new') {
            $executor = $this->faker->boolean(70) ? User::factory() : null;
        }

        return [
            'type_id' => TicketType::factory(),
            'status' => $status,
            'customer_id' => User::factory(),
            'executor_id' => $executor,
            'description' => $this->faker->paragraph(3),
        ];
    }
}
