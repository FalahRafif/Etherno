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
		Schema::create('bookings', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->unsignedBigInteger('customer_id');
			$table->unsignedBigInteger('package_id');
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('location_id');
			$table->date('event_date');
			$table->unsignedBigInteger('event_session')->nullable();
			$table->text('event_detail')->nullable();
			$table->text('gogle_maps_pin')->nullable();
			$table->date('rechedule_date')->nullable();
			$table->text('rechedule_reason')->nullable();
			$table->date('force_majeure_date')->nullable();
			$table->text('force_majeure_reason')->nullable();
			$table->unsignedBigInteger('operator_id')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('customer_id');
			$table->index('package_id');
			$table->index('status_id');
			$table->index('location_id');
			$table->index('event_session');
			$table->index('operator_id');
			$table->index('event_date');
			$table->index('delete_status');

			$table->foreign('customer_id')->references('id')->on('customers');
			$table->foreign('package_id')->references('id')->on('packages');
			$table->foreign('status_id')->references('id')->on('references');
			$table->foreign('location_id')->references('id')->on('locations');
			$table->foreign('event_session')->references('id')->on('references');
			$table->foreign('operator_id')->references('id')->on('users')->nullOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('bookings');
	}
};
