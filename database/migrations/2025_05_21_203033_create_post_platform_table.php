<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('platform_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'published', 'failed'])->default('pending');
            $table->timestamps();

            // Add indexes
            $table->index(['post_id', 'platform_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_platform');
    }
};