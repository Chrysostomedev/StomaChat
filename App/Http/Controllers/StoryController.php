<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoryController extends Controller
{
    // Liste des stories (index)
   public function index()
{
    $now = Carbon::now();
    $user = Auth::user();

    // Récupérer les IDs des amis acceptés
    $amisIds = \App\Models\Friend::where(function($q) use($user){
            $q->where('demandeur_id', $user->id)
              ->orWhere('receveur_id', $user->id);
        })
        ->where('statut', \App\Models\Friend::STATUT_ACCEPTE)
        ->get()
        ->map(function($friend) use($user){
            // On prend l'autre user
            return $friend->demandeur_id === $user->id ? $friend->receveur_id : $friend->demandeur_id;
        })
        ->toArray();

    // Ajouter l'utilisateur connecté pour qu'il voie ses propres stories
    $allowedUserIds = array_merge($amisIds, [$user->id]);

    // Stories des amis + moi
    $usersWithStories = Story::whereIn('user_id', $allowedUserIds)
        ->where('expire_le', '>', $now)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('user_id');

    // Mes propres stories
    $myStories = Story::where('user_id', $user->id)
        ->where('expire_le', '>', $now)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('stories.index', compact('usersWithStories', 'myStories'));
}

    // Formulaire de création de story
    public function create()
    {
        return view('stories.create');
    }

    // Stocker une nouvelle story
    public function store(Request $request)
    {
        $request->validate([
            'texte' => 'nullable|string|max:300',
            'background_color' => 'nullable|string|max:7',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:10240'
        ]);

        $mediaPaths = [];

        if($request->hasFile('media')){
            foreach($request->file('media') as $file){
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('stories', $filename, 'public');
                $mediaPaths[] = $path;
            }
        }

        $story = Story::create([
            'user_id' => Auth::id(),
            'texte' => $request->texte,
            'background_color' => $request->background_color ?? '#75e4ecff',
            'media' => $mediaPaths,
            'expire_le' => Carbon::now()->addHours(24),
        ]);

        return redirect()->route('stories.index')->with('success', 'Story créée avec succès ✅');
    }

    // Afficher une story
    public function show(Story $story)
    {
        if($story->expire_le < Carbon::now()){
            abort(404, 'Story expirée.');
        }

        $userStories = Story::where('user_id', $story->user_id)
            ->where('expire_le', '>', Carbon::now())
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer la story comme vue
        $story->markAsViewed(Auth::id());

        $storiesForJS = $userStories->map(function($s){
            return [
                'id' => $s->id,
                'media' => $s->media ?? [],
                'texte' => $s->texte,
                'background_color' => $s->background_color ?? '#000',
                'vues' => $s->vuesCount()
            ];
        });

        return view('stories.show', compact('userStories', 'storiesForJS', 'story'));
    }

    // Supprimer sa story
    public function destroy(Story $story)
    {
        if($story->user_id !== Auth::id()){
            abort(403);
        }

        // Supprimer les fichiers médias
        if($story->media){
            foreach($story->media as $file){
                if(Storage::disk('public')->exists($file)){
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $story->delete();

        return redirect()->route('stories.index')->with('success', 'Story supprimée ✅');
    }
}
