<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'publication_id', 'contenu', 'likes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function likesUsers()
    {
        return $this->belongsToMany(User::class, 'commentaire_user_likes')->withTimestamps();
    }

    public function isLikedBy($user)
    {
        return $this->likesUsers()->where('user_id', $user->id)->exists();
    }
}
