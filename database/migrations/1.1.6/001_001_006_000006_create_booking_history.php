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
		Schema::create('booking_history', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('booking_id');
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('operator_id')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('booking_id');
			$table->index('status_id');
			$table->index('operator_id');
			$table->index('delete_status');

			$table->foreign('booking_id')->references('id')->on('bookings');
			$table->foreign('status_id')->references('id')->on('references');
			$table->foreign('operator_id')->references('id')->on('users')->nullOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('booking_history');
	}
};
