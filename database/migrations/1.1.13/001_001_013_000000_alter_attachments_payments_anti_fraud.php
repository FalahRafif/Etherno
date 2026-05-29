<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('payments', function (Blueprint $table): void {
			$table->text('rejection_reason')->nullable()->after('transfer_receipt_attachment_id');
		});
	}

	public function down(): void
	{
		Schema::table('payments', function (Blueprint $table): void {
			$table->dropColumn('rejection_reason');
		});
	}
};
