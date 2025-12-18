@php
    $isMe = $message->expediteur_id === auth()->id();
    $files = is_array($message->fichier) ? $message->fichier : json_decode($message->fichier, true) ?? [];
    $isVueUnique = $message->vue_unique;
@endphp

<div id="msg-{{ $message->id }}" 
     class="message-bubble {{ $isMe ? 'me' : 'other' }}" 
     data-id="{{ $message->id }}" 
     data-vue-unique="{{ $isVueUnique ? 1 : 0 }}">

    {{-- Contenu texte --}}
    @if($message->contenu)
        <div @if(!$isMe && $isVueUnique) onclick="viewUnique(this, '{{ $message->id }}')" class="cursor-pointer" @endif>
            {{ $message->contenu }}
        </div>
    @endif

    {{-- Fichiers --}}
    @foreach($files as $f)
        @php
            $ext = pathinfo($f, PATHINFO_EXTENSION);
            $url = asset('storage/' . $f);
        @endphp

        @if(in_array($ext, ['jpg','jpeg','png','gif']))
            <img src="{{ $url }}" class="chat-media mt-1" 
                 @if(!$isMe && $isVueUnique) onclick="viewUnique(this, '{{ $message->id }}')" class="cursor-pointer" @endif>
        @elseif($ext === 'mp4')
            <video controls class="chat-media mt-1" 
                   @if(!$isMe && $isVueUnique) onclick="viewUnique(this, '{{ $message->id }}')" class="cursor-pointer" @endif>
                <source src="{{ $url }}" type="video/mp4">
            </video>
        @elseif($ext === 'pdf')
            @if(!$isVueUnique)
                <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm mt-1 w-100">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
            @endif
        @endif
    @endforeach

    {{-- Info message et actions --}}
    <div class="d-flex justify-content-end align-items-center mt-1 small text-muted">
        <span class="me-2">{{ $message->created_at->format('H:i') }}</span>

        @if($isMe)
            @if($message->lu)
                <i class="bi bi-check2-all text-primary"></i>
            @else
                <i class="bi bi-check2"></i>
            @endif
        @endif

        @if($isMe)
            <div class="dropdown ms-2">
                <i class="bi bi-three-dots cursor-pointer" id="dropdownMenu{{ $message->id }}" data-bs-toggle="dropdown" aria-expanded="false"></i>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $message->id }}">
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="editMessage('{{ $message->id }}', '{{ addslashes($message->contenu) }}')">Modifier</a></li>
                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDelete('{{ $message->id }}')">Supprimer</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="copyMessage('{{ addslashes($message->contenu) }}')">Copier</a></li>
                </ul>
            </div>
        @endif
    </div>
</div>

<script>
    // Copier le message
    function copyMessage(contenu) {
        navigator.clipboard.writeText(contenu);
        alert('Message copié !');
    }

    // Vue unique côté destinataire (disparition automatique 5 à 10s)
    function viewUnique(el, id) {
        if(el.dataset.viewed) return; // éviter double clic
        el.dataset.viewed = '1';

        // Affichage semi-transparent
        el.style.opacity = '0.3';

        // Durée aléatoire entre 5 et 10 secondes
        const duration = 5000 + Math.random() * 5000;

        setTimeout(() => {
            const msg = document.getElementById('msg-' + id);
            if(msg) msg.remove();

            // Marquer comme lu et supprimer côté serveur
            fetch(`/messages/${id}/viewed`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        }, duration);
    }
</script>

<style>
.cursor-pointer { cursor: pointer; }
</style>
