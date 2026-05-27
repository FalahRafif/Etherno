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
		Schema::create('billing_installments', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('billing_id');
			$table->unsignedBigInteger('installment_type');
			$table->unsignedBigInteger('status_id');
			$table->decimal('amount', 15, 2)->default(0);
			$table->date('due_date')->nullable();
			$table->decimal('paid_amount', 15, 2)->default(0);
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('billing_id');
			$table->index('installment_type');
			$table->index('status_id');
			$table->index('due_date');
			$table->index('delete_status');

			$table->foreign('billing_id')->references('id')->on('billings');
			$table->foreign('installment_type')->references('id')->on('references');
			$table->foreign('status_id')->references('id')->on('references');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('billing_installments');
	}
};
