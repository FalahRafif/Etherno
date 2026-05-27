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
				'code' => 'BS_WAITING_APPROVAL',
				'description' => 'WAITING APPROVAL (checking petugas sebelum meminta dp)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_APPROVED_WAITING_DP',
				'description' => 'APPROVED WAITING DP (sudah di approve dan menunggu dp)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_APPROVED_WAITING_FINAL_PAYMENT',
				'description' => 'APPROVED WAITING FINAL PAYMENT (sudah approve, sudah dp, menunggul pelunasan)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_EXPIRED',
				'description' => 'EXPIRED (sudah approved tetapi belum bayar dp)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_EXPIRED_DP',
				'description' => 'EXPIRED DP (sudah approve, sudah dp tetapi belum bayar lunas)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_CANCEL',
				'description' => 'CANCEL (sudah dp tetapi dibatalkan)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_CONFIRMED',
				'description' => 'CONFIRMED (sudah approve, sudah dp, sudah lunas)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_RESCHEDULE',
				'description' => 'RESCHEDULE (rechedule sebelum date of acara)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_FORCE_MAJEURE',
				'description' => 'FORCE MAJEURE (rechedule di date of acara)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_COMPLETE',
				'description' => 'COMPLETE (acara selesai)',
				'group_id' => 'booking_status',
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
				'code' => 'BS_REFUND',
				'description' => 'REFUND (booking refund)',
				'group_id' => 'booking_status',
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
			->where('group_id', 'booking_status')
			->whereIn('code', [
				'BS_WAITING_APPROVAL',
				'BS_APPROVED_WAITING_DP',
				'BS_APPROVED_WAITING_FINAL_PAYMENT',
				'BS_EXPIRED',
				'BS_EXPIRED_DP',
				'BS_CANCEL',
				'BS_CONFIRMED',
				'BS_RESCHEDULE',
				'BS_FORCE_MAJEURE',
				'BS_COMPLETE',
				'BS_REFUND',
			])
			->delete();
	}
};
