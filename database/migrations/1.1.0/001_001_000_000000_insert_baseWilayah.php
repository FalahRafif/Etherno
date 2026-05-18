<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('wilayah', function (Blueprint $table): void {
			$table->string('kode', 13)->primary();
			$table->string('nama');
			$table->index('nama', 'wilayah_name_idx');
		});

		$records = $this->loadWilayahRecords(database_path('migrations/1.1.0/dataset/wilayah.sql'));

		foreach (array_chunk($records, 1000) as $chunk) {
			DB::table('wilayah')->insert($chunk);
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('wilayah');
	}

	/**
	 * @return array<int, array{kode: string, nama: string}>
	 */
	private function loadWilayahRecords(string $path): array
	{
		$sql = file_get_contents($path);

		if ($sql === false) {
			throw new RuntimeException('Unable to read wilayah dataset.');
		}

		preg_match_all(
			"/\\(\\s*'((?:[^']|'')*)'\\s*,\\s*'((?:[^']|'')*)'\\s*\\)/",
			$sql,
			$matches,
			PREG_SET_ORDER
		);

		return array_map(static function (array $match): array {
			return [
				'kode' => str_replace("''", "'", $match[1]),
				'nama' => str_replace("''", "'", $match[2]),
			];
		}, $matches);
	}
};
