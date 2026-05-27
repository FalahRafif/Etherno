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
		Schema::create('billings', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('booking_id');
			$table->decimal('total_amount', 15, 2)->default(0);
			$table->decimal('total_paid', 15, 2)->default(0);
			$table->decimal('refunded_amount', 15, 2)->default(0);
			$table->unsignedBigInteger('status_id');
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('booking_id');
			$table->index('status_id');
			$table->index('delete_status');

			$table->foreign('booking_id')->references('id')->on('bookings');
			$table->foreign('status_id')->references('id')->on('references');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('billings');
	}
};
