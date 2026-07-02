<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otpify_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index();
            $table->string('purpose')->default('default');
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['identifier', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otpify_tokens');
    }
};
