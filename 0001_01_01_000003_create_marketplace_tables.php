<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('icon', 50)->nullable();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->decimal('budget', 10, 2);
            $table->string('attachment')->nullable();
            $table->string('attachment_name')->nullable();
            $table->foreignId('hired_freelancer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open', 'in_progress', 'completed', 'closed'])->default('open');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('bid_amount', 10, 2);
            $table->text('proposal');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
            $table->unique(['project_id', 'freelancer_id']);
        });

        Schema::create('gigs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('delivery_days')->default(7);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gig_id')->constrained('gigs')->cascadeOnDelete();
            $table->string('gig_title')->nullable();
            $table->string('freelancer_name')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'gig_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_number', 50)->nullable()->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('order_summary')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['card', 'paypal'])->default('card');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('transaction_id', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('gig_id')->constrained('gigs')->cascadeOnDelete();
            $table->string('gig_title')->nullable();
            $table->string('freelancer_name')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'reviewer_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title', 200);
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('paid');
            $table->timestamps();
            $table->unique('project_id');
        });
    }

    public function down(): void
    {
        foreach (['earnings', 'notifications', 'messages', 'reviews', 'order_items', 'orders', 'cart_items', 'gigs', 'bids', 'projects', 'categories'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
