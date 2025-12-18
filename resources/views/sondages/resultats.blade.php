@extends('layouts.app')

@section('content')
<style>
.card-sondage {
    background: rgba(255,255,255,0.9);
    border-radius: 12px;
    padding: 1.5rem;
}
body.dark-mode .card-sondage {
    background: rgba(255,255,255,0.6);
    border: 1px solid rgba(47, 230, 230, 0.94);
}
.text-cyan { color:#007bff; }
body.dark-mode .text-cyan { color:#00FFFF; }
.progress-bar { transition: width 1s; }
</style>

<div class="container py-5">
  <h2 class="fw-bold text-cyan mb-4"><i class="bi bi-bar-chart-line me-2"></i>Résultats du sondage</h2>

  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @php $stats = $sondage->stats(); @endphp
  <div class="card-sondage">
    @foreach($sondage->options as $i=>$opt)
    <div class="mb-3">
      <div class="d-flex justify-content-between">
        <span><i class="bi bi-circle-fill text-cyan me-2"></i>{{ $opt['label'] }}</span>
        <span class="small text-muted">{{ $stats['percent'][$i] }}%</span>
      </div>
      <div class="progress mt-1" style="height:8px;">
        <div class="progress-bar bg-info" style="width: {{ $stats['percent'][$i] }}%;"></div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="mt-4 text-center">
    <a href="{{ route('sondage.show',$sondage->id) }}" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
  </div>
</div>
@endsection
