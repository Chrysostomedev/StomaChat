<div class="container py-4">
    <h4 class="fw-bold mb-3"><i class="bi bi-stars text-success"></i> Suggestions d’amis</h4>

    @if($suggestions->isEmpty())
        <p class="text-muted text-center mt-4">Aucune suggestion pour le moment </p>
    @else
        <div id="suggestions" class="suggestions-scroll d-flex overflow-auto gap-4 py-3">
            @foreach($suggestions as $user)
                <div class="friend-card position-relative text-center flex-shrink-0" data-user-id="{{ $user->id }}">
                    <div class="photo-wrapper mx-auto position-relative">
                        <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('images/default-user.png') }}"
                             class="friend-photo"
                             alt="{{ $user->pseudo }}">
                        <form action="{{ route('friends.envoyer', $user->id) }}" method="POST" class="friend-action-form position-absolute bottom-0 end-0">
                            @csrf
                            <button type="submit" class="btn-add">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                        </form>
                    </div>
                    <div class="mt-2 fw-bold small text-truncate">{{ $user->pseudo }}</div>
                    <div class="text-muted small text-truncate">{{ $user->centre_interet ?? '—' }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ========================== STYLES ========================== --}}
<style>
/* Scroll horizontal esthétique */
.suggestions-scroll {
    background: rgba(255,255,255,0.6);
    border-radius: 20px;
    padding: 20px;
    scroll-behavior: smooth;
    overflow-x: auto;

    /* Cacher la barre de défilement */
    -ms-overflow-style: none;  /* IE & Edge */
    scrollbar-width: none;     /* Firefox */
}
.suggestions-scroll::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.friend-card { width: 110px; transition: transform 0.3s ease; }
.friend-card:hover { transform: scale(1.05); }
.photo-wrapper { width: 90px; height: 90px; border-radius: 50%; border: 3px solid #24e2f0; overflow: hidden; position: relative; }
.friend-photo { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease; }
.friend-card:hover .friend-photo { transform: scale(1.1); }
.btn-add { background-color: #0c0a58; border: none; border-radius: 50%; color: white; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 1rem; box-shadow: 0 0 6px rgba(0,0,0,0.2); transition: 0.2s; }
.btn-add:hover { background-color: #21a3ca; transform: scale(1.15); }
.flash-message {
    position: fixed;
    bottom: 25px;
    left: 50%;
    transform: translateX(-50%);
    background: rgb(8, 95, 0);
    color: white;
    padding: 10px 25px;
    border-radius: 25px;
    font-weight: 500;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 9999;
}
.flash-message.show { opacity: 1; }
</style>

{{-- ========================== SCRIPT ========================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    function attachFriendForms() {
        document.querySelectorAll('.friend-action-form').forEach(form => {

            if(form._listener) form.removeEventListener('submit', form._listener);

            form._listener = function(e) {
                e.preventDefault();
                const card = form.closest('.friend-card');
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': formData.get('_token')
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if(res.success) {
                        showFlash(res.message, true);
                        card.remove();
                        setTimeout(reloadSuggestions, 500);
                    } else {
                        showFlash(res.error || "Erreur, réessayez.", false);
                        card.remove();
                        setTimeout(reloadSuggestions, 500);
                    }
                })
                .catch(() => {
                    showFlash("Erreur, réessayez.", false);
                    card.remove();
                    setTimeout(reloadSuggestions, 500);
                });
            };

            form.addEventListener('submit', form._listener);
        });
    }

    function showFlash(msg, success=true) {
        let flash = document.getElementById('flash-message');
        if(!flash) {
            flash = document.createElement('div');
            flash.id = 'flash-message';
            flash.className = 'flash-message';
            document.body.appendChild(flash);
        }
        flash.textContent = msg;
        flash.style.background = success ? '#25D366' : '#dc3545';
        flash.classList.add('show');
        setTimeout(() => flash.classList.remove('show'), 3000);
    }

    function reloadSuggestions() {
        fetch("{{ route('friends.suggestions') }}", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const container = document.getElementById('suggestions-container');
            if(container) {
                container.innerHTML = html;
                attachFriendForms();
            }
        })
        .catch(err => console.error('Erreur reload suggestions:', err));
    }

    attachFriendForms();
});
</script>
