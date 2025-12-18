<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('expediteur_id')->constrained('users')->onDelete('cascade');
            $table->text('contenu')->nullable();
            $table->json('fichier')->nullable(); // image, doc, pdf, vidéo
            $table->boolean('lu')->default(false);
            $table->boolean('modifie')->default(false);
            $table->boolean('vue_unique')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('messages');
    }
};
