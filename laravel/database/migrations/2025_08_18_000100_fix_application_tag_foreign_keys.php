<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixApplicationTagForeignKeys extends Migration
{
	public function up(): void
	{
		try { DB::statement('ALTER TABLE applications ENGINE=InnoDB'); } catch (\Throwable $e) {}
		try { DB::statement('ALTER TABLE classes ENGINE=InnoDB'); } catch (\Throwable $e) {}

		$constraints = DB::select(<<<SQL
			SELECT CONSTRAINT_NAME
			FROM information_schema.KEY_COLUMN_USAGE
			WHERE TABLE_SCHEMA = DATABASE()
			  AND TABLE_NAME = 'application_tag'
			  AND REFERENCED_TABLE_NAME IS NOT NULL
		SQL);
		$hasAppFk = false; $hasTagFk = false;
		foreach ($constraints as $row) {
			$name = $row->CONSTRAINT_NAME ?? $row->constraint_name ?? null;
			if ($name === 'application_tag_app_id_foreign') { $hasAppFk = true; }
			if ($name === 'application_tag_tag_id_foreign') { $hasTagFk = true; }
		}
		Schema::table('application_tag', function (Blueprint $table) use (&$hasAppFk, &$hasTagFk) {
			if (!$hasAppFk) {
				try { DB::statement('ALTER TABLE application_tag ADD INDEX app_id_idx (app_id)'); } catch (\Throwable $e) {}
				try { $table->foreign('app_id', 'application_tag_app_id_foreign')->references('id')->on('applications')->onDelete('cascade'); } catch (\Throwable $e) {}
			}
			if (!$hasTagFk) {
				try { DB::statement('ALTER TABLE application_tag ADD INDEX tag_id_idx (tag_id)'); } catch (\Throwable $e) {}
				try { $table->foreign('tag_id', 'application_tag_tag_id_foreign')->references('id')->on('tag')->onDelete('cascade'); } catch (\Throwable $e) {}
			}
		});
	}

	public function down(): void
	{
		Schema::table('application_tag', function (Blueprint $table) {
			try { $table->dropForeign('application_tag_app_id_foreign'); } catch (\Throwable $e) {}
			try { $table->dropForeign('application_tag_tag_id_foreign'); } catch (\Throwable $e) {}
			try { DB::statement('ALTER TABLE application_tag DROP INDEX app_id_idx'); } catch (\Throwable $e) {}
			try { DB::statement('ALTER TABLE application_tag DROP INDEX tag_id_idx'); } catch (\Throwable $e) {}
		});
	}
}

