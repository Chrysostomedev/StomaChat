<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('contenu')->nullable();
            $table->json('media')->nullable(); // images, pdf, vidéos
            $table->enum('type', ['texte', 'image', 'pdf', 'video'])->default('texte');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('publications');
    }
};
