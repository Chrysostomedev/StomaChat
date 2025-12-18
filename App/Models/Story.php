<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Story extends Model
{
    protected $fillable = ['user_id', 'media', 'texte', 'expire_le', 'background_color', 'vues'];

    protected $casts = [
        'media' => 'array',
        'vues' => 'array',
        'expire_le' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isExpired() {
        return $this->expire_le < now();
    }

    public function vuesCount() {
        return is_array($this->vues) ? count($this->vues) : 0;
    }

    public function markAsViewed($userId) {
        $vues = $this->vues ?? [];
        if (!in_array($userId, $vues)) {
            $vues[] = $userId;
            $this->vues = $vues;
            $this->save();
        }
    }
}
