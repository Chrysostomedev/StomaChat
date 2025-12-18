<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('pseudo', 191)->unique();
    $table->string('email', 191)->unique();
    $table->integer('age')->nullable();
    $table->text('description')->nullable();
    $table->string('centre_interet')->nullable();
    $table->string('profession')->nullable();
    $table->string('photo')->nullable();
    $table->string('password');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
