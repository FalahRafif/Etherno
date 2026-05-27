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
				'code' => 'INS_DP',
				'description' => 'DP',
				'group_id' => 'intallment_type',
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
				'code' => 'INS_PARTIAL',
				'description' => 'PARTIAL',
				'group_id' => 'intallment_type',
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
				'code' => 'INS_FINAL',
				'description' => 'FINAL',
				'group_id' => 'intallment_type',
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
				'code' => 'INS_REFUND',
				'description' => 'REFUND',
				'group_id' => 'intallment_type',
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
			->where('group_id', 'intallment_type')
			->whereIn('code', ['INS_DP', 'INS_PARTIAL', 'INS_FINAL', 'INS_REFUND'])
			->delete();
	}
};
