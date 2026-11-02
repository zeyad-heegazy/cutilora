<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('code');

            $table->unique(
                ['country_id', 'code'],
                'governorates_country_code_unique'
            );
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('code');

            $table->unique(
                ['governorate_id', 'code'],
                'cities_governorate_code_unique'
            );
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('code');

            $table->unique(
                ['city_id', 'code'],
                'districts_city_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropUnique('districts_city_code_unique');
            $table->dropColumn(['code', 'name_ar']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropUnique('cities_governorate_code_unique');
            $table->dropColumn(['code', 'name_ar']);
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropUnique('governorates_country_code_unique');
            $table->dropColumn(['code', 'name_ar']);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });
    }
};
