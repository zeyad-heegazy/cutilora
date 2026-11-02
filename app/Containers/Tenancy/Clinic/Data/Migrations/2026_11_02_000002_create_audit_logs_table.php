<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->string('actor_type');
            $table->unsignedBigInteger('actor_id');
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('meta')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'created_at']);
            $table->index(['actor_type', 'actor_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
