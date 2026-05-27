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
		Schema::create('payments', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('billing_installment_id');
			$table->unsignedBigInteger('payment_type');
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('payment_method');
			$table->decimal('amount', 15, 2)->default(0);
			$table->timestamp('paid_at')->nullable();
			$table->unsignedBigInteger('transfer_receipt_attachment_id')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('billing_installment_id');
			$table->index('payment_type');
			$table->index('status_id');
			$table->index('payment_method');
			$table->index('paid_at');
			$table->index('transfer_receipt_attachment_id');
			$table->index('delete_status');

			$table->foreign('billing_installment_id')->references('id')->on('billing_installments');
			$table->foreign('payment_type')->references('id')->on('references');
			$table->foreign('status_id')->references('id')->on('references');
			$table->foreign('payment_method')->references('id')->on('references');
			$table->foreign('transfer_receipt_attachment_id')->references('id')->on('attachments')->nullOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('payments');
	}
};
