<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['est_groupe', 'titre', 'cree_par'];

    // Relation avec les utilisateurs
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withTimestamps();
    }

    // Relation avec les messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    

}
