<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('ticket_types');
            $table->enum('status', ['new', 'open', 'closed'])->default('new');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('executor_id')->nullable()->constrained('users');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_types');
    }
};
