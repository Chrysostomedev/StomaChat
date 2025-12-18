@extends('layouts.app')

@section('content')
<div class="container py-5">

  <div class="text-center mb-4">
    @if($user->photo)
      <img src="{{ asset('storage/'.$user->photo) }}" class="rounded-circle shadow" style="width:120px;height:120px;object-fit:cover;">
    @else
      @php
          srand($user->id);
          $colors = ['#0d6efd','#6610f2','#6f42c1','#d63384','#fd7e14','#20c997','#198754','#0dcaf0'];
          $bg = $colors[array_rand($colors)];
          srand();
          $desc = strtolower($user->description ?? '');
          $emoji = '🙂';
          if (str_contains($desc, 'musique')) $emoji = '🎵';
          elseif (str_contains($desc, 'voyage')) $emoji = '🌍';
          elseif (str_contains($desc, 'photo')) $emoji = '📸';
          elseif (str_contains($desc, 'sport')) $emoji = '⚽';
          elseif (str_contains($desc, 'dev') || str_contains($desc, 'code')) $emoji = '💻';
          elseif (str_contains($desc, 'lecture')) $emoji = '📚';
      @endphp
      <div class="rounded-circle d-flex justify-content-center align-items-center mx-auto shadow" style="width:120px;height:120px;background:{{ $bg }};font-size:45px;color:white;">
        {{ $emoji }}
      </div>
    @endif

    <h3 class="fw-bold mt-3">{{ $user->pseudo }}</h3>
    <p class="text-muted">{{ $user->email }}</p>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h5 class="card-title mb-3"><i class="bi bi-person-vcard me-2 text-info"></i> Informations personnelles</h5>
      <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>Âge :</strong> {{ $user->age ?? 'Non précisé' }}</li>
        <li class="list-group-item"><strong>Description :</strong> {{ $user->description ?? 'Aucune' }}</li>
        <li class="list-group-item"><strong>Centres d’intérêt :</strong> {{ $user->centre_interet ?? '—' }}</li>
        <li class="list-group-item"><strong>Profession :</strong> {{ $user->profession ?? '—' }}</li>
        <li class="list-group-item"><strong>Membre depuis :</strong> {{ $user->created_at->format('d M Y') }}</li>
      </ul>
      <div class="mt-4 text-center">
        <a href="{{route('profile.edit')}}" class="btn btn-outline-info px-4"><i class="bi bi-pencil-square me-2"></i>Modifier le profil</a>
      </div>
    </div>
  </div>
</div>
@endsection
