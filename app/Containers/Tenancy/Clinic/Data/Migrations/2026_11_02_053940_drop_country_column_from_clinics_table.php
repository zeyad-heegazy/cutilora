<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('clinics', static function (Blueprint $table) {
            if (Schema::hasColumn('clinics', 'country')) {
                $table->dropColumn('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinics', static function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'country')) {
                $table->string('country')->nullable();
            }
        }); }
};
