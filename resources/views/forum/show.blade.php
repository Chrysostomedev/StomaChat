@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- 🧠 Détails de la question --}}
    <div class="card mb-4 shadow-sm border-0 rounded-4 forum-card">
        <div class="card-body d-flex flex-column gap-3">

            <div class="d-flex align-items-center mb-3">
                <img src="{{ $question->user->photo ? asset('storage/'.$question->user->photo) : asset('images/default-avatar.png') }}"
                     alt="avatar" class="rounded-circle me-2" width="48" height="48" style="object-fit: cover;">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $question->user->pseudo }}</h5>
                    <small class="text-muted">{{ $question->created_at->diffForHumans() }}</small>
                </div>

                @if($question->user_id === auth()->id())
                    <form action="{{ route('forum.destroy', $question->id) }}" method="POST" 
                          onsubmit="return confirm('Supprimer cette question ?')" class="ms-auto">
                        @csrf @method('DELETE')
                        <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                    </form>
                @endif
            </div>

            <h4 class="fw-bold">{{ $question->titre }}</h4>
            <p class="text-muted">{{ $question->contenu }}</p>

            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-info">{{ $question->thematique }}</span>
                <div class="d-flex align-items-center gap-3">
                    <small><i class="bi bi-eye"></i> {{ $question->vues }}</small>
                    <small><i class="bi bi-chat-dots"></i> {{ $question->reponses->count() }}</small>
                    <button class="btn btn-sm btn-outline-danger border-0 p-0 favori-btn" data-id="{{ $question->id }}">
                        <i class="bi {{ session()->has('fav_'.$question->id) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                        <span class="small ms-1" id="fav-count-{{ $question->id }}">{{ $question->favoris }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>



   {{-- Ajouter une réponse --}}
@auth
<h6 class="fw-bold mb-3"><i class="bi bi-chat-left-text"></i> Ajouter une réponse</h6>
<form action="{{ route('reponse.store', $question->id) }}" method="POST" class="mb-4" id="reply-form">
    @csrf
    <div class="d-flex gap-2">
        <textarea name="contenu" class="form-control" rows="2" placeholder="Ta réponse..." required></textarea>
        <button class="btn btn-primary"><i class="bi bi-send"></i></button>
    </div>
</form>
@endauth

{{-- Liste des réponses --}}
<h6 class="fw-bold mb-3"><i class="bi bi-chat-left-text"></i> Toutes les réponses</h6>
<div id="responses-container">
    @forelse($question->reponses as $reponse)
        <div class="card mb-3 border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <img src="{{ $reponse->user->photo ? asset('storage/'.$reponse->user->photo) : asset('images/default-avatar.png') }}"
                         alt="avatar" class="rounded-circle me-2" width="36" height="36" style="object-fit: cover;">
                    <div>
                        <strong>{{ $reponse->user->pseudo }}</strong>
                        <small class="text-muted d-block">{{ $reponse->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <p class="mb-0">{{ $reponse->contenu }}</p>
            </div>
        </div>
    @empty
        <p class="text-muted">Aucune réponse pour le moment.</p>
    @endforelse
</div>


    {{--  Bloc Questions populaires --}}
    @include('forum.partials._reponse', ['questionsPopulaires' => $questionsPopulaires])

</div>

{{--  Réponses --}}
<script>
const form = document.getElementById('reply-form');
if(form){
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const contenu = form.querySelector('[name="contenu"]').value.trim();
        if(!contenu) return;

        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ contenu })
        });

        const data = await res.json().catch(() => null);

        if (data && data.success) {
            const container = document.getElementById('responses-container');
            const html = `
                <div class="card mb-3 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <img src="${data.reponse.photo}" alt="avatar"
                                 class="rounded-circle me-2" width="36" height="36"
                                 style="object-fit: cover;">
                            <div>
                                <strong>${data.reponse.auteur}</strong>
                                <small class="text-muted d-block">${data.reponse.date}</small>
                            </div>
                        </div>
                        <p class="mb-0">${data.reponse.contenu}</p>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            form.reset();
        } else {
            alert("Erreur lors de l’envoi de la réponse 😕");
        }
    });
}

document.querySelectorAll('.favori-btn').forEach(btn => {
    btn.addEventListener('click', async e => {
        e.preventDefault();
        const id = btn.dataset.id;
        const icon = btn.querySelector('i');
        const countEl = document.getElementById('fav-count-' + id);
        const res = await fetch(`/forum/${id}/favori`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest'}
        });
        const data = await res.json();
        if (data.success) {
            countEl.textContent = data.favoris;
            if (data.isFav) { icon.classList.replace('bi-heart','bi-heart-fill'); icon.classList.add('text-danger'); }
            else { icon.classList.replace('bi-heart-fill','bi-heart'); icon.classList.remove('text-danger'); }
        }
    });
});
</script>

<style>
.forum-card:hover {transform:translateY(-3px); transition:all 0.3s ease;}
[data-bs-theme="dark"] .forum-card {background-color:#1a1a2e; color:#ddd;}
</style>
@endsection
