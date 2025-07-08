<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->foreignUuid('users_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('advices_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('image_url');
            $table->string('image_public_id');
            $table->string('prediction');
            $table->string('confidence');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
