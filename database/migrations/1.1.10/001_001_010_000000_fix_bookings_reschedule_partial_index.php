<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql' || !Schema::hasTable('bookings')) {
            return;
        }

        $targetColumn = null;

        if (Schema::hasColumn('bookings', 'reschedule_date')) {
            $targetColumn = 'reschedule_date';
        } elseif (Schema::hasColumn('bookings', 'rechedule_date')) {
            $targetColumn = 'rechedule_date';
        }

        if ($targetColumn === null) {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS idx_bookings_reschedule_date_act');
        DB::statement(
            sprintf(
                'CREATE INDEX IF NOT EXISTS idx_bookings_reschedule_date_act ON "bookings" ("%s") WHERE "delete_status" = false AND "%s" IS NOT NULL',
                $targetColumn,
                $targetColumn
            )
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS idx_bookings_reschedule_date_act');
    }
};
