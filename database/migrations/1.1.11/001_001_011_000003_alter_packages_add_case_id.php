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
		if (!Schema::hasTable('packages')) {
			return;
		}

		if (!Schema::hasColumn('packages', 'case_id')) {
			Schema::table('packages', function (Blueprint $table): void {
				$table->string('case_id', 32)->nullable()->unique();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		if (!Schema::hasTable('packages')) {
			return;
		}

		if (Schema::hasColumn('packages', 'case_id')) {
			Schema::table('packages', function (Blueprint $table): void {
				$table->dropUnique('packages_case_id_unique');
				$table->dropColumn('case_id');
			});
		}
	}
};
