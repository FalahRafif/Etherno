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
		$weddingTypeId = DB::table('references')
			->where('group_id', 'package_type')
			->where('code', 'PKT_WEDDING')
			->value('id');
		$nonWeddingTypeId = DB::table('references')
			->where('group_id', 'package_type')
			->where('code', 'PKT_NON_WEDDING')
			->value('id');

		if ($weddingTypeId === null || $nonWeddingTypeId === null) {
			throw new RuntimeException('Reference package_type (PKT_WEDDING / PKT_NON_WEDDING) not found.');
		}

		DB::table('settings')->insert([
			[
				'uuid' => (string) Str::uuid(),
				'code' => 'PDR_DP',
				'description' => 'DP Maksimal Hari',
				'group_id' => 'paymet_date_rule',
				'type_id' => null,
				'value' => 'H+3',
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
				'code' => 'PDR_MAX_FINAL',
				'description' => 'MAX FINAL Lunas ',
				'group_id' => 'paymet_date_rule',
				'type_id' => null,
				'value' => 'H-1', 
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
				'code' => 'PTPP_WED',
				'description' => 'Percentage DP Wedding',
				'group_id' => 'payment_type_price_percentage',
				'type_id' => (int) $weddingTypeId,
				'value' => '15', // 15% (with % sign to test type conversion)
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
				'code' => 'PTPP_NON_WED',
				'description' => 'Percentage DP Non Wedding',
				'group_id' => 'payment_type_price_percentage',
				'type_id' => (int) $nonWeddingTypeId,
				'value' => '10', // 10% (without % sign to test type conversion)
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
				'code' => 'PKDR_MAX_RECHEDULE_DATE',
				'description' => 'MAX RECHEDULE DATE',
				'group_id' => 'package_date_rule',
				'type_id' => null,
				'value' => 'H-14', 
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
		DB::table('settings')
			->where(function ($query): void {
				$query
					->where(function ($group): void {
						$group->where('group_id', 'paymet_date_rule')
							->whereIn('code', ['PDR_DP', 'PDR_MAX_FINAL']);
					})
					->orWhere(function ($group): void {
						$group->where('group_id', 'payment_type_price_percentage')
							->whereIn('code', ['PTPP_WED', 'PTPP_NON_WED']);
					})
					->orWhere(function ($group): void {
						$group->where('group_id', 'package_date_rule')
							->where('code', 'PKDR_MAX_RECHEDULE_DATE');
					});
			})
			->delete();
	}
};
