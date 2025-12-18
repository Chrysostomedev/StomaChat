@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-3"><i class="bi bi-person-lines-fill"></i> Demandes d’amis reçues</h4>

    @if($demandes->isEmpty())
        <p class="text-muted text-center mt-4">Aucune demande reçue pour le moment 🤝</p>
    @else
        <div class="list-group">
            @foreach($demandes as $demande)
                @php $user = $demande->demandeur; @endphp
                <div class="list-group-item friend-request d-flex align-items-center justify-content-between flex-wrap p-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('images/default-user.png') }}" 
                             class="rounded-circle friend-photo" alt="{{ $user->pseudo }}">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $user->pseudo }}</h6>
                            <p class="text-muted small mb-0">{{ $user->centre_interet ?? 'Aucun centre d’intérêt' }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <form action="{{ route('friends.accepter', $demande->id) }}" method="POST" class="friend-action-form">
                            @csrf
                            <button class="btn btn-success rounded-circle"><i class="bi bi-check-lg"></i></button>
                        </form>
                        <form action="{{ route('friends.refuser', $demande->id) }}" method="POST" class="friend-action-form">
                            @csrf
                            <button class="btn btn-outline-danger rounded-circle"><i class="bi bi-x-lg"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.friend-photo {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border: 2px solid #25D366;
}
.friend-request {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}
.friend-request:hover {
    transform: scale(1.01);
    transition: 0.2s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.friend-action-form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(form);
            const item = form.closest('.list-group-item');

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': formData.get('_token') },
                body: formData
            })
            .then(r => r.ok ? r.text() : Promise.reject(r))
            .then(() => { item.style.opacity = 0; setTimeout(() => item.remove(), 300); })
            .catch(() => alert('Erreur, réessayez.'));
        });
    });
});
</script>
@endsection
