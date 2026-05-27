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
		if (!Schema::hasTable('bookings')) {
			return;
		}

		if (Schema::hasColumn('bookings', 'gogle_maps_pin') && !Schema::hasColumn('bookings', 'google_maps_pin')) {
			Schema::table('bookings', function (Blueprint $table): void {
				$table->renameColumn('gogle_maps_pin', 'google_maps_pin');
			});
		}

		if (Schema::hasColumn('bookings', 'rechedule_date') && !Schema::hasColumn('bookings', 'reschedule_date')) {
			Schema::table('bookings', function (Blueprint $table): void {
				$table->renameColumn('rechedule_date', 'reschedule_date');
			});
		}

		if (Schema::hasColumn('bookings', 'rechedule_reason') && !Schema::hasColumn('bookings', 'reschedule_reason')) {
			Schema::table('bookings', function (Blueprint $table): void {
				$table->renameColumn('rechedule_reason', 'reschedule_reason');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		if (!Schema::hasTable('bookings')) {
			return;
		}

		if (Schema::hasColumn('bookings', 'google_maps_pin') && !Schema::hasColumn('bookings', 'gogle_maps_pin')) {
			Schema::table('bookings', function (Blueprint $table): void {
				$table->renameColumn('google_maps_pin', 'gogle_maps_pin');
			});
		}

		if (Schema::hasColumn('bookings', 'reschedule_date') && !Schema::hasColumn('bookings', 'rechedule_date')) {
			Schema::table('bookings', function (Blueprint $table): void {
				$table->renameColumn('reschedule_date', 'rechedule_date');
			});
		}

		if (Schema::hasColumn('bookings', 'reschedule_reason') && !Schema::hasColumn('bookings', 'rechedule_reason')) {
			Schema::table('bookings', function (Blueprint $table): void {
				$table->renameColumn('reschedule_reason', 'rechedule_reason');
			});
		}
	}
};
