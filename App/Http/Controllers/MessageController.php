<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // 📤 Envoi message
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'contenu' => 'nullable|string|max:2000',
            'fichier.*' => 'nullable|file|max:10240',
            'vue_unique' => 'nullable|boolean',
        ]);

        $data = [
            'expediteur_id' => Auth::id(),
            'vue_unique' => $request->boolean('vue_unique', false),
        ];

        if ($request->filled('contenu')) $data['contenu'] = $request->contenu;

        if ($request->hasFile('fichier')) {
            $paths = [];
            foreach ($request->file('fichier') as $file) $paths[] = $file->store('messages', 'public');
            $data['fichier'] = $paths;
        }

        $message = $conversation->messages()->create($data);
        $message->load('expediteur');

        return response()->json([
            'status' => 'success',
            'message' => view('conversations.partials.message', compact('message'))->render(),
            'id' => $message->id
        ]);
    }

    // ✏️ Update message
    public function update(Request $request, Message $message)
    {
        if ($message->expediteur_id !== Auth::id()) return response()->json(['error' => 'Action non autorisée.'], 403);

        $request->validate(['contenu' => 'required|string|max:2000']);

        $message->update(['contenu' => $request->contenu, 'modifie' => true]);

        return response()->json([
            'status' => 'success',
            'updated' => true,
            'html' => view('conversations.partials.message', compact('message'))->render()
        ]);
    }

    // 🗑 Delete message
    public function destroy(Message $message)
    {
        if ($message->expediteur_id !== Auth::id()) return response()->json(['error' => 'Action non autorisée.'], 403);

        if ($message->fichier) {
            foreach ($message->fichier as $f) {
                if (Storage::disk('public')->exists($f)) Storage::disk('public')->delete($f);
            }
        }

        $message->delete();

        return response()->json(['status' => 'success', 'deleted' => true, 'id' => $message->id]);
    }

    // Marquer lus
    public function read(Conversation $conversation)
    {
        $conversation->messages()
            ->where('expediteur_id', '!=', Auth::id())
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['status' => 'ok']);
    }

// POST /messages/{message}/viewed
public function viewed(Message $message)
{
    $userId = Auth::id();

    // L'expéditeur ne peut pas déclencher la vue unique
    if ($message->expediteur_id === $userId) {
        return response()->json(['error' => 'Action non autorisée.'], 403);
    }

    if ($message->vue_unique) {
        // Supprimer fichiers si existants
        if ($message->fichier) {
            foreach ($message->fichier as $f) {
                if (Storage::disk('public')->exists($f)) {
                    Storage::disk('public')->delete($f);
                }
            }
        }
        // Supprimer le message
        $message->delete();

        return response()->json([
            'status' => 'deleted',
            'message_id' => $message->id
        ]);
    }

    // Sinon, marquer juste comme lu
    $message->update(['lu' => true]);

    return response()->json([
        'status' => 'read',
        'message_id' => $message->id
    ]);
}



   public function friends()
{
    $user = Auth::user();

    $friends = $user->friends()->get(); // récupère uniquement les amis confirmés

    foreach ($friends as $friend) {
        $conversation = $user->conversationWith($friend);

        if ($conversation) {
            $lastMessage = $conversation->messages()->latest()->first();
            $unreadCount = $conversation->messages()
                ->where('expediteur_id', $friend->id)
                ->where('lu', false)
                ->count();
        } else {
            $lastMessage = null;
            $unreadCount = 0;
        }

        $friend->lastMessage = $lastMessage;
        $friend->unreadCount = $unreadCount;
    }

    return view('friends.index', compact('friends'));
}

}
