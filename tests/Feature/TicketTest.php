<?php

namespace Tests\Feature\Auth;

use App\Models\Ticket;

use App\Http\ApiMessages;

use App\Models\TicketType;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function find_ticket_success()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(200)->assertJson([
            'data' => [
                "type" => $ticket->type->label,
                "status" => $ticket->status,
                "customer_id" => $ticket->customer_id,
                "executor_id" => $ticket->executor_id,
                "description" => $ticket->description,
                "created_at" => $ticket->created_at->toIso8601String(),
                "updated_at" => $ticket->updated_at->toIso8601String(),
                "comment" => [
                    "text" => $ticket->comment === null ? null : $ticket->comment->text,
                ]
            ]
        ]);
    }

    #[Test]
    public function find_ticket_not_related()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $user->id]);

        $this->actingAs($user2);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function find_operator_ticket_success()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['executor_id' => $operator->id]);

        $this->actingAs($operator);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(200)->assertJson([
            'data' => [
                "type" => $ticket->type->label,
                "status" => $ticket->status,
                "customer_id" => $ticket->customer_id,
                "executor_id" => $ticket->executor_id,
                "description" => $ticket->description,
                "created_at" => $ticket->created_at->toIso8601String(),
                "updated_at" => $ticket->updated_at->toIso8601String(),
                "comment" => [
                    "text" => $ticket->comment === null ? null : $ticket->comment->text,
                ]
            ]
        ]);
    }

    #[Test]
    public function find_operator_ticket_not_related()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $operator2 = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status' => 'open', 'executor_id' => $operator2->id]);

        $this->actingAs($operator);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function find_operator_ticket_new()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status' => 'new', 'executor_id' => null]);

        $this->actingAs($operator);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(200)->assertJson([
            'data' => [
                "type" => $ticket->type->label,
                "status" => $ticket->status,
                "customer_id" => $ticket->customer_id,
                "executor_id" => $ticket->executor_id,
                "description" => $ticket->description,
                "created_at" => $ticket->created_at->toIso8601String(),
                "updated_at" => $ticket->updated_at->toIso8601String(),
                "comment" => [
                    "text" => $ticket->comment === null ? null : $ticket->comment->text,
                ]
            ]
        ]);
    }

    #[Test]
    public function user_can_store_ticket()
    {
        $user = User::factory()->create();
        $ticketType = TicketType::factory()->create();

        $this->actingAs($user);

        $data = [
            'type_id' => $ticketType->id,
            'description' => 'description',
        ];

        $response = $this->post("/tickets", $data);

        $response->assertStatus(201)->assertJsonStructure([
            'data' => ['id']
        ]);

        $ticketId = $response->json('data.id');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticketId,
            'type_id' => $ticketType->id,
        ]);
    }

    #[Test]
    public function operator_cannot_store_ticket()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticketType = TicketType::factory()->create();

        $this->actingAs($operator);

        $data = [
            'type_id' => $ticketType->id,
            'description' => 'description',
        ];

        $response = $this->post("/tickets", $data);

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_can_become_executor()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status' => 'new', 'executor_id' => null]);

        $this->actingAs($operator);

        $data = [
            'operatorId' => $operator->id,
        ];

        $response = $this->post("/tickets/{$ticket->id}/executor", $data);

        $response->assertStatus(200)->assertJson([
            'message' => ApiMessages::EXECUTOR_ASSIGN,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'executor_id' => $operator->id,
            'status' => 'open',
        ]);
    }

    #[Test]
    public function user_cannot_become_executor()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['status' => 'new', 'executor_id' => null]);

        $this->actingAs($user);

        $data = [
            'operatorId' => $user->id,
        ];

        $response = $this->post("/tickets/{$ticket->id}/executor", $data);

        $response->assertStatus(403);
    }

    #[Test]
    public function operator_can_close_him_ticket_without_comment()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status' => 'open', 'executor_id' => $operator->id]);

        $this->actingAs($operator);

        $response = $this->post("/tickets/{$ticket->id}/close");

        $response->assertStatus(200)->assertJson([
            'message' => ApiMessages::TICKET_CLOSED,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
        ]);
    }

    #[Test]
    public function operator_can_close_him_ticket_with_comment()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status' => 'open', 'executor_id' => $operator->id]);

        $this->actingAs($operator);

        $data = [
            'text' => 'comment',
        ];

        $response = $this->post("/tickets/{$ticket->id}/close", $data);

        $response->assertStatus(200)->assertJson([
            'message' => ApiMessages::TICKET_CLOSED,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
        ]);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'text' => 'comment',
        ]);
    }

    #[Test]
    public function operator_cannot_close_not_him_ticket()
    {
        $operator1 = User::factory()->create(['role' => 'operator']);
        $operator2 = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status' => 'open', 'executor_id' => $operator1->id]);

        $this->actingAs($operator2);

        $response = $this->post("/tickets/{$ticket->id}/close");

        $response->assertStatus(403);
    }

    #[Test]
    public function user_can_view_him_tickets()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $user->id]);
        $ticket2 = Ticket::factory()->create(['customer_id' => $user->id]);
        $ticket3 = Ticket::factory()->create();
        $ticket4 = Ticket::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/tickets/my?limit=2&page=1");

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'type' => $ticket->type->label,
                    'status' => $ticket->status,
                    'customerId' => $ticket->customer_id,
                ],
                [
                    'type' => $ticket2->type->label,
                    'status' => $ticket2->status,
                    'customerId' => $ticket2->customer_id,
                ],
            ],
            'meta' => [
                'total' => 2,
                'currentPage' => 1,
                'totalPages' => 1,
            ]
        ]);
    }

    #[Test]
    public function operator_can_view_him_tickets()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status'=> 'open', 'executor_id' => $operator->id]);
        $ticket2 = Ticket::factory()->create(['status'=> 'closed', 'executor_id' => $operator->id]);
        $ticket3 = Ticket::factory()->create();
        $ticket4 = Ticket::factory()->create();

        $this->actingAs($operator);

        $response = $this->get("/tickets/operator/my?limit=2&page=1");

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'type' => $ticket->type->label,
                    'status' => $ticket->status,
                    'customerId' => $ticket->customer_id,
                ],
                [
                    'type' => $ticket2->type->label,
                    'status' => $ticket2->status,
                    'customerId' => $ticket2->customer_id,
                ],
            ],
            'meta' => [
                'total' => 2,
                'currentPage' => 1,
                'totalPages' => 1,
            ]
        ]);
    }

    #[Test]
    public function operator_can_view_new_tickets()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $ticket = Ticket::factory()->create(['status'=> 'new', 'executor_id' => null]);
        $ticket2 = Ticket::factory()->create(['status'=> 'new', 'executor_id' => null]);

        $this->actingAs($operator);

        $response = $this->get("/tickets/new?limit=2&page=1");

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'type' => $ticket->type->label,
                    'status' => $ticket->status,
                    'customerId' => $ticket->customer_id,
                ],
                [
                    'type' => $ticket2->type->label,
                    'status' => $ticket2->status,
                    'customerId' => $ticket2->customer_id,
                ],
            ],
            'meta' => [
                'total' => 2,
                'currentPage' => 1,
                'totalPages' => 1,
            ]
        ]);
    }
}
