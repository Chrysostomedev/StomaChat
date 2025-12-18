{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StomaChat</title>
<link rel="icon" href="{{ asset('imagesite/chlogo.png" type="image/png') }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="..." crossorigin="anonymous">

<style>
:root {
  --color-light-bg: linear-gradient(145deg,#f8f9fa,#d5f8f8,#f8f9fa);
  --color-light-text: #191970;
  --color-dark-bg: linear-gradient(145deg,#0b132b,#1a237e,#0b132b);
  --color-dark-text: #00ffff;
}

body {
  background: var(--color-light-bg);
  color: var(--color-light-text);
  transition: background 0.6s,color 0.4s;
  min-height:100vh;
}
body.dark-mode { background: var(--color-dark-bg); color: var(--color-dark-text); }

header, footer { transition: background 0.6s,color 0.4s; }

header {
  position:fixed; top:0; left:0; right:0; z-index:1030;
  padding:.6rem 1rem;
  background: rgba(255,255,255,0.85); backdrop-filter:blur(6px);
  border-bottom:2px solid rgba(0,0,0,.05);
  display:flex; justify-content:space-between; align-items:center;
  flex-wrap:wrap; gap:.5rem;
}
body.dark-mode header { background: rgba(15,20,45,0.85); border-bottom-color: rgba(255,255,255,.05); }

footer {
  position:fixed; bottom:0; left:0; right:0; z-index:1030;
  background: rgba(255,255,255,0.9);
  border-top:2px solid rgba(0,0,0,.05);
  backdrop-filter:blur(6px);
  display:flex; justify-content:space-around; align-items:center;
  padding:.25rem 0;
}
body.dark-mode footer { background: rgba(10,15,40,0.9); border-top-color: rgba(255,255,255,.08); }

main { padding-top:70px; padding-bottom:75px; }

.logo-text {
  font-weight:bold; font-size:1.2rem;
  background: linear-gradient(90deg,#191970,#00bcd4);
  -webkit-background-clip:text; color:transparent;
}

.header-icons {
  display:flex; align-items:center; gap:.75rem; flex-wrap:nowrap;
}
.header-icons a, .header-icons button {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  font-size:.75rem; font-weight:600; color:inherit; text-decoration:none;
  border:none; background:transparent; transition: transform .3s,color .3s;
}
.header-icons a i, .header-icons button i { font-size:1.5rem; transition: transform .3s ease; }
.header-icons a:hover i, .header-icons button:hover i { transform: scale(1.2) rotateY(180deg); }
body.dark-mode .header-icons a:hover, body.dark-mode .header-icons button:hover { color:#00ffff; }

footer .nav-link, footer .friends-icon, footer .forum-icon {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  font-size:.8rem; font-weight:600; color:inherit; text-decoration:none;
  transition: transform .3s,color .3s; position:relative;
}
footer .nav-link i, footer .friends-icon i, footer .forum-icon i { font-size:1.6rem; transition: transform .4s ease; }
footer .nav-link:hover i, footer .friends-icon:hover i, footer .forum-icon:hover i { transform: rotateY(180deg) scale(1.2); }
footer .nav-link.active, footer .friends-icon.active, footer .forum-icon.active { color:#0d6efd; }
body.dark-mode footer .nav-link.active, body.dark-mode footer .friends-icon.active, body.dark-mode footer .forum-icon.active,
body.dark-mode footer .nav-link:hover, body.dark-mode footer .friends-icon:hover, body.dark-mode footer .forum-icon:hover { color:#00ffff; }

.friends-dropdown, .forum-dropdown {
  position:absolute; bottom:60px; left:50%; transform:translateX(-50%) translateY(10px);
  background: rgba(255,255,255,0.95); backdrop-filter:blur(10px);
  border-radius:14px; padding:10px 15px;
  display:flex; gap:15px; opacity:0; pointer-events:none;
  transition: all .25s ease; z-index:200;
}
.friends-dropdown.show, .forum-dropdown.show { opacity:1; transform:translateX(-50%) translateY(-5px); pointer-events:auto; }
body.dark-mode .friends-dropdown, body.dark-mode .forum-dropdown { background: rgba(25,25,60,0.95); }

.friends-icon, .forum-icon { color:#0d6efd; display:flex; flex-direction:column; align-items:center; text-decoration:none; transition: all .25s ease; }
.friends-icon span, .forum-icon span { font-size:.7rem; margin-top:2px; color:#333; }
body.dark-mode .friends-icon span, body.dark-mode .forum-icon span { color:#e0e0e0; }
.friends-icon:hover, .forum-icon:hover { color:#6610f2; transform:scale(1.2); }
</style>
</head>
<body>

<header>
  <div class="d-flex align-items-center gap-2">
    <img src="{{ asset('imagesite/chlogo.png') }}" alt="Logo" style="height:32px;">
    <span class="logo-text">StomaChat</span>
  </div>

  <div class="header-icons">
    <button id="themeToggle"><i class="bi bi-moon fs-5"></i></button>
    <a href="#"><i class="bi bi-gift-fill"></i><span>Dons</span></a>
    {{-- <a href="#"><i class="bi bi-gear"></i><span>Paramètres</span></a> --}}
    <a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i><span>Profil</span></a>
    {{-- <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"><i class="bi bi-box-arrow-right"></i><span>Quitter</span></button>
    </form> --}}
  </div>
</header>

<main class="container-fluid">@yield('content')</main>

<footer>
  <a href="{{ route('publication.create') }}" class="nav-link"><i class="bi bi-image"></i><span>Publier</span></a>

  <div class="nav-item position-relative">
    <a href="#" id="forumToggle" class="nav-link"><i class="bi bi-chat-square-dots"></i><span>Forum</span></a>
    <div class="forum-dropdown" id="forumDropdownMenu">
      <a href="{{ route('forum.index') }}" class="forum-icon"><i class="bi bi-question-circle"></i><span>Questions</span></a>
      <a href="{{ route('sondage.index') }}" class="forum-icon"><i class="bi bi-bar-chart"></i><span>Sondages</span></a>
    </div>
  </div>

  <a href="{{ route('dashboard.index') }}" class="nav-link"><i class="bi bi-house"></i><span>Akwaba</span></a>
  <a href="{{ route('stories.index') }}" class="nav-link"><i class="bi bi-camera"></i><span>Story</span></a>

 
@php 
$authId = Auth::id();

// Demandes d'amis non vues
$friendRequestsCount = \App\Models\Friend::where('receveur_id', $authId)
    ->where('statut', 'en_attente')
    ->count();

// Messages non lus (utiliser la même logique que friends())
$unreadMessagesCount = 0;
$auth = \App\Models\User::find($authId);
foreach($auth->friends()->get() as $friend) {
    $conv = $auth->conversationWith($friend);
    if($conv) {
        $unreadMessagesCount += $conv->messages()
            ->where('expediteur_id', $friend->id)
            ->where('lu', false)
            ->count();
    }
}

// Total notifications
$totalNotifications = $friendRequestsCount + $unreadMessagesCount;

@endphp

<div class="nav-item position-relative">
    <a href="#" id="friendsToggle" class="nav-link position-relative">
        <i class="bi bi-people-fill fs-4"></i>
        <span>Amis</span>
        @if($totalNotifications > 0)
            <span class="badge bg-danger rounded-circle position-absolute top-0 start-100 translate-middle" style="font-size:0.75rem;">
                {{ $totalNotifications }}
            </span>
        @endif
    </a>

    <div class="friends-dropdown" id="friendsDropdown">
        <a href="{{ route('friends.index') }}" class="friends-icon">
            <i class="bi bi-person-heart"></i><span>Amis</span>
            @if($unreadMessagesCount > 0)
                <span class="badge bg-danger rounded-pill ms-1">{{ $unreadMessagesCount }}</span>
            @endif
        </a>
        <a href="{{ route('friends.demandes') }}" class="friends-icon">
            <i class="bi bi-envelope-paper"></i><span>Demandes</span>
            @if($friendRequestsCount > 0)
                <span class="badge bg-danger rounded-pill ms-1">{{ $friendRequestsCount }}</span>
            @endif
        </a>
    </div>
</div>


  
</footer>
@stack('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Dark mode toggle
const themeToggle = document.getElementById('themeToggle');
const themeIcon = themeToggle.querySelector('i');
if(localStorage.getItem('theme')==='dark') document.body.classList.add('dark-mode');
themeToggle.addEventListener('click', ()=>{
    document.body.classList.toggle('dark-mode');
    const isDark=document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark?'dark':'light');
    themeIcon.classList.toggle('bi-moon',!isDark);
    themeIcon.classList.toggle('bi-sun',isDark);
});

// Dropdowns
function setupDropdown(toggleId,menuId){
  const toggle=document.getElementById(toggleId);
  const menu=document.getElementById(menuId);
  if(toggle && menu){
    toggle.addEventListener('click', e=>{
      e.preventDefault();
      menu.classList.toggle('show');
    });
    document.addEventListener('click', e=>{
      if(!toggle.contains(e.target) && !menu.contains(e.target)) menu.classList.remove('show');
    });
  }
}
setupDropdown('friendsToggle','friendsDropdown');
setupDropdown('forumToggle','forumDropdownMenu');
</script>

</body>
</html>
