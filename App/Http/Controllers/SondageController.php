<?php
namespace App\Http\Controllers;

use App\Models\Sondage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SondageController extends Controller
{
    // Liste des sondages + sondage du moment
    public function index()
    {
        $sondages = Sondage::latest()->paginate(9);
        $featured = Sondage::inRandomOrder()->first();

        // Ici on ne touche ni aux votes ni aux vues
        return view('sondages.index', compact('sondages', 'featured'));
    }

    public function show($id)
    {
        $sondage = Sondage::with('user')->findOrFail($id);

        // Gestion des vues uniques par utilisateur connecté ou par session
        $userId = Auth::id();
        $sessionKey = 'sondage_viewed_' . $sondage->id;

        if ($userId) {
            if ($userId !== $sondage->user_id && !session()->has($sessionKey)) {
                $sondage->increment('views');
                session()->put($sessionKey, true);
            }
        } else {
            if (!session()->has($sessionKey)) {
                $sondage->increment('views');
                session()->put($sessionKey, true);
            }
        }

        // Normaliser les options
        $sondage->options = array_map(function($opt){
            return [
                'label' => $opt['label'] ?? '',
                'image' => $opt['image'] ?? null,
            ];
        }, $sondage->options ?? []);

        return view('sondages.show', compact('sondage'));
    }

    // Formulaire de création
    public function create()
    {
        return view('sondages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'options' => 'required|array|min:2',
            'expires_in' => 'required|in:24,72,168',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $coverPath = $request->hasFile('cover_image') ? $request->file('cover_image')->store('covers', 'public') : null;

        $options = $request->options;

        $sondage = Sondage::create([
            'user_id' => Auth::id(),
            'titre' => $request->titre,
            'thematique' => $request->thematique,
            'description' => $request->description,
            'options' => $options,
            'votes' => array_fill(0, count($options), 0),
            'views' => 0,
            'total_votes' => 0,
            'expires_at' => now()->addHours((int) $request->expires_in),
            'cover_image' => $coverPath,
            'slug' => Str::slug($request->titre) . '-' . uniqid(),
        ]);

        return redirect()->route('sondage.show', $sondage->id)
                         ->with('success', 'Sondage créé avec succès 🎉');
    }

    // Vote uniquement depuis show
    public function vote(Request $request, $id)
    {
        $sondage = Sondage::findOrFail($id);
        $userId = auth()->id();

        // Vérification que la requête vient bien de show()
        if ($request->input('from_show') !== '1') {
            return response()->json([
                'status' => 'error',
                'message' => 'Le vote n\'est autorisé que depuis la page du sondage.'
            ]);
        }

        if (!$sondage->canVote($userId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous avez déjà voté pour ce sondage.'
            ]);
        }

        $index = (int) $request->input('option');
        $sondage->voter($index, $userId);

        $stats = $sondage->stats();

        return response()->json([
            'status' => 'success',
            'message' => 'Vote enregistré, merci !',
            'stats' => $stats
        ]);
    }

    public function historique($user = null)
    {
        $userId = $user ?? auth()->id();
        $mesSondages = Sondage::where('user_id', $userId)->latest()->paginate(9);
        return view('sondages.historique', compact('mesSondages'));
    }

    public function resultats($id)
    {
        $sondage = Sondage::findOrFail($id);

        $sondage->options = array_map(function($opt){
            return [
                'label' => $opt['label'] ?? '',
                'image' => $opt['image'] ?? null,
            ];
        }, $sondage->options ?? []);

        $stats = $sondage->stats();

        return view('sondages.resultats', compact('sondage', 'stats'));
    }

    public function remix($id)
    {
        $original = Sondage::findOrFail($id);

        $remixData = [
            'titre' => $original->titre,
            'description' => $original->description,
            'thematique' => $original->thematique,
            'options' => array_map(function($opt) {
                return [
                    'label' => $opt['label'] ?? '',
                    'image' => null,
                ];
            }, $original->options ?? []),
        ];

        return view('sondages.create', ['original' => $remixData]);
    }

    public function destroy($id)
    {
        $sondage = Sondage::findOrFail($id);

        if ($sondage->user_id === Auth::id()) {
            $sondage->delete();
            return redirect()->route('sondage.historique', Auth::id())
                             ->with('success', 'Sondage supprimé');
        }

        return back()->with('error', 'Action non autorisée ❌');
    }
}
