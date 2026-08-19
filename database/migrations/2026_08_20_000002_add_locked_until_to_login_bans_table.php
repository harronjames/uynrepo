<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('login_bans', 'locked_until')) {
            Schema::table('login_bans', function (Blueprint $table) {
                $table->timestamp('locked_until')->nullable()->after('banned_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('login_bans', function (Blueprint $table) {
            if (Schema::hasColumn('login_bans', 'locked_until')) {
                $table->dropColumn('locked_until');
            }
        });
    }
};
