<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$roleIds = DB::table('roles')
			->whereIn('name', ['Admin', 'Petugas', 'Customer'])
			->pluck('id', 'name');

		if ($roleIds->count() !== 3) {
			throw new RuntimeException('Default roles (Admin, Petugas, Customer) must exist before inserting users.');
		}

		$now = now();

		$users = [
			[
				'name' => 'Admin',
				'username' => 'admin',
				'email' => 'admin@etherno.local',
				'role_name' => 'Admin',
			],
			[
				'name' => 'Petugas',
				'username' => 'petugas',
				'email' => 'petugas@etherno.local',
				'role_name' => 'Petugas',
			],
			[
				'name' => 'Customer',
				'username' => 'customer',
				'email' => 'customer@etherno.local',
				'role_name' => 'Customer',
			],
		];

		foreach ($users as $user) {
			DB::table('users')->updateOrInsert(
				['email' => $user['email']],
				[
					'uuid' => (string) Str::uuid(),
					'name' => $user['name'],
					'username' => $user['username'],
					'email_verified_at' => $now,
					'password' => Hash::make('password'),
					'remember_token' => null,
					'role_id' => (int) $roleIds[$user['role_name']],
					'profile_image_attachment_id' => null,
					'created_at' => $now,
					'created_by' => null,
					'updated_at' => $now,
					'updated_by' => null,
					'deleted_at' => null,
					'deleted_by' => null,
					'delete_status' => false,
				]
			);
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		DB::table('users')
			->whereIn('email', [
				'admin@etherno.local',
				'petugas@etherno.local',
				'customer@etherno.local',
			])
			->delete();
	}
};
