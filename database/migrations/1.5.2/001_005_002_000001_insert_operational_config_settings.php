<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
	public function up(): void
	{
		$now = now();

		DB::table('settings')->insert([
			[
				'uuid' => (string) Str::uuid(),
				'code' => 'ADMIN_WHATSAPP',
				'description' => 'Nomor WhatsApp Admin',
				'group_id' => 'operational_config',
				'type_id' => null,
				'value' => '6281234567890',
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

	public function down(): void
	{
		DB::table('settings')
			->where('group_id', 'operational_config')
			->whereIn('code', ['ADMIN_WHATSAPP'])
			->delete();
	}
};
