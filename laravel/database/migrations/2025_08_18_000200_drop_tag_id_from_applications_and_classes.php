<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropTagIdFromApplicationsAndClasses extends Migration
{
    /**
     * Move any remaining tag_id references into pivot tables, then drop columns.
     */
    public function up(): void
    {
        // Backfill to application_tag from applications.tag_id if present
        if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'tag_id')) {
            if (Schema::hasTable('application_tag')) {
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

            // Drop FK on applications.tag_id if it exists (avoid exceptions inside Schema::table)
            $constraints = DB::select(<<<SQL
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'applications'
                  AND COLUMN_NAME = 'tag_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            SQL);
            foreach ($constraints as $row) {
                $name = $row->CONSTRAINT_NAME ?? $row->constraint_name ?? null;
                if ($name) {
                    DB::statement('ALTER TABLE `applications` DROP FOREIGN KEY `'.str_replace('`','',$name).'`');
                }
            }

            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'tag_id')) {
                    $table->dropColumn('tag_id');
                }
            });
        }

        // Backfill to class_tag from classes.tag_id if present
        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'tag_id')) {
            if (Schema::hasTable('class_tag')) {
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

            // Drop FK on classes.tag_id if it exists
            $constraints = DB::select(<<<SQL
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'classes'
                  AND COLUMN_NAME = 'tag_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            SQL);
            foreach ($constraints as $row) {
                $name = $row->CONSTRAINT_NAME ?? $row->constraint_name ?? null;
                if ($name) {
                    DB::statement('ALTER TABLE `classes` DROP FOREIGN KEY `'.str_replace('`','',$name).'`');
                }
            }

            Schema::table('classes', function (Blueprint $table) {
                if (Schema::hasColumn('classes', 'tag_id')) {
                    $table->dropColumn('tag_id');
                }
            });
        }
    }

    /**
     * Recreate tag_id columns and backfill a representative value from pivot tables.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications') && !Schema::hasColumn('applications', 'tag_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->unsignedBigInteger('tag_id')->nullable()->after('question_num');
                // FK to singular 'tag' table (matches this schema)
                try { $table->foreign('tag_id')->references('id')->on('tag')->onDelete('set null'); } catch (\Throwable $e) {}
            });
            // Backfill a representative tag_id from pivot (min)
            if (Schema::hasTable('application_tag')) {
                DB::statement('UPDATE applications a LEFT JOIN (SELECT app_id, MIN(tag_id) AS tag_id FROM application_tag GROUP BY app_id) t ON t.app_id = a.id SET a.tag_id = t.tag_id');
            }
        }

        if (Schema::hasTable('classes') && !Schema::hasColumn('classes', 'tag_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->unsignedBigInteger('tag_id')->nullable()->after('is_researchable');
                try { $table->foreign('tag_id')->references('id')->on('tag')->onDelete('set null'); } catch (\Throwable $e) {}
            });
            if (Schema::hasTable('class_tag')) {
                DB::statement('UPDATE classes c LEFT JOIN (SELECT class_id, MIN(tag_id) AS tag_id FROM class_tag GROUP BY class_id) t ON t.class_id = c.id SET c.tag_id = t.tag_id');
            }
        }
    }
}
