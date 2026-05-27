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
				'code' => 'ES_PAGI_SIANG',
				'description' => 'PAGI - SIANG',
				'group_id' => 'event_session',
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
				'code' => 'ES_SORE_MALAM',
				'description' => 'SORE - MALAM',
				'group_id' => 'event_session',
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
			->where('group_id', 'event_session')
			->whereIn('code', ['ES_PAGI_SIANG', 'ES_SORE_MALAM'])
			->delete();
	}
};
