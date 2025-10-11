<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TicketController;

Route::middleware('auth')->group(function () {
    Route::get(
        '/tickets/{ticketId}',
        [TicketController::class, 'findById']
    )->where('ticketId', '[0-9]+');
    Route::get('/tickets/my', [TicketController::class, 'findMyTickets']);

    Route::post('/tickets', [TicketController::class, 'store']);
});

Route::middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/tickets/operator/my', [TicketController::class, 'findMyOperatorTickets']);
    Route::get('/tickets/new', [TicketController::class, 'findNewTickets']);

    Route::post('/tickets/{ticketId}/executor', [TicketController::class, 'assignExecutor']);
    Route::post('/tickets/{ticketId}/close', [TicketController::class, 'closeTicket']);
});
