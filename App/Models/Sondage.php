<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Sondage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'titre',
        'slug',
        'description',
        'thematique',
        'cover_image',
        'lang',
        'options',        // JSON
        'votes',          // JSON
        'total_votes',
        'allow_multiple',
        'anonymous',
        'reactions_enabled',
        'is_public',
        'pinned',
        'tags',
        'views',
        'share_count',
        'boost_count',
        'trending_score',
        'allow_remix',
        'remixed_from_id',
        'expires_at',
        'voter_ids',
    ];

    protected $casts = [
        'options' => 'array',
        'votes' => 'array',
        'tags' => 'array',
        'expires_at' => 'datetime',
        'voter_ids' => 'array',
        'allow_multiple' => 'boolean',
        'anonymous' => 'boolean',
        'reactions_enabled' => 'boolean',
        'is_public' => 'boolean',
        'pinned' => 'boolean',
        'allow_remix' => 'boolean',
    ];

    /* ---------- Relations ---------- */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function remixedFrom()
    {
        return $this->belongsTo(self::class, 'remixed_from_id');
    }

    public function remixes()
    {
        return $this->hasMany(self::class, 'remixed_from_id');
    }

    /* ---------- Boot / Slug ---------- */
    protected static function booted()
    {
        static::creating(function ($sondage) {
            if (empty($sondage->slug)) {
                $sondage->slug = Str::slug($sondage->titre) . '-' . Str::random(5);
            }
        });
    }

    /* ---------- Helpers ---------- */

    // Check expiration
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Retourne votes par option (assure la structure)
    public function getVotesArray(): array
    {
        $optionsCount = is_array($this->options) ? count($this->options) : 0;
        $votes = $this->votes ?? [];
        // normalize: ensure numeric keys 0..n-1 exist
        $result = [];
        for ($i = 0; $i < $optionsCount; $i++) {
            $result[$i] = isset($votes[$i]) ? (int)$votes[$i] : 0;
        }
        return $result;
    }

    // Voter : $userId optionnel
    public function voter(int $index, $userId = null)
    {
        if ($this->isExpired()) {
            return false;
        }

        // init votes
        $votes = $this->votes ?? [];
        $votes[$index] = ($votes[$index] ?? 0) + 1;
        $this->votes = $votes;

        // total votes increment (approx)
        $this->total_votes = ($this->total_votes ?? 0) + 1;

        // enregistrer voter_id si pas anonyme
        if (!$this->anonymous && $userId) {
            $voterIds = $this->voter_ids ?? [];
            $voterIds[] = $userId;
            // garder unique
            $this->voter_ids = array_values(array_unique($voterIds));
        }

        // mettre à jour trending simple
        $this->trending_score = $this->trending_score + 1;

        $this->save();

        return true;
    }

    // Vérifier si un utilisateur peut voter
    public function canVote($userId = null): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->anonymous) {
            // autoriser (nous ne trackons pas)
            return true;
        }

        if (!$userId) {
            // utilisateur non connecté ne peut pas voter
            return false;
        }

        $voterIds = $this->voter_ids ?? [];
        return !in_array($userId, $voterIds);
    }

    
   // Statistiques en % des votes
public function stats(): array
{
    $votes = $this->getVotesArray();
    $total = $this->total_votes ?? array_sum($votes);

    // éviter division par zéro
    if ($total <= 0) {
        $percent = array_fill(0, count($votes), 0);
    } else {
        $percent = [];
        foreach ($votes as $i => $count) {
            $percent[$i] = round(($count / $total) * 100, 1);
        }
    }

    return [
        'counts' => $votes,
        'percent' => $percent,
        'total' => $total,
    ];
}


    // Simple accessor pour cover (fallback)
    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            return $this->cover_image;
        }
        // placeholder selon thématique
        return '/images/placeholders/sondage-'.$this->thematique.'.png';
    }

    // Remix helper (crée un nouvel array ready pour duplication)
    public function prepareRemixAttributes($overrides = []): array
    {
        return array_merge($this->only([
            'titre',
            'thematique',
            'description',
            'cover_image',
            'options',
            'lang',
        ]), $overrides);
    }
}
