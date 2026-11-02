<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clinics', static function (Blueprint $table): void {
            $table->foreignId('country_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('governorate_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clinics', static function (Blueprint $table): void {
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('governorate_id');
            $table->dropConstrainedForeignId('country_id');
        });
    }
};
