<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS dictionnaries_view');
        DB::statement('DROP VIEW IF EXISTS dictionaries_view');

        DB::statement('
            CREATE VIEW dictionaries_view AS
            SELECT
                d.id,
                d.slug,
                d.name,
                d.type,
                COALESCE(entries.entries_count, 0) AS entries_count
            FROM dictionnaries d
            LEFT JOIN (
                SELECT
                    dictionnary_id,
                    COUNT(*) AS entries_count
                FROM dictionnary_entries
                WHERE parent IS NULL AND deleted_at IS NULL
                GROUP BY dictionnary_id
            ) AS entries ON d.id = entries.dictionnary_id
        ');
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS dictionnaries_view');
        DB::statement('DROP VIEW IF EXISTS dictionaries_view');
    }
};
