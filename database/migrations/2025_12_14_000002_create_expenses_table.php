<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->onDelete('set null');
            $table->string('reference_number')->unique();
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_interval')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'expense_date']);
            $table->index('category_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
