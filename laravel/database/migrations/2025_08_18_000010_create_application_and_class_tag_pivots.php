<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateApplicationAndClassTagPivots extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		// Ensure referenced tables use InnoDB so foreign keys can be created
		try { DB::statement('ALTER TABLE applications ENGINE=InnoDB'); } catch (\Throwable $e) {}
		try { DB::statement('ALTER TABLE classes ENGINE=InnoDB'); } catch (\Throwable $e) {}
		try { DB::statement('ALTER TABLE tag ENGINE=InnoDB'); } catch (\Throwable $e) {}

		// applications <-> tag
		if (!Schema::hasTable('application_tag')) {
			Schema::create('application_tag', function (Blueprint $table) {
				// Ensure pivot table is InnoDB for FK support
				$table->engine = 'InnoDB';
				$table->unsignedInteger('app_id');
				$table->unsignedInteger('tag_id');
				$table->primary(['app_id', 'tag_id']);
				$table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade');
				$table->foreign('tag_id')->references('id')->on('tag')->onDelete('cascade');
			});
		}

		// classes <-> tag
		if (!Schema::hasTable('class_tag')) {
			Schema::create('class_tag', function (Blueprint $table) {
				// Ensure pivot table is InnoDB for FK support
				$table->engine = 'InnoDB';
				$table->unsignedInteger('class_id');
				$table->unsignedInteger('tag_id');
				$table->primary(['class_id', 'tag_id']);
				$table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
				$table->foreign('tag_id')->references('id')->on('tag')->onDelete('cascade');
			});
		}

		// backfill existing single tag_id values into new pivots
		if (Schema::hasColumn('applications', 'tag_id')) {
			$rows = DB::table('applications')->whereNotNull('tag_id')->select('id', 'tag_id')->get();
			foreach ($rows as $row) {
				$exists = DB::table('application_tag')
					->where('app_id', $row->id)
					->where('tag_id', $row->tag_id)
					->exists();
				if (!$exists) {
					DB::table('application_tag')->insert([
						'app_id' => $row->id,
						'tag_id' => $row->tag_id,
					]);
				}
			}
		}

		if (Schema::hasColumn('classes', 'tag_id')) {
			$rows = DB::table('classes')->whereNotNull('tag_id')->select('id', 'tag_id')->get();
			foreach ($rows as $row) {
				$exists = DB::table('class_tag')
					->where('class_id', $row->id)
					->where('tag_id', $row->tag_id)
					->exists();
				if (!$exists) {
					DB::table('class_tag')->insert([
						'class_id' => $row->id,
						'tag_id' => $row->tag_id,
					]);
				}
			}
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('application_tag');
		Schema::dropIfExists('class_tag');
	}
}

