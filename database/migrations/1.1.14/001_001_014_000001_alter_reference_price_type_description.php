<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$now = now();

		DB::table('references')
			->where('group_id', 'price_type')
			->where('code', 'PT_RG')
			->update([
				'description' => 'Tambahan Ringan (100K-500K)',
				'updated_at' => $now,
				'updated_by' => null,
			]);

		DB::table('references')
			->where('group_id', 'price_type')
			->where('code', 'PT_SD')
			->update([
				'description' => 'Tambahan Sedang (500K+)',
				'updated_at' => $now,
				'updated_by' => null,
			]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		$now = now();

		DB::table('references')
			->where('group_id', 'price_type')
			->where('code', 'PT_RG')
			->update([
				'description' => 'Tambahan Ringan',
				'updated_at' => $now,
				'updated_by' => null,
			]);

		DB::table('references')
			->where('group_id', 'price_type')
			->where('code', 'PT_SD')
			->update([
				'description' => 'Tambahan Sedang',
				'updated_at' => $now,
				'updated_by' => null,
			]);
	}
};
