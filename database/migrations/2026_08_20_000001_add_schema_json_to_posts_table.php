<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'schema_json')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->text('schema_json')->nullable()->after('meta_keywords');
            });
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'schema_json')) {
                $table->dropColumn('schema_json');
            }
        });
    }
};
