<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $groupId = 'booking_status';

    private string $code = 'BS_REJECTED';

    public function up(): void
    {
        $exists = DB::table('references')
            ->where('group_id', $this->groupId)
            ->where('code', $this->code)
            ->exists();

        if (! $exists) {
            DB::table('references')->insert([
                'uuid' => (string) Illuminate\Support\Str::uuid(),
                'code' => $this->code,
                'description' => 'REJECTED (request booking ditolak)',
                'group_id' => $this->groupId,
                'delete_status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('references')
            ->where('group_id', $this->groupId)
            ->where('code', $this->code)
            ->delete();
    }
};
