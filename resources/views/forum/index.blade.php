@extends('layouts.app')
@section('content')
<div class="container py-4">

    {{-- Message flash --}}
    @if(session('success'))
        <div class="alert alert-success text-center shadow-sm rounded-pill">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- En-tête forum --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-people text-primary"></i> Forum communautaire
        </h4>
        <a href="{{ route('forum.create') }}" class="btn btn-primary rounded-pill shadow-sm">
            <i class="bi bi-plus-circle"></i> Poser une question
        </a>
    </div>

    {{-- Filtres thématiques --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('forum.index') }}" 
           class="badge rounded-pill px-3 py-2 {{ request('thematique') ? 'bg-light text-dark border' : 'bg-primary text-white' }}" style="background: rgba(255,255,255,0.6);">
            <i class="bi bi-globe"></i> Toutes
        </a>
        @foreach($thematiques as $t)
            <a href="{{ route('forum.index', ['thematique' => $t]) }}" 
               class="badge rounded-pill px-3 py-2 {{ request('thematique') === $t ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
               <i class="bi bi-hash"></i> {{ $t }}
            </a>
        @endforeach
    </div>

   {{-- Tri --}}
<ul class="nav nav-pills mb-4 shadow-sm rounded-3 p-2 flex-wrap" style="background: rgba(255,255,255,0.6);">
    <li class="nav-item">
        <a href="?sort=toutes" class="nav-link {{ request('sort')=='toutes' ? 'active' : '' }}">
            <i class="bi bi-collection"></i> Toutes
        </a>
    </li>
    <li class="nav-item">
        <a href="?sort=recentes" class="nav-link {{ request('sort')=='recentes' || !request('sort') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Récentes
        </a>
    </li>
    <li class="nav-item">
        <a href="?sort=populaires" class="nav-link {{ request('sort')=='populaires' ? 'active' : '' }}">
            <i class="bi bi-fire"></i> Populaires
        </a>
    </li>
    <li class="nav-item">
        <a href="?sort=non_resolues" class="nav-link {{ request('sort')=='non_resolues' ? 'active' : '' }}">
            <i class="bi bi-question-circle"></i> Non résolues
        </a>
    </li>
</ul>


    {{-- Liste des questions --}}
    @forelse($questions as $q)
        <div class="card mb-3 border-0 shadow-sm rounded-4 forum-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="fw-bold text-primary">
                        <a href="{{ route('forum.show', $q->id) }}" class="text-decoration-none text-primary">{{ $q->titre }}</a>
                    </h5>

                    {{-- Suppression par auteur --}}
                    @if(Auth::id() === $q->user_id)
                        <form action="{{ route('forum.destroy', $q->id) }}" method="POST"
                              onsubmit="return confirm('Supprimer cette question ?')" class="ms-2">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <p class="small text-muted mb-3">{{ Str::limit($q->contenu, 180) }}</p>

                {{-- Infos auteur et stats --}}
                <div class="d-flex justify-content-between align-items-center small">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $q->user->photo ? asset('storage/'.$q->user->photo) : asset('images/default-avatar.png') }}"
                             alt="avatar" class="rounded-circle" width="36" height="36" style="object-fit: cover;">
                        <div class="text-muted">
                            <strong>{{ $q->user->pseudo }}</strong>
                            <span class="badge bg-info text-dark ms-2">
                                <i class="bi bi-tag"></i> {{ $q->thematique }}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
    <span><i class="bi bi-eye text-secondary"></i> {{ $q->vues }}</span>

    {{-- page de la question --}}
    <a href="{{ route('forum.show', $q->id) }}" class="text-decoration-none text-primary">
        <i class="bi bi-chat-dots"></i> {{ $q->reponses_count ?? $q->reponses->count() }}
    </a>

    <button class="btn btn-sm btn-outline-danger border-0 p-0 favori-btn" data-id="{{ $q->id }}">
        <i class="bi {{ session()->has('fav_'.$q->id) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
        <span id="fav-count-{{ $q->id }}">{{ $q->favoris }}</span>
    </button>
</div>

                </div>
            </div>
        </div>
    @empty
        <p class="text-muted text-center py-5">
            <i class="bi bi-inbox"></i> Aucune question trouvée.
        </p>
    @endforelse

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $questions->links() }}
    </div>
</div>

<script>
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
.badge{cursor:pointer;transition:all 0.2s ease;}
.badge:hover{transform:translateY(-2px);}
.card{background-color:var(--bs-body-bg);transition:all 0.3s ease;}
.card:hover{transform:translateY(-3px);box-shadow:0 4px 14px rgba(0,0,0,0.1);}
.forum-card{background: rgba(255,255,255,0.6);color:#ddd;}
</style>
@endsection
