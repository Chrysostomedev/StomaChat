<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'titre', 'contenu', 'thematique',
        'vues', 'votes', 'favoris', 'resolue'
    ];

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function reponses() {
        return $this->hasMany(Reponse::class);
    }

    // Helpers
    public function isFavoritedBy($userId) {
        return $this->favoris_users()->where('user_id', $userId)->exists();
    }
}
