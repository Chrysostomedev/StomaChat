@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h4 class="fw-bold mb-3">Stories</h4>

    <div class="d-flex align-items-center overflow-auto mb-3">

        {{-- Story du user connecté --}}
        @if($myStories->count())
            @php $latestMine = $myStories->last(); @endphp
            <div class="text-center me-3 position-relative">
                <a href="{{ route('stories.show', $latestMine->id) }}" class="d-block position-relative">
                    <img src="{{ Auth::user()->photo ? asset('storage/'.Auth::user()->photo) : asset('images/default-user.png') }}" 
                         class="rounded-circle border border-primary" 
                         style="width:70px;height:70px;object-fit:cover;">
                    <small class="d-block mt-1"></small>
                </a>
                {{-- Petit + pour créer une nouvelle story --}}
                <a href="{{ route('stories.create') }}" class="position-absolute bottom-0 start-50 translate-middle-x" 
                   style="width:24px;height:24px;background:#000;border-radius:50%;border:2px solid #0d6efd;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-plus text-primary" style="font-size:14px;"></i>
                </a>
            </div>
        @else
            {{-- Si aucune story, afficher le + pour créer --}}
            <div class="text-center me-3">
                <a href="{{ route('stories.create') }}" class="d-block position-relative">
                    <div class="rounded-circle border border-primary d-flex justify-content-center align-items-center" 
                         style="width:70px;height:70px;background:#000;">
                        <i class="bi bi-plus text-primary fs-3"></i>
                    </div>
                    <small>Moi</small>
                </a>
            </div>
        @endif

        {{-- Stories des amis uniquement --}}
        @foreach($usersWithStories as $userId => $stories)
            @continue($userId === Auth::id()) {{-- On ignore la sienne déjà affichée --}}
            @php $latest = $stories->last(); @endphp
            <div class="text-center me-3">
                <a href="{{ route('stories.show', $latest->id) }}" class="d-block position-relative">
                    <img src="{{ $latest->user->photo ? asset('storage/'.$latest->user->photo) : asset('images/default-user.png') }}" 
                         class="rounded-circle border border-primary" 
                         style="width:70px;height:70px;object-fit:cover;">
                    <small class="d-block mt-1">{{ $latest->user->pseudo }}</small>
                </a>
            </div>
        @endforeach

    </div>
</div>

<style>
/* Scroll horizontal */
.container > .d-flex {
    scrollbar-width: thin;
}
.container > .d-flex::-webkit-scrollbar {
    height: 6px;
}
.container > .d-flex::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.3);
    border-radius: 3px;
}

/* Petit + sur story existante */
.position-relative a.position-absolute i {
    pointer-events: none;
}
</style>
@endsection
