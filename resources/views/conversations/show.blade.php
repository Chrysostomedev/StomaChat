@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="container-fluid py-3 d-flex flex-column position-relative" style="height: 90vh;">

    {{-- ========= HEADER ========= --}}
    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2 bg-white sticky-top shadow-sm rounded-3" style="z-index:1000; background: rgba(255,255,255,0.6);">
        <div class="d-flex align-items-center">
            <a href="{{ route('friends.index') }}" class="btn btn-link text-dark me-2">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->pseudo }}" 
                     class="rounded-circle me-2 border border-2 border-primary-subtle" 
                     style="width:50px; height:50px; object-fit:cover;">
            @else
                <div class="avatar-circle me-2 shadow-sm" 
                     style="background:#021738; width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:50%; color:white;">
                     {{ strtoupper(substr($user->pseudo,0,2)) }}
                </div>
            @endif
            <div>
                <h5 class="mb-0 fw-bold">{{ $user->pseudo }}</h5>      
            </div>
        </div>
        <button class="btn btn-outline-light border" onclick="toggleSidebar()">
            <i class="bi bi-info-circle text-dark fs-5"></i>
        </button>
    </div>

    {{-- ========= MESSAGES ========= --}}
    <div id="messages" class="flex-grow-1 overflow-auto mb-3 p-3 rounded" 
         style="background: rgba(255,255,255,0.6); border-radius:12px; scroll-behavior:smooth;">
        @foreach($conversation->messages as $message)
            @include('conversations.partials.message', ['message' => $message])
        @endforeach
    </div>

    {{-- ========= FORMULAIRE ENVOI ========= --}}
    <form id="messageForm" action="{{ route('messages.store', $conversation) }}" method="POST" enctype="multipart/form-data" 
          class="d-flex align-items-center gap-2 p-2 border-top bg-white rounded-top shadow-sm">
        @csrf

        <input type="text" name="contenu" id="messageInput" class="form-control rounded-pill px-3" placeholder="Écrire un message..." autocomplete="off">

        
        <label for="fileInput" class="btn btn-light rounded-circle mb-0" title="Joindre un média">
            <i class="bi bi-paperclip fs-4"></i>
        </label>
        <input type="file" name="fichier[]" id="fileInput" class="d-none" multiple>

        
        <button type="button" id="uniqueBtn" class="btn btn-light rounded-circle mb-0" title="Message / média éphémère">
            <i class="bi bi-eye-slash fs-4 text-muted"></i>
        </button>
        <input type="hidden" name="vue_unique" id="vueUniqueInput" value="0">

        {{-- Envoyer --}}
        <button id="sendBtn" class="btn btn-primary rounded-circle px-3" type="submit" title="Envoyer">
            <i class="bi bi-send fs-4"></i>
        </button>
    </form>

    {{-- Aperçu fichiers --}}
    <div id="filePreview" class="p-2 d-flex gap-2 flex-wrap"></div>

    {{-- ========= SIDEBAR FICHIERS ========= --}}
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-folder2-open"></i> Fichiers partagés</h5>
            <button class="btn btn-link text-dark" onclick="toggleSidebar()"><i class="bi bi-x-lg fs-5"></i></button>
        </div>
        <div class="p-3 overflow-auto" style="height:calc(100% - 60px);">
            @php
                $sharedFiles = $conversation->messages->filter(fn($m) => !$m->vue_unique);
            @endphp

            @forelse($sharedFiles as $message)
                @php
                    $files = is_array($message->fichier) ? $message->fichier : json_decode($message->fichier, true) ?? [];
                @endphp
                @foreach($files as $f)
                    @php
                        $ext = pathinfo($f, PATHINFO_EXTENSION);
                        $url = asset('storage/' . $f);
                    @endphp
                    <div class="mb-3">
                        <small class="text-muted">{{ $message->created_at->format('d/m H:i') }}</small><br>
                        @if(in_array($ext, ['jpg','jpeg','png','gif']))
                            <img src="{{ $url }}" alt="media" class="rounded shadow-sm w-100">
                        @elseif($ext === 'mp4')
                            <video controls style="width:100%; border-radius:10px;">
                                <source src="{{ $url }}" type="video/mp4">
                            </video>
                        @elseif($ext === 'pdf')
                            <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-file-earmark-pdf"></i> Voir PDF
                            </a>
                        @endif
                    </div>
                @endforeach
            @empty
                <p class="text-muted">Aucun fichier partagé pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ========= SCRIPTS ========= --}}
<script>
const messagesDiv = document.getElementById('messages');
const fileInput = document.getElementById('fileInput');
const filePreview = document.getElementById('filePreview');
const form = document.getElementById('messageForm');
const sendBtn = document.getElementById('sendBtn');
const input = document.getElementById('messageInput');

