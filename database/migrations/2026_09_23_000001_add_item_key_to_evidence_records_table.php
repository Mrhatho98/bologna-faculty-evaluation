<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evidence_records', function (Blueprint $table) {
            if (!Schema::hasColumn('evidence_records', 'item_key')) {
                $table->string('item_key')->nullable()->after('section_key')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('evidence_records', function (Blueprint $table) {
            if (Schema::hasColumn('evidence_records', 'item_key')) {
                $table->dropColumn('item_key');
            }
        });
    }
};
