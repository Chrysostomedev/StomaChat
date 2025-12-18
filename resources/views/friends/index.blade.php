@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-3"><i class="bi bi-chat-dots"></i> Discussions</h4>

    {{-- Barre de recherche --}}
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Rechercher un ami ou un message" id="searchFriends">
    </div>

 @php
        use Carbon\Carbon;
        Carbon::setLocale('fr');
 @endphp

    @if($friends->isEmpty())
        <p class="text-muted text-center mt-4">Vous n’avez pas encore d’amis </p>
    @else
        <div id="friends-list" class="list-group">
            @foreach($friends as $friend)
                @php
                    $lastMessageFull = $friend->lastMessage ? $friend->lastMessage->contenu : '';
                @endphp
                <a href="{{ route('conversations.show', $friend) }}" 
                   class="list-group-item list-group-item-action d-flex align-items-center justify-content-between px-3 py-2"
                   data-name="{{ strtolower($friend->pseudo) }}"
                   data-message="{{ strtolower($lastMessageFull) }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <img src="{{ $friend->photo ? asset('storage/'.$friend->photo) : asset('images/default-user.png') }}" 
                                 alt="{{ $friend->pseudo }}" class="rounded-circle friend-avatar-lg">
                            <span class="status-dot"></span>
                        </div>
                        <div class="friend-text">
                            <div class="fw-bold">{{ $friend->pseudo }}</div>
                            <div class="small text-muted text-truncate">
                                @if($friend->lastMessage)
                                    @if($friend->lastMessage->vue_unique)
                                        Message éphémère
                                    @else
                                        {{ Str::limit($friend->lastMessage->contenu, 30) }}
                                    @endif
                                @else
                                    Aucun message
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        @if($friend->lastMessage)
                            <div class="small text-muted">{{ $friend->lastMessage->created_at->translatedFormat('d F Y à H:i') }}</div>
                        @endif
                        @if($friend->unreadCount > 0)
                            <span class="badge bg-danger rounded-pill">{{ $friend->unreadCount }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

<style>
.friend-avatar-lg {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border: 4px solid #4cece4ff;
}
.status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 12px;
    height: 12px;
    background: #28a745;
    border: 2px solid #fff;
    border-radius: 50%;
}
.list-group-item:hover {
    background-color: #3fecdeff;
}
.highlight {
    background-color: yellow;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchFriends');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();
        const terms = query.split(/\s+/);

        document.querySelectorAll('#friends-list a').forEach(item => {
            const name = item.dataset.name || '';
            const message = item.dataset.message || '';

            // Vérifie que tous les mots recherchés sont présents
            const matches = terms.every(term => name.includes(term) || message.includes(term));
            item.style.display = matches ? 'flex' : 'none';

            // Highlight
            const nameEl = item.querySelector('.fw-bold');
            const msgEl = item.querySelector('.text-muted.text-truncate');

            if(query && matches) {
                if(nameEl) {
                    nameEl.innerHTML = nameEl.textContent.replace(new RegExp(`(${terms.join('|')})`, 'gi'), '<span class="highlight">$1</span>');
                }
                if(msgEl) {
                    msgEl.innerHTML = msgEl.textContent.replace(new RegExp(`(${terms.join('|')})`, 'gi'), '<span class="highlight">$1</span>');
                }
            } else {
                // reset
                if(nameEl) nameEl.textContent = nameEl.textContent;
                if(msgEl) msgEl.textContent = msgEl.textContent;
            }
        });
    });
});
</script>
@endsection
