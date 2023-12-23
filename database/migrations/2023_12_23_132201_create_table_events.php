<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->nullable(false);
            $table->string('tempat', 100)->nullable(false);
            $table->string('dihadiri', 100)->nullable(false);
            $table->string('pakaian', 100)->nullable(false);
            $table->dateTime('start_event')->nullable(false);
            $table->dateTime('end_event')->nullable(true);
            $table->string('keterangan')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