let editingMessageId = null;
let vueUniqueActive = false;

// Sidebar toggle
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }

// Scroll auto
messagesDiv.scrollTop = messagesDiv.scrollHeight;

// ===== Envoi / Edition AJAX =====
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(form);
    const url = editingMessageId ? `/messages/${editingMessageId}` : form.action;
    const method = 'POST';
    if(editingMessageId) data.append('_method', 'PUT');

    const res = await fetch(url, {
        method, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: data
    });
    const json = await res.json();

    if(json.status === 'success') {
        if(editingMessageId) {
            document.getElementById(`msg-${editingMessageId}`).outerHTML = json.html;
            editingMessageId = null;
            sendBtn.innerHTML = '<i class="bi bi-send fs-4"></i>';
        } else {
            messagesDiv.insertAdjacentHTML('beforeend', json.message);
        }
        form.reset();
        vueUniqueActive = false;
        document.getElementById('vueUniqueInput').value = 0;
        document.getElementById('uniqueBtn').classList.remove('btn-primary');
        filePreview.innerHTML = '';
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
});

// ===== Vue unique toggle =====
const uniqueBtn = document.getElementById('uniqueBtn');
const vueUniqueInput = document.getElementById('vueUniqueInput');
uniqueBtn.addEventListener('click', () => {
    vueUniqueActive = !vueUniqueActive;
    vueUniqueInput.value = vueUniqueActive ? '1' : '0';
    uniqueBtn.classList.toggle('btn-primary', vueUniqueActive);
    uniqueBtn.classList.toggle('btn-light', !vueUniqueActive);
});

// ===== Suppression AJAX =====
async function confirmDelete(id) {
    if(!confirm("Supprimer ce message ?")) return;
    const res = await fetch(`/messages/${id}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    const json = await res.json();
    if(json.status === 'success') {
        const el = document.getElementById(`msg-${id}`);
        el.style.opacity = '0';
        setTimeout(()=>el.remove(), 300);
    }
}

// ===== Edition =====
function editMessage(id, contenu) {
    input.value = contenu;
    input.focus();
    editingMessageId = id;
    sendBtn.innerHTML = '<i class="bi bi-check-lg fs-4"></i>';
}

// ===== Aperçu fichiers =====
fileInput.addEventListener('change', () => {
    filePreview.innerHTML = '';
    Array.from(fileInput.files).forEach(file => {
        const ext = file.name.split('.').pop().toLowerCase();
        const div = document.createElement('div');
        div.classList.add('position-relative');

        if(['jpg','jpeg','png','gif'].includes(ext)) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '80px'; img.style.height = '80px';
            img.style.objectFit = 'cover';
            img.classList.add('rounded','shadow-sm');
            div.appendChild(img);
        } else if(['mp4','mov'].includes(ext)) {
            const vid = document.createElement('video');
            vid.src = URL.createObjectURL(file);
            vid.style.width = '80px'; vid.style.height = '80px';
            vid.muted = true; vid.loop = true; vid.play();
            vid.classList.add('rounded');
            div.appendChild(vid);
        } else {
            const doc = document.createElement('div');
            doc.classList.add('p-2','border','rounded','bg-light','small');
            doc.textContent = file.name;
            div.appendChild(doc);
        }
        filePreview.appendChild(div);
    });
});

// ===== Marquer messages lus =====
fetch("{{ route('messages.read', $conversation) }}", { method:"POST", headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} });

// Déclenchement automatique de la vue unique pour le destinataire
document.querySelectorAll('.message-bubble.other').forEach(msg => {
    if(msg.dataset.vueUnique === '1') {
        fetch(`/messages/${msg.dataset.id}/viewed`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => msg.remove());
    }
});
</script>

{{-- ========= STYLES ========= --}}
<style>
.message-bubble { max-width: 70%; padding:10px 14px; border-radius:14px; background:white; box-shadow:0 1px 3px rgba(0,0,0,0.1); word-wrap: break-word; animation:fadeIn 0.3s ease-in-out; margin-bottom:6px; position:relative; }
.message-bubble.me { background:#dcf8c6; border-bottom-right-radius:0; margin-left:auto; }
.message-bubble.other { background:#fff; border-bottom-left-radius:0; margin-right:auto; }
.chat-media { max-width: 100%; border-radius:8px; margin-top:4px; }
.sidebar { position:fixed; top:0; right:-100%; width:100%; height:100%; background: rgba(255,255,255,0.6); box-shadow:-3px 0 8px rgba(0,0,0,0.3); transition:right 0.3s ease; z-index:2000; }
.sidebar.active { right:0; }
form input[type="text"] { border-radius:50px; padding:8px 16px; }
#filePreview div { position:relative; display:inline-block; margin:2px; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
#uniqueBtn.btn-primary i { color: white; }
</style>
@endsection
