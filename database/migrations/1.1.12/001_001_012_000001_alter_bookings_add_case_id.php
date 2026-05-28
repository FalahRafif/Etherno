<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->string('case_id', 32)->nullable()->unique();
        });

        $bookings = DB::table('bookings')
            ->select('id', 'created_at')
            ->whereNull('deleted_at')
            ->orWhere('delete_status', false)
            ->orderBy('id')
            ->get();

        foreach ($bookings as $booking) {
            $date = $booking->created_at
                ? \Carbon\Carbon::parse($booking->created_at)->format('Ymd')
                : now()->format('Ymd');
            $caseId = sprintf('ETH-%s-%05d', $date, $booking->id);
            DB::table('bookings')->where('id', $booking->id)->update(['case_id' => $caseId]);
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropUnique(['case_id']);
            $table->dropColumn('case_id');
        });
    }
};
