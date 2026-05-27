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
		Schema::create('packages', function (Blueprint $table): void {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('name');
			$table->text('description')->nullable();
			$table->decimal('price', 15, 2)->default(0);
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('thumbnail_attachment_id')->nullable();
			$table->unsignedBigInteger('package_type');
			$table->timestamp('created_at')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamp('updated_at')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->unsignedBigInteger('deleted_by')->nullable();
			$table->boolean('delete_status')->default(false);

			$table->index('name');
			$table->index('status_id');
			$table->index('thumbnail_attachment_id');
			$table->index('package_type');
			$table->index('delete_status');

			$table->foreign('status_id')->references('id')->on('references');
			$table->foreign('thumbnail_attachment_id')->references('id')->on('attachments')->nullOnDelete();
			$table->foreign('package_type')->references('id')->on('references');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('packages');
	}
};
