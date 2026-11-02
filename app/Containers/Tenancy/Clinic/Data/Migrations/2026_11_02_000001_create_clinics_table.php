<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinics', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->char('country', 2);
            $table->string('timezone');
            $table->char('currency', 3);
            $table->string('subscription_status')->default('trial');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->timestamps();

            $table->index('country');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
