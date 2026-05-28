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
        $pagiSiangId = DB::table('references')
            ->where('group_id', 'event_session')
            ->where('code', 'ES_PAGI_SIANG')
            ->value('id');
        $soreMalamId = DB::table('references')
            ->where('group_id', 'event_session')
            ->where('code', 'ES_SORE_MALAM')
            ->value('id');

        if ($pagiSiangId === null || $soreMalamId === null) {
            throw new RuntimeException('Reference event_session (ES_PAGI_SIANG / ES_SORE_MALAM) not found.');
        }

        DB::table('settings')->insert([
            [
                'uuid' => (string) Str::uuid(),
                'code' => 'PKDR_MAX_QUOTA_PAGI_SIANG',
                'description' => 'MAX QUOTA PAGI-SIANG',
                'group_id' => 'package_date_rule',
                'type_id' => (int) $pagiSiangId,
                'value' => '1',
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
                'code' => 'PKDR_MAX_QUOTA_SORE_MALAM',
                'description' => 'MAX QUOTA SORE-MALAM',
                'group_id' => 'package_date_rule',
                'type_id' => (int) $soreMalamId,
                'value' => '1',
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
            ->where('group_id', 'package_date_rule')
            ->whereIn('code', ['PKDR_MAX_QUOTA_PAGI_SIANG', 'PKDR_MAX_QUOTA_SORE_MALAM'])
            ->delete();
    }
};
