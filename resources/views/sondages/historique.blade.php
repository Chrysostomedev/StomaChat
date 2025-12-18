@extends('layouts.app')

@section('content')
<style>
.card-sondage {
    background: rgba(255,255,255,0.9);
    border-radius: 12px;
    padding: 1rem;
}
body.dark-mode .card-sondage {
    background: rgba(15,20,45,0.85);
    border: 1px solid rgba(0,255,255,0.15);
}
.text-cyan { color:#007bff; }
body.dark-mode .text-cyan { color:#00FFFF; }
</style>

<div class="container py-5">
  <h2 class="fw-bold text-cyan mb-4"><i class="bi bi-clock-history me-2"></i>Mon Historique de sondages</h2>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-4">
    @forelse($mesSondages as $sondage)
    <div class="col-md-6 col-lg-4">
      <div class="card-sondage p-3">
        <div class="d-flex justify-content-between align-items-center">
          <span class="badge bg-info text-dark">{{ $sondage->thematique }}</span>
          <small class="text-muted">{{ $sondage->created_at->diffForHumans() }}</small>
        </div>
        <h5 class="mt-2">{{ $sondage->titre }}</h5>
        <p class="small text-muted">{{ Str::limit($sondage->description,80) }}</p>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>
            <i class="bi bi-person-vote text-cyan me-1"></i>{{ $sondage->total_votes }}
            <i class="bi bi-eye text-cyan ms-3 me-1"></i>{{ $sondage->views }}
          </div>
          <a href="{{ route('sondage.resultats',$sondage->id) }}" class="btn btn-sm btn-outline-info" title="Voir résultats">
            <i class="bi bi-graph-up"></i>
          </a>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted">
      Vous n'avez créé aucun sondage pour le moment.
    </div>
    @endforelse
  </div>

  <div class="mt-5 d-flex justify-content-center">
    {{ $mesSondages->links() }}
  </div>
</div>
@endsection
