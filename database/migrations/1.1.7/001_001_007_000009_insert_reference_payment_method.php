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
				'code' => 'PYM_BANK_TRANSFER',
				'description' => 'BANK TRANSFER',
				'group_id' => 'payment_method',
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
				'code' => 'PYM_QRIS',
				'description' => 'QRIS',
				'group_id' => 'payment_method',
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
				'code' => 'PYM_CASH',
				'description' => 'CASH',
				'group_id' => 'payment_method',
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
				'code' => 'PYM_E_WALLET',
				'description' => 'E-WALLET',
				'group_id' => 'payment_method',
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
			->where('group_id', 'payment_method')
			->whereIn('code', ['PYM_BANK_TRANSFER', 'PYM_QRIS', 'PYM_CASH', 'PYM_E_WALLET'])
			->delete();
	}
};
