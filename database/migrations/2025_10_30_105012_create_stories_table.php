<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('texte')->nullable();
            $table->string('background_color', 7)->nullable();

            // Médias multiples stockés en JSON
            $table->json('media')->nullable();

            $table->json('vues')->nullable();
            $table->timestamp('expire_le');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stories');
    }
};
