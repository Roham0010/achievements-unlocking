<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievement_levels', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('achievement_id');
            $table->integer('count');
            $table->string('label', 256);

            $table->foreign('achievement_id')
                ->references('id')
                ->on('achievements')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->index(['achievement_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_levels');
    }
};
