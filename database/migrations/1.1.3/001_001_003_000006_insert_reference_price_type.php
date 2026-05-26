<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$now = now();

		DB::table('references')->insert([
			[
				'uuid' => (string) Str::uuid(),
				'code' => 'PT_RG',
				'description' => 'Tambahan Ringan',
				'group_id' => 'price_type',
				'created_at' => $now,
				'created_by' => null,
				'updated_at' => $now,
				'updated_by' => null,
				'deleted_at' => null,
				'deleted_by' => null,
				'delete_status' => false,
			],
			[
				'uuid' => (string) Str::uuid(),
				'code' => 'PT_SD',
				'description' => 'Tambahan Sedang',
				'group_id' => 'price_type',
				'created_at' => $now,
				'created_by' => null,
				'updated_at' => $now,
				'updated_by' => null,
				'deleted_at' => null,
				'deleted_by' => null,
				'delete_status' => false,
			],
			[
				'uuid' => (string) Str::uuid(),
				'code' => 'PT_CS',
				'description' => 'Tambahan Custom ( Transport/ + Akomodasi)',
				'group_id' => 'price_type',
				'created_at' => $now,
				'created_by' => null,
				'updated_at' => $now,
				'updated_by' => null,
				'deleted_at' => null,
				'deleted_by' => null,
				'delete_status' => false,
			],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		DB::table('references')
			->where('group_id', 'price_type')
			->whereIn('code', ['PT_RG', 'PT_SD', 'PT_CS'])
			->delete();
	}
};
