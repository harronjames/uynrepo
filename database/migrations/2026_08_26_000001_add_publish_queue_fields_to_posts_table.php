<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'status')) {
                $table->enum('status', ['published', 'scheduled', 'draft'])
                    ->default('published')
                    ->after('main_image');
            }

            if (! Schema::hasColumn('posts', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('posts', 'queue_position')) {
                $table->unsignedInteger('queue_position')->nullable()->after('published_at');
            }
        });

        // Mevcut yazılar: veri kaybı olmadan yayında kabul et, tarih = created_at
        DB::table('posts')
            ->whereNull('published_at')
            ->update([
                'published_at' => DB::raw('COALESCE(created_at, NOW())'),
                'status'       => 'published',
            ]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'queue_position')) {
                $table->dropColumn('queue_position');
            }
            if (Schema::hasColumn('posts', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('posts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
