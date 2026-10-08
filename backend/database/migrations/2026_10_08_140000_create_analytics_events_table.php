<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_name', 64);
            $table->string('path', 2048)->nullable();
            $table->string('product_slug')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index(['event_name', 'created_at']);
            $table->index(['product_slug', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
