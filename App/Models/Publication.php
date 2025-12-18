<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $fillable = ['user_id', 'contenu', 'media', 'type'];

    protected $casts = ['media' => 'array'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function commentaires() {
        return $this->hasMany(Commentaire::class);
    }
}
