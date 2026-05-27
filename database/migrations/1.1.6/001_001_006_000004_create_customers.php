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
		Schema::create('customers', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('first_name');
			$table->string('last_name')->nullable();
			$table->string('phone_number', 30)->nullable();
			$table->string('email')->nullable();
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('first_name');
			$table->index('phone_number');
			$table->index('email');
			$table->index('delete_status');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('customers');
	}
};
